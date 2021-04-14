<?php

class Auth_model extends CI_Model
{

    public function get_active_user($username)
    {
        return $this->db->get_where('user', ['username' => $username, 'is_active' => 1])->row_array();
    }

    public function change_password_model($password_hash, $id)
    {
        $this->db->set('password', $password_hash);
        $this->db->where('id_user', $id);
        $this->db->update('user');
    }

    public function update_profile_model($data)
    {
        if ($data['newImage'] !== null) {
            $this->db->set('image', $data['newImage']);
        }
        $this->db->set('username', $data['username']);
        $this->db->where('id_user', $data['id_user']);
        $this->db->update('user');
    }
}