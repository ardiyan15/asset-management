<?php

class Rooms_model extends CI_Model {
    
    public function get_all_rooms()
    {
        $this->db->order_by('id', 'DESC');
        return $this->db->get_where('rooms', ['status' => 1])->result_array();
    }

    public function get_room_by_building_id($role_id, $building_id)
    {
        $this->db->select('rooms.name, rooms.id');
        $this->db->from('rooms');
        if($role_id !== '1'){
            $this->db->join('floors', 'floors.id = rooms.floor_id');
            $this->db->join('buildings', 'buildings.id = floors.building_id');
            $this->db->where(['buildings.id' => $building_id, 'rooms.status' => 1]);
            $this->db->order_by('rooms.id', 'DESC');
        }
        return $this->db->get()->result_array();
    }

    public function get_all_rooms_by_floor_id($floor_id)
    {
        $this->db->select('rooms.name, rooms.id, rooms.status, rooms.description, COUNT(id_asset) AS total');
        $this->db->from('rooms');
        $this->db->join('asset', 'asset.room_id = rooms.id', 'left');
        $this->db->order_by('rooms.name', 'ASC');
        $this->db->group_by('rooms.id');
        $this->db->where(['rooms.status' => 1, 'rooms.floor_id' => $floor_id]);
        return $this->db->get()->result_array();
    }

    public function get_room_name_by_id($room_id)
    {
        $this->db->select('rooms.name');
        $this->db->from('rooms');
        $this->db->where('id', $room_id);
        return $this->db->get()->row_array();
    }

    public function create($data)
    {
        $this->db->insert('rooms', $data);
        return $this->db->affected_rows();
    }

    public function get_room_by_asset_id($asset_ids)
    {
        $this->db->select('room_id');
        $this->db->from('asset');
        $this->db->where_in('id_asset', $asset_ids);
        return $this->db->get()->result_array();
    }

    public function get_single_room_by_id($id)
    {
        return $this->db->get_where('rooms', ['id' => $id])->row_array();
    }

    public function update_room_by_id($data)
    {
        $this->db->set('name', $data['name']);
        $this->db->set('description', $data['description']);
        $this->db->set('updated_at', $data['updated_at']);
        $this->db->where('id', $data['id']);
        $this->db->update('rooms');
        return $this->db->affected_rows();
    }
}