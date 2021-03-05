<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Assets_model', 'Assets');
        $this->load->model('Buildings_model', 'Buildings');
        $this->load->model('Rooms_model', 'Rooms');
        // is_logged_in();
    }

    public function index()
    {

        $data['title'] = 'Dashboard';
        $session = $this->session->userdata('email');
        if ($session === null) {
            redirect('auth');
        }
        $data['user'] = $this->Assets->login_model($this->session->userdata('email'));
        $data['order'] = $this->input->post('order');
        $data['assets'] = $this->Assets->count_asset($data['order']);
        $data['store'] = $this->Assets->get_all_store_location();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/index', $data);
        $this->load->view('templates/footer');
    }

    public function list_user()
    {

        $this->load->library('pagination');
        $data['title'] = 'List User';
        $session = $this->session->userdata('email');
        if ($session === null) {
            redirect('auth');
        }
        $data['user'] = $this->Assets->login_model($this->session->userdata('email'));

        if ($this->input->post('keyword')) {
            $data['all_users'] = $this->Assets->search_data_user();
        } else {

            $config['base_url'] = base_url('admin/list_user'); //base url
            $config['total_rows'] = $this->Assets->get_total_user_row(); //total row
            $config['per_page'] = 5;  //show record per halaman

            $config['full_tag_open'] = '<nav><ul class ="pagination">';
            $config['full_tag_close'] = '</ul></nav>';

            $config['first_link'] = 'First';
            $config['first_tag_open'] = '<li class="page-item">';
            $config['first_tag_close'] = '</li>';

            $config['last_link'] = 'Last';
            $config['last_tag_open'] = '<li class="page-item">';
            $config['last_tag_close'] = '</li>';

            $config['next_link'] = '&raquo';
            $config['next_tag_open'] = '<li class="page-item">';
            $config['next_tag_close'] = '</li>';

            $config['prev_link'] = '&laquo';
            $config['prev_tag_open'] = '<li class="page-item">';
            $config['prev_tag_close'] = '</li>';

            $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
            $config['cur_tag_close'] = '</a></li>';

            $config['num_tag_open'] = '<li class="page-item">';
            $config['num_tag_close'] = '</li>';

            $config['attributes'] = array('class' => 'page-link');

            $this->pagination->initialize($config);
            $data['start'] = $this->uri->segment(3);

            $data['all_users'] = $this->Assets->get_all_user($config['per_page'], $data['start']);
        }

        $data['pagination'] = $this->pagination->create_links();

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
            $this->session->set_flashdata(
                'message',
                'activate'
            );
            redirect('admin/list_user');
        } else {
            $this->session->set_flashdata(
                'message',
                'not_activate'
            );
            redirect('admin/list_user');
        }
    }

    public function deactivated($id)
    {
        $deactivate = $this->Assets->deactivate_list_user($id);

        if ($deactivate > 0) {
            $this->session->set_flashdata(
                'message',
                'deactivate'
            );
            redirect('admin/list_user');
        } else {
            $this->session->set_flashdata(
                'message',
                'not_deactivate'
            );
            redirect('admin/list_user');
        }
    }

    public function buildings()
    {
        $session = $this->session->userdata('email');
        if ($session === null) {
            redirect('auth');
        }
        $data['user'] = $this->Assets->login_model($this->session->userdata('email'));

        $data['title'] = "Room Location";
        $data['buildings'] = $this->Buildings->get_all_buildings();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/list_location', $data);
        $this->load->view('templates/footer');
    }

    public function addStrLocation()
    {
        $data = [
            'store_code' => $this->input->post('strCode'),
            'store_name' => $this->input->post('strName'),
            'address' => $this->input->post('address'),
            'handphone' => $this->input->post('handphone'),
            'status_location' => 1
        ];

        $addStrLct = $this->Assets->addStrLocationMdl($data);

        if ($addStrLct > 0) {
            $this->session->set_flashdata(
                'message',
                'success'
            );
            redirect('admin/store_location');
        } else {
            $this->session->set_flashdata(
                'message',
                'failed '
            );
            redirect('admin/store_location');
        }
    }

    public function editStrLocation($id)
    {
        $data = [
            'store_code' => $this->input->post('strCode'),
            'store_name' => $this->input->post('strName'),
            'address' => $this->input->post('address'),
            'handphone' => $this->input->post('handphone'),
            'status_location' => 1
        ];

        $edit = $this->Assets->editStrLocationMdl($data, $id);

        if ($edit > 0) {
            $this->session->set_flashdata(
                'message',
                'edtStr'
            );
            redirect('admin/store_location');
        } else {
            $this->session->set_flashdata(
                'message',
                'failed '
            );
            redirect('admin/store_location');
        }
    }

    public function deactivateStr($id)
    {
        $result = $this->Assets->deactivate_store($id);

        if ($result > 0) {
            $this->session->set_flashdata(
                'message',
                'deactStr'
            );
            redirect('admin/store_location');
        } else {
            $this->session->set_flashdata(
                'message',
                'failed'
            );
            redirect('admin/store_location');
        }
    }

    public function activateStr($id)
    {
        $result = $this->Assets->activated_store($id);

        if ($result > 0) {
            $this->session->set_flashdata(
                'message',
                'actvtStr'
            );
            redirect('admin/store_location');
        } else {
            $this->session->set_flashdata(
                'message',
                'failed'
            );
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
