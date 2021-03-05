<?php

class Buildings_model extends CI_Model {

    public function get_all_buildings()
    {
        $this->db->order_by('id', 'DESC');
        return $this->db->get_where('buildings', ['status' => 1])->result_array();
    }

}