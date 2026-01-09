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
        $this->load->library('encryption');
        $this->load->library('pdf');
        $this->load->helper('url_crypto');
        is_logged_in();
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
        $role_id = $this->session->userdata('role_id');
        $data['title'] = 'Riwayat Transaksi Keluar';
        $data['user'] = $this->Auth->get_active_user($this->session->userdata('username'));
        $building_id = $data['user']['building_id'];
        $data['asset'] = $this->Transactions->transactions_complete_out($role_id, $building_id);

        foreach ($data['asset'] as &$row) {
            $cipher = $this->encryption->encrypt($row['transaction_id']);
            $row['trx_token'] = urlsafe_b64encode($cipher);
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('history/out', $data);
        $this->load->view('templates/footer');
    }

    public function detail_out($transaction_id)
    {
        $data['title'] = 'Detail Transaksi Keluar';
        $data['user'] = $this->Auth->get_active_user($this->session->userdata('username'));

        $cipher = urlsafe_b64decode($transaction_id);
        $trxId = $this->encryption->decrypt($cipher);

        $data['asset'] = $this->Transactions->get_transaction_out_by_id($trxId);
        $data['trx_token'] = $transaction_id; // Pass token to view for print link

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('history/detail_out', $data);
        $this->load->view('templates/footer');
    }

    public function print_bast($transaction_id)
    {
        $data['title'] = 'Berita Acara Serah Terima';

        $cipher = urlsafe_b64decode($transaction_id);
        $trxId = $this->encryption->decrypt($cipher);

        $data['asset'] = $this->Transactions->get_transaction_out_by_id($trxId);

        // Ensure we have data
        if (empty($data['asset'])) {
            redirect('history/asset_out');
        }

        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->filename = "BAST-" . $data['asset']['transaction_id'] . ".pdf";
        $this->pdf->load_view('history/print_bast', $data);
    }

    public function asset_in()
    {
        $role_id = $this->session->userdata('role_id');
        $data['title'] = 'Riwayat Transaksi Masuk';
        $data['user'] = $this->Auth->get_active_user($this->session->userdata('username'));
        $building_id = $data['user']['building_id'];
        $data['asset'] = $this->Transactions->transactions_complete_in($role_id, $building_id);

        foreach ($data['asset'] as &$row) {
            $cipher = $this->encryption->encrypt($row['transaction_id']);
            $row['trx_token'] = urlsafe_b64encode($cipher);
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('history/in', $data);
        $this->load->view('templates/footer');
    }

    public function detail_in($transaction_id)
    {
        $data['title'] = 'Detail Transaksi Masuk';
        $data['user'] = $this->Auth->get_active_user($this->session->userdata('username'));

        $cipher = urlsafe_b64decode($transaction_id);
        $trxId = $this->encryption->decrypt($cipher);

        $data['asset'] = $this->Transactions->get_transaction_in_by_id($trxId);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('history/detail_in', $data);
        $this->load->view('templates/footer');
    }
}
