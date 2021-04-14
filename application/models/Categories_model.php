<?php

class Categories_model extends CI_Model {

    public function get_all_categories()
    {
        $this->db->order_by('id', 'DESC');
        return $this->db->get_where('categories', ['status' => 1])->result_array();
    }

    public function create($data)
    {
        $this->db->insert('categories', $data);
        return $this->db->affected_rows();
    }

    public function delete_category_by_id($id)
    {
        $this->db->set('status', 0);
        $this->db->set('updated_at', date("Y-m-d H:i:s"));
        $this->db->where('id', $id);
        $this->db->update('categories');
        return $this->db->affected_rows();
    }

    public function get_category_by_id($id)
    {
        return $this->db->get_where('categories', ['id' => $id])->row_array();
    }

    public function update_category_by_id($data)
    {
        $this->db->set('code', $data['code']);
        $this->db->set('name', $data['name']);
        $this->db->set('updated_at', $data['updated_at']);
        $this->db->where('id', $data['id']);;
        $this->db->update('categories');
        return $this->db->affected_rows();
    }
}