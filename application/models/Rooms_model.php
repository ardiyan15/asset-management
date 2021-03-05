<?php

class Rooms_model extends CI_Model {
    
    public function get_all_rooms()
    {
        $this->db->order_by('id', 'DESC');
        return $this->db->get_where('rooms', ['status' => 1])->result_array();
    }

    public function get_all_rooms_by_building_id($building_id)
    {
        $this->db->order_by('id', 'DESC');
        return $this->db->get_where('rooms', ['status' => 1, 'building_id' => $building_id])->result_array();
    }

}