<?php

class Buildings_model extends CI_Model {

    public function get_all_buildings()
    {
        $this->db->order_by('id', 'DESC');
        return $this->db->get_where('buildings')->result_array();
    }

    public function add_building($data)
    {
        $this->db->insert('buildings', $data);
        return $this->db->affected_rows();
    }

    public function get_single_active_building_by_id($id)
    {
        return $this->db->get_where('buildings', ['status' => 1, 'id' => $id])->row_array();
    }

    public function update_data_building_by_id($data)
    {
        $this->db->set('name', $data['name']);
        $this->db->set('updated_at', $data['updated_at']);
        $this->db->where('id', $data['id']);
        $this->db->update('buildings');
        return $this->db->affected_rows();
    }
}