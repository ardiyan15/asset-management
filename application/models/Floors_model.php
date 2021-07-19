<?php

class Floors_model extends CI_Model {

    public function get_all_floors_by_building_id($building_id)
    {
        $this->db->order_by('name', 'ASC');
        return $this->db->get_where('floors', ['building_id' => $building_id ])->result_array();
    }

    public function create($data)
    {
        $this->db->insert('floors', $data);
        return $this->db->affected_rows();
    }

    public function get_single_floor_by_id($id)
    {
        return $this->db->get_where('floors', ['id' => $id])->row_array();
    }

    public function update_floors_by_id($data)
    {
        $this->db->set('name', $data['name']);
        $this->db->set('updated_at', $data['updated_at']);
        $this->db->where('id', $data['id']);
        $this->db->update('floors');
        return $this->db->affected_rows();
    }
}