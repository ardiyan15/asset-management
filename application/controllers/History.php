<?php
defined('BASEPATH') or exit('No direct script access allowed');

class History extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Assets_model', 'Assets');
    }

    public function index()
    {
        $data['title'] = 'History';
        $session = $this->session->userdata('email');
        if ($session == null) {
            redirect('auth');
        }
        $data['user'] = $this->Assets->login_model($this->session->userdata('email'));

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('history/index', $data);
        $this->load->view('templates/footer');
    }

    public function asset_out()
    {
        $data['title'] = 'Assets That Have Been Sent';
        $session = $this->session->userdata('email');
        if ($session == null) {
            redirect('auth');
        }
        $data['user'] = $this->Assets->login_model($this->session->userdata('email'));

        $data['asset'] = $this->Assets->get_data_history_asset_out($data['user']['user_code']);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('history/out', $data);
        $this->load->view('templates/footer');
    }

    public function asset_in()
    {
        $data['title'] = 'Assets That Have Been Received';
        $session = $this->session->userdata('email');
        if ($session == null) {
            redirect('auth');
        }
        $data['user'] = $this->Assets->login_model($this->session->userdata('email'));

        $data['asset'] = $this->Assets->get_data_history_asset_in($data['user']['user_code']);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('history/in', $data);
        $this->load->view('templates/footer');
    }
}
