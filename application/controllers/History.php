<?php
defined('BASEPATH') or exit('No direct script access allowed');

class History extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'Auth');
        $this->load->model('Assets_model', 'Assets');
        $this->load->model('Transactions_model', 'Transactions');
        is_logged_in();
        is_allowed();
    }

    public function index()
    {
        $data['title'] = 'Riwayat Transaksi';
        $data['user'] = $this->Auth->get_active_user($this->session->userdata('username'));

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('history/index', $data);
        $this->load->view('templates/footer');
    }

    public function asset_out()
    {
        $role_id        = $this->session->userdata('role_id');
        $data['title']  = 'Riwayat Transaksi Keluar';
        $data['user']   = $this->Auth->get_active_user($this->session->userdata('username'));
        $building_id    = $data['user']['building_id'];
        $data['asset']  = $this->Transactions->transactions_complete_out($role_id, $building_id);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('history/out', $data);
        $this->load->view('templates/footer');
    }

    public function asset_in()
    {
        $role_id        = $this->session->userdata('role_id');
        $data['title']  = 'Riwayat Transaksi Masuk';
        $data['user']   = $this->Auth->get_active_user($this->session->userdata('username'));
        $building_id    = $data['user']['building_id'];
        $data['asset']  = $this->Transactions->transactions_complete_in($role_id, $building_id);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('history/in', $data);
        $this->load->view('templates/footer');
    }
}
