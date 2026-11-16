<?php

/**
 * Data laporan nilai aset (harga perolehan, penyusutan garis lurus, nilai buku).
 * Hanya aset aktif (belum dihapus). Non-admin dibatasi ke gedungnya.
 */
class Valuation_model extends CI_Model
{
    public function get_assets($role_id, $building_id, array $filters = [])
    {
        $this->db->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number, asset.asset_condition,
            asset.purchase_date, asset.purchase_price, asset.vendor, asset.warranty_until,
            categories.id AS category_id, categories.code AS category_code, categories.name AS category_name,
            categories.useful_life_years, categories.residual_percent,
            rooms.name AS room_name, floors.name AS floor_name, buildings.id AS building_id, buildings.name AS building_name');
        $this->db->from('asset');
        $this->db->join('categories', 'categories.id = asset.category_id', 'left');
        $this->db->join('rooms', 'rooms.id = asset.room_id');
        $this->db->join('floors', 'floors.id = rooms.floor_id');
        $this->db->join('buildings', 'buildings.id = floors.building_id');
        $this->db->where('asset.is_disposed', 0);

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

        $this->db->order_by('categories.name', 'ASC');
        $this->db->order_by('asset.asset_name', 'ASC');
        $this->db->order_by('asset.serial_number', 'ASC');
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
