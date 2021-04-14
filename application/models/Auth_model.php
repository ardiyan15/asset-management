<?php

class Auth_model extends CI_Model
{

    public function get_active_user($username)
    {
        return $this->db->get_where('user', ['username' => $username, 'is_active' => 1])->row_array();
    }

}