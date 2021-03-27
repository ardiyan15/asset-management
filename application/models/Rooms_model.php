<?php

class Rooms_model extends CI_Model {
    
    public function get_all_rooms()
    {
        $this->db->order_by('id', 'DESC');
        return $this->db->get_where('rooms', ['status' => 1])->result_array();
    }

    public function get_all_rooms_by_floor_id($floor_id)
    {
        $this->db->order_by('id', 'DESC');
        return $this->db->get_where('rooms', ['status' => 1, 'floor_id' => $floor_id])->result_array();
    }

    public function create($data)
    {
        $this->db->insert('rooms', $data);
        return $this->db->affected_rows();
    }
}