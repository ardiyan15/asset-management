<?php

class Floors extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Assets_model', 'Assets');
        $this->load->model('Floors_model', 'Floors');
    }

    public function index($building_id)
    {
        $session = $this->session->userdata('email');
        
        if ($session === null) {
            redirect('auth');
        }

        $data['user']       = $this->Assets->login_model($this->session->userdata('email'));
        $data['title']      = "Lokasi Lantai";
        $data['floors']     = $this->Floors->get_all_floors_by_building_id($building_id);
        $data['building_id'] = $this->uri->segment(2);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('floors/index', $data);
        $this->load->view('templates/footer');
    }

    public function store()
    {
        $data = [
            'name' => $this->input->post('name'),
            'status' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'building_id' => $this->input->post('building_id')
        ];
        
        $result = $this->Floors->create($data);

        if($result > 0) {
            $this->session->set_flashdata('message', 'addFloor');
            redirect('floors/'.$data['building_id']);
        } else {
            $this->session->set_flashdata(
                'message',
                'failed '
            );
            redirect('floors/'.$data['building_id']);
        }
    }
}