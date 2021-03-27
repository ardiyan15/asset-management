<?php

class Room extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rooms_model', 'Rooms');
        $this->load->model('Assets_model', 'Assets');
        // is_logged_in();
    }

    public function index($floor_id)
    {
        $session = $this->session->userdata('email');
        if ($session === null) {
            redirect('auth');
        }
        $data['user'] = $this->Assets->login_model($this->session->userdata('email'));
        
        $data['title'] = "Lokasi Ruangan";
        $data['rooms'] = $this->Rooms->get_all_rooms_by_floor_id($floor_id);
        $data['floor_id'] = $this->uri->segment(2);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('rooms/index', $data);
        $this->load->view('templates/footer');
    }

    public function list_rooms()
    {
        $building_id = $this->input->post('building_id');
        $result = $this->Rooms->get_all_rooms_by_building_id($building_id);
        echo json_encode($result);
    }

    public function store()
    {
        $data = [
            'name' => $this->input->post('name'),
            'status' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'floor_id' => $this->input->post('floor_id')
        ];

        $result = $this->Rooms->create($data);

        if($result > 0) {
            $this->session->set_flashdata('message', 'addRoom');
            redirect('room/'.$data['floor_id']);
        } else {
            $this->session->set_flashdata(
                'message',
                'failed '
            );
            redirect('room/'.$data['floor_id']);
        }
    }
}