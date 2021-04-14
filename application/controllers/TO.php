<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TO extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'Auth');
        $this->load->model('Assets_model', 'Assets');
        $this->load->model('Transactions_model', 'Transactions');
        is_logged_in();
    }

    public function index()
    {
        $data['title']  = "Transaksi Keluar";
        $data['user']   = $this->Auth->get_active_user($this->session->userdata('username'));
        $role_id        = $data['user']['role_id'];
        $building_id    = $data['user']['building_id'];
        $data['asset']  = $this->Transactions->transaction_out_process($role_id, $building_id);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('TO/index', $data);
        $this->load->view('templates/footer');
    }

    public function print_page()
    {

        $data = $this->input->post('sndAst');

        $header['title'] = "Receipt Form";
        $result['asset'] = $this->Assets->print_model($data);
        $this->load->view('templates/header', $header);
        $this->load->view('TO/print', $result);
    }
}
