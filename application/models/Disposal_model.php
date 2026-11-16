<?php

/**
 * Penghapusan (disposal) aset.
 *
 * Alur: user mengajukan aset kondisi Rusak Berat → admin menyetujui / menolak per aset →
 * aset yang disetujui ditandai asset.is_disposed = 1 (data tetap ada untuk riwayat & BAST).
 *
 * asset_disposal_details.status: 0 = diajukan, 1 = disetujui, 2 = ditolak
 */
class Disposal_model extends CI_Model
{
    const STATUS_PENDING  = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;

    /** Kondisi minimal yang boleh diajukan (3 = Rusak Berat). */
    const ELIGIBLE_CONDITION = 3;

    public static function methods()
    {
        return [
            'musnah'  => 'Dimusnahkan',
            'jual'    => 'Dijual / dilelang',
            'hibah'   => 'Dihibahkan',
            'lainnya' => 'Lainnya',
        ];
    }

    public static function method_label($method)
    {
        return self::methods()[$method] ?? $method;
    }

    /**
     * Kode pengajuan: DSP/YYYY/MM/000001 (running number per bulan).
     */
    public function generate_code()
    {
        $prefix = 'DSP/' . date('Y') . '/' . date('m') . '/';

        $last = $this->db->select('code')->from('asset_disposals')
            ->like('code', $prefix, 'after')
            ->order_by('code', 'DESC')->limit(1)
            ->get()->row_array();

        $next = $last ? ((int) substr($last['code'], -6)) + 1 : 1;
        return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Aset yang boleh diajukan: Rusak Berat, belum dihapus, ada di ruangan (tidak sedang
     * dipindahkan), belum punya pengajuan yang masih menunggu, dan (non-admin) di gedung user.
     */
    public function get_eligible_assets($role_id, $building_id, array $asset_ids = [])
    {
        $this->db->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number,
            asset.asset_condition, asset.room_id, rooms.name AS room_name, floors.name AS floor_name,
            buildings.name AS building_name');
        $this->db->from('asset');
        $this->db->join('rooms', 'rooms.id = asset.room_id');
        $this->db->join('floors', 'floors.id = rooms.floor_id');
        $this->db->join('buildings', 'buildings.id = floors.building_id');
        $this->db->where([
            'asset.asset_condition'  => self::ELIGIBLE_CONDITION,
            'asset.is_disposed'      => 0,
            'asset.placement_status' => 1,
        ]);
        $this->db->where('NOT EXISTS (SELECT 1 FROM asset_disposal_details p WHERE p.asset_id = asset.id_asset AND p.status = ' . self::STATUS_PENDING . ')', null, false);

        if ($role_id !== '1') {
            $this->db->where('buildings.id', $building_id);
        }
        if (!empty($asset_ids)) {
            $this->db->where_in('asset.id_asset', $asset_ids);
        }

        $this->db->order_by('buildings.name', 'ASC');
        $this->db->order_by('rooms.name', 'ASC');
        $this->db->order_by('asset.asset_name', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Buat pengajuan untuk aset-aset yang memenuhi syarat.
     * @return int|false id pengajuan
     */
    public function create(array $assets, $method, $reason, $user_id)
    {
        if (empty($assets)) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $this->db->trans_begin();

        $this->db->insert('asset_disposals', [
            'code'         => $this->generate_code(),
            'method'       => $method,
            'reason'       => $reason,
            'requested_by' => $user_id,
            'created_at'   => $now,
        ]);
        $disposal_id = $this->db->insert_id();

        $rows = [];
        foreach ($assets as $asset) {
            $rows[] = [
                'disposal_id'          => $disposal_id,
                'asset_id'             => $asset['id_asset'],
                'room_id'              => $asset['room_id'],
                'condition_at_request' => $asset['asset_condition'],
                'status'               => self::STATUS_PENDING,
                'created_at'           => $now,
            ];
        }
        $this->db->insert_batch('asset_disposal_details', $rows);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return (int) $disposal_id;
    }

    private function _header_query()
    {
        $this->db->select('asset_disposals.*, requester.username AS requested_by_name,
            COUNT(d.id) AS total_items,
            SUM(CASE WHEN d.status = 0 THEN 1 ELSE 0 END) AS total_pending,
            SUM(CASE WHEN d.status = 1 THEN 1 ELSE 0 END) AS total_approved,
            SUM(CASE WHEN d.status = 2 THEN 1 ELSE 0 END) AS total_rejected,
            GROUP_CONCAT(DISTINCT floors.building_id) AS building_ids,
            GROUP_CONCAT(DISTINCT buildings.name ORDER BY buildings.name SEPARATOR ", ") AS building_names', false);
        $this->db->from('asset_disposals');
        $this->db->join('user AS requester', 'requester.id_user = asset_disposals.requested_by', 'left');
        $this->db->join('asset_disposal_details AS d', 'd.disposal_id = asset_disposals.id');
        $this->db->join('rooms', 'rooms.id = d.room_id', 'left');
        $this->db->join('floors', 'floors.id = rooms.floor_id', 'left');
        $this->db->join('buildings', 'buildings.id = floors.building_id', 'left');
        $this->db->group_by('asset_disposals.id');
    }

    /**
     * Daftar pengajuan. Non-admin hanya melihat pengajuan yang berisi aset di gedungnya.
     */
    public function get_list($role_id, $building_id)
    {
        $this->_header_query();
        if ($role_id !== '1') {
            $this->db->where('floors.building_id', $building_id);
        }
        $this->db->order_by('asset_disposals.id', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_header($id)
    {
        $this->_header_query();
        $this->db->where('asset_disposals.id', $id);
        return $this->db->get()->row_array();
    }

    /**
     * Detail aset dalam satu pengajuan. $building_id diisi untuk membatasi ke satu gedung (non-admin).
     */
    public function get_details($disposal_id, $building_id = null, $status = null)
    {
        $this->db->select('d.*, asset.asset_name, asset.merk, asset.serial_number, asset.asset_condition,
            asset.is_disposed, rooms.name AS room_name, floors.name AS floor_name, buildings.name AS building_name,
            reviewer.username AS reviewed_by_name');
        $this->db->from('asset_disposal_details AS d');
        $this->db->join('asset', 'asset.id_asset = d.asset_id', 'left');
        $this->db->join('rooms', 'rooms.id = d.room_id', 'left');
        $this->db->join('floors', 'floors.id = rooms.floor_id', 'left');
        $this->db->join('buildings', 'buildings.id = floors.building_id', 'left');
        $this->db->join('user AS reviewer', 'reviewer.id_user = d.reviewed_by', 'left');
        $this->db->where('d.disposal_id', $disposal_id);
        if ($building_id !== null) {
            $this->db->where('floors.building_id', $building_id);
        }
        if ($status !== null) {
            $this->db->where('d.status', $status);
        }
        $this->db->order_by('d.id', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Setujui / tolak detail yang masih menunggu. Persetujuan menandai aset sudah dihapus.
     *
     * @param int[] $detail_ids  kosong = semua detail yang menunggu di pengajuan ini
     * @return int|false jumlah detail yang diproses
     */
    public function review($disposal_id, array $detail_ids, $approve, $notes, $admin_id)
    {
        $this->db->where(['disposal_id' => $disposal_id, 'status' => self::STATUS_PENDING]);
        if (!empty($detail_ids)) {
            $this->db->where_in('id', $detail_ids);
        }
        $details = $this->db->get('asset_disposal_details')->result_array();

        if (empty($details)) {
            return 0;
        }

        $now   = date('Y-m-d H:i:s');
        $notes = trim((string) $notes);

        $this->db->trans_begin();

        foreach ($details as $detail) {
            $this->db->where('id', $detail['id'])->update('asset_disposal_details', [
                'status'       => $approve ? self::STATUS_APPROVED : self::STATUS_REJECTED,
                'reviewed_by'  => $admin_id,
                'reviewed_at'  => $now,
                'review_notes' => $notes !== '' ? mb_substr($notes, 0, 255) : null,
                'updated_at'   => $now,
            ]);

            if ($approve) {
                $this->db->where('id_asset', $detail['asset_id'])->update('asset', [
                    'is_disposed' => 1,
                    'disposed_at' => $now,
                ]);
            }
        }

        $this->db->where('id', $disposal_id)->update('asset_disposals', ['updated_at' => $now]);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return count($details);
    }

    /** Jumlah aset yang menunggu persetujuan (badge sidebar admin). */
    public function count_pending()
    {
        return $this->db->where('status', self::STATUS_PENDING)->count_all_results('asset_disposal_details');
    }

    /**
     * Status penghapusan terakhir satu aset (untuk halaman scan): null kalau belum pernah diajukan.
     */
    public function get_latest_for_asset($asset_id)
    {
        $this->db->select('d.id, d.disposal_id, d.status, d.reviewed_at, d.review_notes, asset_disposals.code, asset_disposals.method');
        $this->db->from('asset_disposal_details AS d');
        $this->db->join('asset_disposals', 'asset_disposals.id = d.disposal_id');
        $this->db->where('d.asset_id', $asset_id);
        $this->db->order_by('d.id', 'DESC');
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }
}
