<?php

class Room extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'Auth');
        $this->load->model('Floors_model', 'Floors');
        $this->load->model('Rooms_model', 'Rooms');
        $this->load->model('Assets_model', 'Assets');
        is_logged_in();
    }

    public function index($floor_id)
    {
        $data['user']       = $this->Auth->get_active_user($this->session->userdata('username'));
        $data['role_id']    = $data['user']['role_id'];
        $data['title']      = "Lokasi Ruangan";
        $data['rooms']      = $this->Rooms->get_all_rooms_by_floor_id($floor_id);
        $data['floor_id']   = $this->uri->segment(2);
        $data['floor']      = $this->Floors->get_single_floor_by_id($this->uri->segment(2));

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('rooms/index', $data);
        $this->load->view('templates/footer');
    }

    public function store()
    {
        $data = [
            'name'          => $this->input->post('name'),
            'status'        => 1,
            'created_at'    => date("Y-m-d H:i:s"),
            'floor_id'      => $this->input->post('floor_id')
        ];

        $result = $this->Rooms->create($data);

        if($result > 0) {
            $this->session->set_flashdata('message', 'addRoom');
            redirect('room/'.$data['floor_id']);
        } else {
            $this->session->set_flashdata('message','failed ');
            redirect('room/'.$data['floor_id']);
        }
    }

    public function edit($id)
    {
        $data['user']   = $this->Auth->get_active_user($this->session->userdata('username'));
        $data['title']  = 'Edit Ruangan';
        $data['room']   = $this->Rooms->get_single_room_by_id($id);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('rooms/edit', $data);
        $this->load->view('templates/footer');   
    }

    public function update()
    {
        $floor_id = $this->input->post('floor_id');

        $data = [
            'id'            => $this->input->post('id'),
            'name'          => $this->input->post('name'),
            'description'   => $this->input->post('description'),
            'updated_at'    => date("Y-m-d H:i:s")
        ];

        $result = $this->Rooms->update_room_by_id($data);

        if($result > 0) {
            $this->session->set_flashdata('message', 'editRoom');
            redirect('room/'.$floor_id);
        } else {
            $this->session->set_flashdata('message','failed ');
            redirect('room/'.$floor_id);
        }
    }
}