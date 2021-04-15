<?php

class Take_In_model extends CI_Model
{

    public function get_asset_take_in_by_building_id($role_id, $building_id)
    {
        $this->db->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number, source.name, transactions.id, rooms.name AS destination');
        $this->db->from('asset');
        $this->db->join('transactions', 'transactions.asset_id = asset.id_asset');
        $this->db->join('rooms AS source', 'transactions.source_id = source.id');
        $this->db->join('rooms', 'transactions.room_id = rooms.id');
        if($role_id !== '1'){
            $this->db->join('floors', 'rooms.floor_id = floors.id');
            $this->db->join('buildings', 'floors.building_id = buildings.id');
            $this->db->where('buildings.id', $building_id);
        }
        $this->db->where('transactions.status', 0);
        return $this->db->get()->result_array();
    }
}