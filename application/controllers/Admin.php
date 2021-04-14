<?php

class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Assets_model', 'Assets');
        $this->load->model('Auth_model', 'Auth');
        $this->load->model('Buildings_model', 'Buildings');
        $this->load->model('Rooms_model', 'Rooms');
        $this->load->model('Users_model', 'Users');
        is_logged_in();
    }

    public function index()
    {
        $data['title']      = 'Dashboard';
        $data['user']       = $this->Auth->get_active_user($this->session->userdata('username'));
        $building_id        = $data['user']['building_id'];
        $data['room_id']    = $this->input->post('room_id');
        $data['assets']     = $this->Assets->filter_asset_by_room_id($data['room_id'], $building_id);
        $data['room_name']  = $this->Rooms->get_room_name_by_id($data['room_id']);
        $data['rooms']      = $this->Rooms->get_room_by_building_id($building_id);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/index', $data);
        $this->load->view('templates/footer');
    }

    public function list_user()
    {
        $data['title']      = 'Daftar Pengguna';
        $data['user']       = $this->Auth->get_active_user($this->session->userdata('username'));
        $data['all_users']  = $this->Users->get_all_user();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/list_user', $data);
        $this->load->view('templates/footer');
    }

    public function activated($id)
    {
        $active = $this->Assets->activate_list_user($id);

        if ($active > 0) {
            $this->session->set_flashdata('message','activate');
            redirect('admin/list_user');
        } else {
            $this->session->set_flashdata('message','not_activate');
            redirect('admin/list_user');
        }
    }

    public function deactivated($id)
    {
        $deactivate = $this->Assets->deactivate_list_user($id);

        if ($deactivate > 0) {
            $this->session->set_flashdata('message','deactivate');
            redirect('admin/list_user');
        } else {
            $this->session->set_flashdata('message','not_deactivate');
            redirect('admin/list_user');
        }
    }

    public function add_building_location()
    {
        $data = [
            'name'          => $this->input->post('name'),
            'status'        => 1,
            'created_at'    => date("Y-m-d H:i:s")
        ];

        $add_building = $this->Buildings->add_building($data);

        if ($add_building > 0) {
            $this->session->set_flashdata('message','success');
            redirect('buildings');
        } else {
            $this->session->set_flashdata('message','failed ');
            redirect('buildings');
        }
    }

    public function editStrLocation($id)
    {
        $data = [
            'store_code'        => $this->input->post('strCode'),
            'store_name'        => $this->input->post('strName'),
            'address'           => $this->input->post('address'),
            'handphone'         => $this->input->post('handphone'),
            'status_location'   => 1
        ];

        $edit = $this->Assets->editStrLocationMdl($data, $id);

        if ($edit > 0) {
            $this->session->set_flashdata('message','edtStr');
            redirect('admin/store_location');
        } else {
            $this->session->set_flashdata('message','failed ');
            redirect('admin/store_location');
        }
    }

    public function activateStr($id)
    {
        $result = $this->Assets->activated_store($id);

        if ($result > 0) {
            $this->session->set_flashdata('message','actvtStr');
            redirect('admin/store_location');
        } else {
            $this->session->set_flashdata('message','failed');
            redirect('admin/store_location');
        }
    }

    public function filter_asset()
    {
        $this->Assets->get_filter_asset();
    }

    public function list_rooms()
    {
        $session = $this->session->userdata('email');
        if ($session === null) {
            redirect('auth');
        }
        $data['user'] = $this->Assets->login_model($this->session->userdata('email'));
        
        $building_id = $this->uri->segment(3);
        $data['title'] = "Room Location";
        $data['buildings'] = $this->Rooms->get_all_rooms_by_building_id($building_id);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('rooms/index', $data);
        $this->load->view('templates/footer');
    }
}
