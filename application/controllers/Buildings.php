<?php

class Buildings extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Assets_model', 'Assets');
        $this->load->model('Buildings_model', 'Buildings');
    }

    public function index()
    {
        $session = $this->session->userdata('email');
        if ($session === null) {
            redirect('auth');
        }
        $data['user']       = $this->Assets->login_model($this->session->userdata('email'));
        $data['title']      = "Lokasi Bangunan";
        $data['buildings']  = $this->Buildings->get_all_buildings();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('buildings/index', $data);
        $this->load->view('templates/footer');
    }

    public function edit($id)
    {
        $data['title']  = 'Edit Building';
        $data['user']   = $this->Assets->login_model($this->session->userdata('email'));
        $data['result'] = $this->Buildings->get_single_active_building_by_id($id);
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('buildings/edit_building', $data);
        $this->load->view('templates/footer');
    }

    public function update()
    {
        $data = [
            'id'            => $this->input->post('id'),
            'name'          => $this->input->post('name'),
            'updated_at'    => date("Y-m-d H:i:s")
        ];

        $result = $this->Buildings->update_data_building_by_id($data);
        if($result > 0) {
            $this->session->set_flashdata('message', 'editBuilding');
            redirect('buildings');
        } else {
            $this->session->set_flashdata(
                'message',
                'failed '
            );
            redirect('buildings');
        }
    }
}