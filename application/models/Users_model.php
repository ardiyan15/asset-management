<?php

class Users_model extends CI_Model
{
    public function get_all_user()
    {
        $this->db->select('*');
        $this->db->from('user');
        $this->db->join('buildings', 'buildings.id = user.building_id');
        $this->db->order_by('user.id_user', 'DESC');
        return $this->db->get()->result_array();
    }

    public function create($data)
    {
        $this->db->insert('user', $data);
        return $this->db->affected_rows();
    }

    public function update_user($id, $data)
    {
        $this->db->where('id_user', $id);
        $this->db->update('user', $data);
        return $this->db->affected_rows();
    }
}