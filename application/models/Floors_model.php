<?php

class Floors_model extends CI_Model {

    public function get_all_floors_by_building_id($building_id)
    {
        $this->db->order_by('id', 'DESC');
        return $this->db->get_where('floors', ['building_id' => $building_id ])->result_array();
    }

    public function create($data)
    {
        $this->db->insert('floors', $data);
        return $this->db->affected_rows();
    }
}