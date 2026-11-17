<?php

/**
 * Data laporan rekap aset & statistik dashboard.
 * Non-admin selalu dibatasi ke gedungnya sendiri.
 */
class Recap_model extends CI_Model
{
    /**
     * Daftar aset sesuai filter, lengkap dengan kategori, lokasi, dan data pembelian.
     *
     * $filters: building_id, category_id ('none' = tanpa kategori), condition, status (active|disposed|all)
     */
    public function get_assets($role_id, $building_id, array $filters = [])
    {
        $this->db->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number, asset.asset_condition,
            asset.placement_status, asset.is_disposed, asset.disposed_at, asset.created,
            asset.purchase_date, asset.purchase_price, asset.vendor,
            categories.id AS category_id, categories.name AS category_name,
            categories.useful_life_years, categories.residual_percent,
            rooms.id AS room_id, rooms.name AS room_name, floors.name AS floor_name,
            buildings.id AS building_id, buildings.name AS building_name');
        $this->db->from('asset');
        $this->db->join('categories', 'categories.id = asset.category_id', 'left');
        $this->db->join('rooms', 'rooms.id = asset.room_id');
        $this->db->join('floors', 'floors.id = rooms.floor_id');
        $this->db->join('buildings', 'buildings.id = floors.building_id');

        $status = $filters['status'] ?? 'active';
        if ($status === 'active') {
            $this->db->where('asset.is_disposed', 0);
        } elseif ($status === 'disposed') {
            $this->db->where('asset.is_disposed', 1);
        }

        if ($role_id !== '1') {
            $this->db->where('buildings.id', $building_id);
        } elseif (!empty($filters['building_id'])) {
            $this->db->where('buildings.id', (int) $filters['building_id']);
        }

        if (!empty($filters['category_id'])) {
            if ($filters['category_id'] === 'none') {
                $this->db->where('asset.category_id IS NULL', null, false);
            } else {
                $this->db->where('asset.category_id', (int) $filters['category_id']);
            }
        }

        if (!empty($filters['condition'])) {
            $this->db->where('asset.asset_condition', (int) $filters['condition']);
        }

        $this->db->order_by('buildings.name', 'ASC');
        $this->db->order_by('rooms.name', 'ASC');
        $this->db->order_by('asset.asset_name', 'ASC');
        return $this->db->get()->result_array();
    }

    // ------------------------------------------------------------------
    // Dashboard
    // ------------------------------------------------------------------

    /**
     * Jumlah aset aktif per kondisi, dikelompokkan per gedung (admin) atau per ruangan (user).
     * @return array [['label' => .., 1 => n, 2 => n, 3 => n], ...]
     */
    public function condition_by_location($role_id, $building_id)
    {
        $by_room = $role_id !== '1';
        $label   = $by_room ? 'rooms.name' : 'buildings.name';
        $group   = $by_room ? 'rooms.id' : 'buildings.id';

        $this->db->select($label . ' AS label, asset.asset_condition, COUNT(*) AS total', false);
        $this->db->from('asset');
        $this->db->join('rooms', 'rooms.id = asset.room_id');
        $this->db->join('floors', 'floors.id = rooms.floor_id');
        $this->db->join('buildings', 'buildings.id = floors.building_id');
        $this->db->where('asset.is_disposed', 0);
        if ($by_room) {
            $this->db->where('buildings.id', $building_id);
        }
        $this->db->group_by([$group, 'asset.asset_condition']);
        $this->db->order_by($label, 'ASC');

        $result = [];
        foreach ($this->db->get()->result_array() as $row) {
            $key = $row['label'];
            if (!isset($result[$key])) {
                $result[$key] = ['label' => $key, 1 => 0, 2 => 0, 3 => 0];
            }
            $result[$key][(int) $row['asset_condition']] = (int) $row['total'];
        }
        return array_values($result);
    }

