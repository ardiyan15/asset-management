<?php

/**
 * Peminjaman aset sementara. Lokasi aset (asset.room_id) tidak berubah selama dipinjam.
 * status: 0 = sedang dipinjam, 1 = sudah dikembalikan. Terlambat = status 0 dan due_date < hari ini.
 */
class Loan_model extends CI_Model
{
    const STATUS_OPEN     = 0;
    const STATUS_RETURNED = 1;

    /** Subquery dipakai modul lain: aset yang sedang dipinjam tidak boleh dipindah / dihapus. */
    const OPEN_LOAN_EXISTS = 'EXISTS (SELECT 1 FROM asset_loans al WHERE al.asset_id = asset.id_asset AND al.status = 0)';

    public function generate_code()
    {
        $prefix = 'PJM/' . date('Y') . '/' . date('m') . '/';
        $last = $this->db->select('code')->from('asset_loans')
            ->like('code', $prefix, 'after')->order_by('code', 'DESC')->limit(1)
            ->get()->row_array();
        $next = $last ? ((int) substr($last['code'], -6)) + 1 : 1;
        return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    private function _join_location()
    {
        $this->db->join('rooms', 'rooms.id = asset.room_id');
        $this->db->join('floors', 'floors.id = rooms.floor_id');
        $this->db->join('buildings', 'buildings.id = floors.building_id');
    }

    private function _scope($role_id, $building_id, $filter_building = 0)
    {
        if ($role_id !== '1') {
            $this->db->where('buildings.id', $building_id);
        } elseif ($filter_building) {
            $this->db->where('buildings.id', (int) $filter_building);
        }
    }

    private function _loan_select()
    {
        $this->db->select('l.*, asset.asset_name, asset.merk, asset.serial_number, asset.asset_condition,
            rooms.name AS room_name, floors.name AS floor_name, buildings.id AS building_id, buildings.name AS building_name,
            creator.username AS created_by_name, returner.username AS returned_by_name');
        $this->db->from('asset_loans AS l');
        $this->db->join('asset', 'asset.id_asset = l.asset_id');
        $this->_join_location();
        $this->db->join('user AS creator', 'creator.id_user = l.created_by', 'left');
        $this->db->join('user AS returner', 'returner.id_user = l.returned_by', 'left');
    }

    /**
     * $status: open (sedang dipinjam), overdue, returned, all
     */
    public function get_loans($role_id, $building_id, $status = 'open', $filter_building = 0)
    {
        $this->_loan_select();
        $this->_scope($role_id, $building_id, $filter_building);

        switch ($status) {
            case 'open':
                $this->db->where('l.status', self::STATUS_OPEN);
                $this->db->order_by('l.due_date', 'ASC');
                break;
            case 'overdue':
                $this->db->where('l.status', self::STATUS_OPEN)->where('l.due_date <', date('Y-m-d'));
                $this->db->order_by('l.due_date', 'ASC');
                break;
            case 'returned':
                $this->db->where('l.status', self::STATUS_RETURNED);
                $this->db->order_by('l.returned_at', 'DESC');
                break;
            default:
                $this->db->order_by('l.status', 'ASC');
                $this->db->order_by('l.id', 'DESC');
        }
        return $this->db->get()->result_array();
    }

    public function count_by_status($role_id, $building_id, $filter_building = 0)
    {
        $today = $this->db->escape(date('Y-m-d'));
        $month = $this->db->escape(date('Y-m'));
        $this->db->select("SUM(l.status = 0) AS open,
            SUM(l.status = 0 AND l.due_date < $today) AS overdue,
            SUM(l.status = 0 AND l.due_date = $today) AS due_today,
            SUM(l.status = 1) AS returned,
            SUM(l.status = 1 AND DATE_FORMAT(l.returned_at, '%Y-%m') = $month) AS returned_this_month,
            COUNT(*) AS total", false);
        $this->db->from('asset_loans AS l');
        $this->db->join('asset', 'asset.id_asset = l.asset_id');
        $this->_join_location();
        $this->_scope($role_id, $building_id, $filter_building);
        return array_map('intval', $this->db->get()->row_array() ?: []);
    }

    /** Badge sidebar: peminjaman yang terlambat atau harus kembali hari ini. */
    public function count_attention($role_id, $building_id)
    {
        $c = $this->count_by_status($role_id, $building_id);
        return ($c['overdue'] ?? 0) + ($c['due_today'] ?? 0);
    }

    public function get_loan($id)
    {
        $this->_loan_select();
        $this->db->where('l.id', $id);
        return $this->db->get()->row_array();
    }

    /**
     * Aset yang bisa dipinjamkan: aktif, ada di ruangan (tidak sedang dipindah),
     * tidak sedang dipinjam, tidak sedang diajukan penghapusan, dan (non-admin) di gedung user.
     */
    public function available_assets($role_id, $building_id, $asset_id = null)
    {
        $this->db->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number, asset.asset_condition,
            rooms.name AS room_name, buildings.id AS building_id, buildings.name AS building_name');
        $this->db->from('asset');
        $this->_join_location();
        $this->db->where(['asset.is_disposed' => 0, 'asset.placement_status' => 1]);
        $this->db->where('NOT ' . self::OPEN_LOAN_EXISTS, null, false);
        $this->db->where('NOT EXISTS (SELECT 1 FROM asset_disposal_details p WHERE p.asset_id = asset.id_asset AND p.status = 0)', null, false);
        $this->_scope($role_id, $building_id);
        if ($asset_id !== null) {
            $this->db->where('asset.id_asset', (int) $asset_id);
            return $this->db->get()->row_array();
        }
        $this->db->order_by('buildings.name', 'ASC');
        $this->db->order_by('asset.asset_name', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * @return int|false id peminjaman
     */
    public function create(array $asset, array $input, $user_id)
    {
        $now = date('Y-m-d H:i:s');
        $this->db->trans_begin();

        // Cek ulang di dalam transaksi supaya aset yang sama tidak dipinjamkan dua kali
        $open = $this->db->query('SELECT id FROM asset_loans WHERE asset_id = ? AND status = 0 FOR UPDATE', [$asset['id_asset']])->row_array();
        if ($open) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->insert('asset_loans', array_merge($input, [
            'code'          => $this->generate_code(),
            'asset_id'      => $asset['id_asset'],
            'condition_out' => (int) $asset['asset_condition'],
            'status'        => self::STATUS_OPEN,
            'created_by'    => $user_id,
            'created_at'    => $now,
        ]));
        $id = (int) $this->db->insert_id();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return $id;
    }

    /**
     * Tandai dikembalikan. Kalau kondisi saat kembali berbeda, kondisi aset ikut diperbarui
     * (asset_condition_logs source 'loan').
     */
    public function mark_returned(array $loan, $returned_at, $condition_in, $notes, $user_id)
    {
        $this->load->model('Assets_model', 'AssetsForLoan');
        $notes = trim((string) $notes);

        $this->db->trans_begin();

        $this->db->where(['id' => $loan['id'], 'status' => self::STATUS_OPEN])->update('asset_loans', [
            'status'       => self::STATUS_RETURNED,
            'returned_at'  => $returned_at,
            'condition_in' => $condition_in,
            'return_notes' => $notes !== '' ? mb_substr($notes, 0, 255) : null,
            'returned_by'  => $user_id,
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);
        if ($this->db->affected_rows() !== 1) {
            $this->db->trans_rollback();
            return false;
        }

        $status = $this->AssetsForLoan->update_condition(
            $loan['asset_id'],
            $condition_in,
            'Dikembalikan ' . $loan['code'] . ' oleh ' . $loan['borrower_name'] . ($notes !== '' ? ': ' . $notes : ''),
            $user_id,
            'loan'
        );
        if ($status === 'failed') {
            $this->db->trans_rollback();
            return false;
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return true;
    }

    public function extend(array $loan, $new_due_date)
    {
        $this->db->where(['id' => $loan['id'], 'status' => self::STATUS_OPEN])->update('asset_loans', [
            'due_date'         => $new_due_date,
            'extended_count'   => (int) $loan['extended_count'] + 1,
            'last_notified_at' => null,
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);
        return $this->db->affected_rows() === 1;
    }

    // ------------------------------------------------------------------
    // Halaman scan & notifikasi
    // ------------------------------------------------------------------

    public function open_loan_for_asset($asset_id)
    {
        return $this->db->get_where('asset_loans', ['asset_id' => $asset_id, 'status' => self::STATUS_OPEN])->row_array();
    }

    public function history_for_asset($asset_id, $limit = 5)
    {
        return $this->db->order_by('id', 'DESC')->limit($limit)
            ->get_where('asset_loans', ['asset_id' => $asset_id])->result_array();
    }

    /** Peminjaman terlambat yang belum diingatkan hari ini. */
    public function overdue_for_notification()
    {
        $this->_loan_select();
        $this->db->where('l.status', self::STATUS_OPEN);
        $this->db->where('l.due_date <', date('Y-m-d'));
        $this->db->group_start();
        $this->db->where('l.last_notified_at IS NULL', null, false);
        $this->db->or_where('DATE(l.last_notified_at) <', date('Y-m-d'));
        $this->db->group_end();
        $this->db->order_by('l.due_date', 'ASC');
        return $this->db->get()->result_array();
    }

    public function mark_notified(array $ids)
    {
        if (!empty($ids)) {
            $this->db->where_in('id', $ids)->update('asset_loans', ['last_notified_at' => date('Y-m-d H:i:s')]);
        }
    }
}
