<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Room extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rooms_model', 'Rooms');
        // is_logged_in();
    }

    public function list_rooms()
    {
        $building_id = $this->input->post('building_id');
        $result = $this->Rooms->get_all_rooms_by_building_id($building_id);
        echo json_encode($result);
    }
}