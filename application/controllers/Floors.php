<?php

class Floors extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'Auth');
        $this->load->model('Buildings_model', 'Buildings');
        $this->load->model('Assets_model', 'Assets');
        $this->load->model('Floors_model', 'Floors');
        is_logged_in();
    }

    public function index($building_id)
    {
        $data['user']           = $this->Auth->get_active_user($this->session->userdata('username'));
        $data['role_id']        = $data['user']['role_id'];
        $data['title']          = "Lokasi Bangunan";
        $data['floors']         = $this->Floors->get_all_floors_by_building_id($building_id);
        $data['building_id']    = $building_id;
        $data['building_name']  = $this->Buildings->get_single_active_building_by_id($this->uri->segment(2));

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('floors/index', $data);
        $this->load->view('templates/footer');
    }

    public function store()
    {
        $data = [
            'name'          => $this->input->post('name'),
            'status'        => 1,
            'created_at'    => date("Y-m-d H:i:s"),
            'building_id'   => $this->input->post('building_id')
        ];
        
        $result = $this->Floors->create($data);

        if($result > 0) {
            $this->session->set_flashdata('message', 'addFloor');
            redirect('floors/'.$data['building_id']);
        } else {
            $this->session->set_flashdata('message','failed ');
            redirect('floors/'.$data['building_id']);
        }
    }

    public function edit($id)
    {
        $data['user']   = $this->Auth->get_active_user($this->session->userdata('username'));
        $data['floor']  = $this->Floors->get_single_floor_by_id($id);
        $data['title']  = 'Edit Lokasi Lantai';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('floors/edit', $data);
        $this->load->view('templates/footer');   
    }

    public function update()
    {
        $building_id = $this->input->post('building_id');
        
        $data = [
            'id'            => $this->input->post('id'),
            'name'          => $this->input->post('name'),
            'updated_at'    => date("Y-m-d H:i:s")
        ];

        $result = $this->Floors->update_floors_by_id($data);

        if($result > 0) {
            $this->session->set_flashdata('message', 'editFloor');
            redirect('floors/'.$building_id);
        } else {
            $this->session->set_flashdata('message','failed ');
            redirect('floors/'.$building_id);
        }
    }
}