    /**
     * Jumlah transaksi pindah aset per bulan (N bulan terakhir, termasuk bulan ini) per status.
     * Non-admin: transaksi yang asal atau tujuannya di gedung user.
     * @return array ['labels' => ['Des 2025', ...], 'months' => ['2025-12', ...], 0 => [..], 1 => [..], 2 => [..]]
     */
    public function transactions_per_month($role_id, $building_id, $months = 12)
    {
        $start = (new DateTime('first day of this month'))->modify('-' . ($months - 1) . ' months');

        $this->db->select("DATE_FORMAT(td.sent, '%Y-%m') AS ym, td.status, COUNT(*) AS total", false);
        $this->db->from('transaction_details AS td');
        $this->db->where('td.sent >=', $start->format('Y-m-d'));
        if ($role_id !== '1') {
            $this->db->join('rooms AS src', 'src.id = td.source_id', 'left');
            $this->db->join('floors AS src_f', 'src_f.id = src.floor_id', 'left');
            $this->db->join('rooms AS dst', 'dst.id = td.room_id', 'left');
            $this->db->join('floors AS dst_f', 'dst_f.id = dst.floor_id', 'left');
            $this->db->group_start();
            $this->db->where('src_f.building_id', $building_id);
            $this->db->or_where('dst_f.building_id', $building_id);
            $this->db->group_end();
        }
        $this->db->group_by(['ym', 'td.status']);
        $rows = $this->db->get()->result_array();

        $month_names = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $result      = ['labels' => [], 'months' => [], 0 => [], 1 => [], 2 => []];
        $index       = [];
        $cursor      = clone $start;
        for ($i = 0; $i < $months; $i++) {
            $ym                  = $cursor->format('Y-m');
            $index[$ym]          = $i;
            $result['months'][]  = $ym;
            $result['labels'][]  = $month_names[(int) $cursor->format('n')] . ' ' . $cursor->format('Y');
            $result[0][]         = 0;
            $result[1][]         = 0;
            $result[2][]         = 0;
            $cursor->modify('+1 month');
        }

        foreach ($rows as $row) {
            if (isset($index[$row['ym']]) && isset($result[(int) $row['status']])) {
                $result[(int) $row['status']][$index[$row['ym']]] = (int) $row['total'];
            }
        }
        return $result;
    }

    /**
     * Jumlah aset aktif per kategori (untuk doughnut dashboard).
     */
    public function count_by_category($role_id, $building_id)
    {
        $this->db->select("COALESCE(categories.name, 'Tanpa kategori') AS label, COUNT(*) AS total", false);
        $this->db->from('asset');
        $this->db->join('categories', 'categories.id = asset.category_id', 'left');
        $this->db->where('asset.is_disposed', 0);
        if ($role_id !== '1') {
            $this->db->join('rooms', 'rooms.id = asset.room_id');
            $this->db->join('floors', 'floors.id = rooms.floor_id');
            $this->db->where('floors.building_id', $building_id);
        }
        $this->db->group_by('label');
        $this->db->order_by('total', 'DESC');
        return $this->db->get()->result_array();
    }

    /**
     * Transaksi pindah aset terbaru (pengganti query lama ke tabel detail_process).
     */
    public function recent_transactions($role_id, $building_id, $limit = 5)
    {
        $this->db->select('td.id, td.status, td.sent, td.received, td.created_at, transaction.transaction_id AS trx_code,
            asset.asset_name, asset.serial_number, src.name AS source_name, dst.name AS destination_name');
        $this->db->from('transaction_details AS td');
        $this->db->join('transaction', 'transaction.id = td.transaction_id', 'left');
        $this->db->join('asset', 'asset.id_asset = td.asset_id', 'left');
        $this->db->join('rooms AS src', 'src.id = td.source_id', 'left');
        $this->db->join('rooms AS dst', 'dst.id = td.room_id', 'left');
        if ($role_id !== '1') {
            $this->db->join('floors AS src_f', 'src_f.id = src.floor_id', 'left');
            $this->db->join('floors AS dst_f', 'dst_f.id = dst.floor_id', 'left');
            $this->db->group_start();
            $this->db->where('src_f.building_id', $building_id);
            $this->db->or_where('dst_f.building_id', $building_id);
            $this->db->group_end();
        }
        $this->db->order_by('td.id', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function get_buildings()
    {
        return $this->db->order_by('name', 'ASC')->get_where('buildings', ['status' => 1])->result_array();
    }

    public function get_categories()
    {
        return $this->db->order_by('name', 'ASC')->get_where('categories', ['status' => 1])->result_array();
    }
}
