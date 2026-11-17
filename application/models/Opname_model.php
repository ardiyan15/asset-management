<?php

/**
 * Stock opname (audit fisik aset per ruangan).
 *
 * stock_opname.status         : 0 = sedang berjalan, 1 = selesai
 * stock_opname_details.result : 0 = belum/tidak ditemukan, 1 = ditemukan, 2 = salah ruangan
 */
class Opname_model extends CI_Model
{
    const STATUS_OPEN     = 0;
    const STATUS_FINISHED = 1;

    const RESULT_UNCHECKED  = 0;
    const RESULT_FOUND      = 1;
    const RESULT_WRONG_ROOM = 2;

    /**
     * Kode opname: OPN/YYYY/MM/000001 (running number per bulan).
     */
    public function generate_code()
    {
        $prefix = 'OPN/' . date('Y') . '/' . date('m') . '/';

        $this->db->select('code');
        $this->db->from('stock_opname');
        $this->db->like('code', $prefix, 'after');
        $this->db->order_by('code', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get()->row_array();

        $next = $last ? ((int) substr($last['code'], -6)) + 1 : 1;
        return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Ruangan + lantai + gedung, dipakai untuk cek hak akses per gedung.
     */
    public function get_room_location($room_id)
    {
        $this->db->select('rooms.id, rooms.name, rooms.description, floors.name AS floor_name,
            buildings.id AS building_id, buildings.name AS building_name');
        $this->db->from('rooms');
        $this->db->join('floors', 'floors.id = rooms.floor_id');
        $this->db->join('buildings', 'buildings.id = floors.building_id');
        $this->db->where(['rooms.id' => $room_id, 'rooms.status' => 1]);
        return $this->db->get()->row_array();
    }

    /**
     * Ruangan yang boleh diaudit user (admin: semua, user: gedungnya saja).
     */
    public function get_rooms_for_user($role_id, $building_id)
    {
        $this->db->select('rooms.id, rooms.name, rooms.description, floors.name AS floor_name, buildings.name AS building_name');
        $this->db->from('rooms');
        $this->db->join('floors', 'floors.id = rooms.floor_id');
        $this->db->join('buildings', 'buildings.id = floors.building_id');
        $this->db->where('rooms.status', 1);
        if ($role_id !== '1') {
            $this->db->where('buildings.id', $building_id);
        }
        $this->db->order_by('buildings.name', 'ASC');
        $this->db->order_by('rooms.name', 'ASC');
        return $this->db->get()->result_array();
    }

    private function _header_query()
    {
        $this->db->select('stock_opname.*, rooms.name AS room_name, rooms.description AS room_description,
            floors.name AS floor_name, buildings.id AS building_id, buildings.name AS building_name,
            user.username,
            SUM(CASE WHEN d.result <> 2 THEN 1 ELSE 0 END) AS total_expected,
            SUM(CASE WHEN d.result = 1 THEN 1 ELSE 0 END) AS total_found,
            SUM(CASE WHEN d.result = 0 THEN 1 ELSE 0 END) AS total_missing,
            SUM(CASE WHEN d.result = 2 THEN 1 ELSE 0 END) AS total_wrong_room', false);
        $this->db->from('stock_opname');
        $this->db->join('rooms', 'rooms.id = stock_opname.room_id');
        $this->db->join('floors', 'floors.id = rooms.floor_id');
        $this->db->join('buildings', 'buildings.id = floors.building_id');
        $this->db->join('user', 'user.id_user = stock_opname.user_id', 'left');
        $this->db->join('stock_opname_details AS d', 'd.stock_opname_id = stock_opname.id', 'left');
        $this->db->group_by('stock_opname.id');
    }

    public function get_list($role_id, $building_id)
    {
        $this->_header_query();
        if ($role_id !== '1') {
            $this->db->where('buildings.id', $building_id);
        }
        $this->db->order_by('stock_opname.id', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_header($id)
    {
        $this->_header_query();
        $this->db->where('stock_opname.id', $id);
        return $this->db->get()->row_array();
    }

    public function get_open_by_room($room_id)
    {
        return $this->db->get_where('stock_opname', [
            'room_id' => $room_id,
            'status'  => self::STATUS_OPEN,
        ])->row_array();
    }

    /**
     * Detail opname beserta data aset dan ruangan tercatat.
     */
    public function get_details($opname_id)
    {
        $this->db->select('d.id, d.asset_id, d.expected_room_id, d.result, d.checked_at,
            asset.asset_name, asset.merk, asset.serial_number, asset.asset_condition, rooms.name AS expected_room_name,
            (SELECT al.borrower_name FROM asset_loans al WHERE al.asset_id = d.asset_id AND al.status = 0 LIMIT 1) AS on_loan_to', false);
        $this->db->from('stock_opname_details AS d');
        $this->db->join('asset', 'asset.id_asset = d.asset_id', 'left');
        $this->db->join('rooms', 'rooms.id = d.expected_room_id', 'left');
        $this->db->where('d.stock_opname_id', $opname_id);
        $this->db->order_by('asset.asset_name', 'ASC');
        $this->db->order_by('asset.serial_number', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Buat sesi opname: header + snapshot semua aset yang tercatat ada di ruangan
     * (placement_status = 1; aset yang sedang dipindahkan tidak ikut dihitung).
     *
     * @return int|false id opname
     */
    public function create($room_id, $user_id, $notes = null)
    {
        $now = date('Y-m-d H:i:s');

        $this->db->trans_begin();

        $this->db->insert('stock_opname', [
            'code'       => $this->generate_code(),
            'room_id'    => $room_id,
            'user_id'    => $user_id,
            'status'     => self::STATUS_OPEN,
            'notes'      => $notes !== '' ? $notes : null,
            'started_at' => $now,
            'created_at' => $now,
        ]);
        $opname_id = $this->db->insert_id();

        $assets = $this->db->select('id_asset')
            ->get_where('asset', ['room_id' => $room_id, 'placement_status' => 1, 'is_disposed' => 0])
            ->result_array();

        if (!empty($assets)) {
            $rows = [];
            foreach ($assets as $asset) {
                $rows[] = [
                    'stock_opname_id'  => $opname_id,
                    'asset_id'         => $asset['id_asset'],
                    'expected_room_id' => $room_id,
                    'result'           => self::RESULT_UNCHECKED,
                    'created_at'       => $now,
                ];
            }
            $this->db->insert_batch('stock_opname_details', $rows);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();
        return (int) $opname_id;
    }

    /**
     * Proses satu hasil scan nomor seri.
     *
     * @return array ['status' => found|already|wrong_room|not_found, 'asset' => ?, 'detail_id' => ?]
     */
    public function check_serial(array $opname, $serial_number)
    {
        $asset = $this->db->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number,
                asset.asset_condition, asset.is_disposed, asset.room_id, rooms.name AS room_name')
            ->from('asset')
            ->join('rooms', 'rooms.id = asset.room_id', 'left')
            ->where('asset.serial_number', $serial_number)
            ->limit(1)
            ->get()->row_array();

        if (!$asset) {
            return ['status' => 'not_found', 'asset' => null, 'detail_id' => null];
        }
        if ((int) $asset['is_disposed'] === 1) {
            return ['status' => 'disposed', 'asset' => $asset, 'detail_id' => null];
        }

        $now    = date('Y-m-d H:i:s');
        $detail = $this->db->get_where('stock_opname_details', [
            'stock_opname_id' => $opname['id'],
            'asset_id'        => $asset['id_asset'],
        ])->row_array();

        if ($detail) {
            if ((int) $detail['result'] !== self::RESULT_UNCHECKED) {
                return ['status' => 'already', 'asset' => $asset, 'detail_id' => (int) $detail['id'], 'result' => (int) $detail['result']];
            }
            $this->db->where('id', $detail['id'])->update('stock_opname_details', [
                'result'     => self::RESULT_FOUND,
                'checked_at' => $now,
                'updated_at' => $now,
            ]);
            return ['status' => 'found', 'asset' => $asset, 'detail_id' => (int) $detail['id']];
        }

        // Aset ada di sistem tapi tidak tercatat di ruangan ini → salah ruangan
        $this->db->insert('stock_opname_details', [
            'stock_opname_id'  => $opname['id'],
            'asset_id'         => $asset['id_asset'],
            'expected_room_id' => $asset['room_id'],
            'result'           => self::RESULT_WRONG_ROOM,
            'checked_at'       => $now,
            'created_at'       => $now,
        ]);
        return ['status' => 'wrong_room', 'asset' => $asset, 'detail_id' => (int) $this->db->insert_id()];
    }

    /**
     * Centang / batal centang manual. Untuk baris "salah ruangan", batal centang = hapus baris.
     */
    public function set_found($opname_id, $detail_id, $found)
    {
        $detail = $this->db->get_where('stock_opname_details', [
            'id'              => $detail_id,
            'stock_opname_id' => $opname_id,
        ])->row_array();

        if (!$detail) {
            return false;
        }

        $now = date('Y-m-d H:i:s');

        if ((int) $detail['result'] === self::RESULT_WRONG_ROOM) {
            if ($found) {
                return true;
            }
            $this->db->delete('stock_opname_details', ['id' => $detail_id]);
            return $this->db->affected_rows() > 0;
        }

        $this->db->where('id', $detail_id)->update('stock_opname_details', [
            'result'     => $found ? self::RESULT_FOUND : self::RESULT_UNCHECKED,
            'checked_at' => $found ? $now : null,
            'updated_at' => $now,
        ]);
        return true;
    }

    public function get_detail($opname_id, $detail_id)
    {
        return $this->db->get_where('stock_opname_details', [
            'id'              => $detail_id,
            'stock_opname_id' => $opname_id,
        ])->row_array();
    }

    public function finish($opname_id)
    {
        $now = date('Y-m-d H:i:s');
        $this->db->where(['id' => $opname_id, 'status' => self::STATUS_OPEN]);
        $this->db->update('stock_opname', [
            'status'      => self::STATUS_FINISHED,
            'finished_at' => $now,
            'updated_at'  => $now,
        ]);
        return $this->db->affected_rows();
    }

    /**
     * Batalkan opname yang masih berjalan (detail ikut terhapus via FK cascade).
     */
    public function cancel($opname_id)
    {
        $this->db->delete('stock_opname', ['id' => $opname_id, 'status' => self::STATUS_OPEN]);
        return $this->db->affected_rows();
    }
}
