<?php

class Categories_model extends CI_Model {

    public function get_all_categories()
    {
        $this->db->order_by('id', 'DESC');
        return $this->db->get_where('categories', ['status' => 1])->result_array();
    }
}