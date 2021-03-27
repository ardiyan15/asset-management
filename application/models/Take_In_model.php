<?php

class Take_In_model extends CI_Model
{

    public function get_asset_take_in_by_building_id($building_id)
    {
        $this->db->select('asset.asset_name, asset.merk, asset.serial_number, rooms.name, transactions.id');
        $this->db->from('asset');
        $this->db->join('transactions', 'transactions.asset_id = asset.id_asset');
        $this->db->join('rooms', 'transactions.room_id = rooms.id');
        $this->db->join('floors', 'rooms.floor_id = floors.id');
        $this->db->join('buildings', 'floors.building_id = buildings.id');
        $this->db->where(['buildings.id' => $building_id, 'transactions.status' => 0]);
        return $this->db->get()->result_array();
    }

    public function move_asset($location, $assetId, $id)
    {
        $this->db->set('deleted', 1);
        $this->db->set('acctime', date('Y-m-d'));
        $this->db->where('asset_id', $assetId);
        $this->db->update('detail_process');
        $this->db->set('asset_location', $location);
        $this->db->where('id_asset', $assetId);
        $this->db->update('asset');
        return $this->db->affected_rows();
    }

}