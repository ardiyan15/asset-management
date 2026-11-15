<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Halaman tujuan QR label aset: scan/<serial_number>.
 *
 * Sengaja tidak memanggil is_logged_in() supaya label bisa di-scan pakai HP siapa saja.
 * Tanpa login hanya tampil identitas dan lokasi aset. Riwayat perpindahan baru tampil
 * kalau user login dan (admin atau aset berada di gedung user tersebut).
 */
class Scan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'Auth');
        $this->load->model('Assets_model', 'Assets');
        $this->load->model('Transactions_model', 'Transactions');
        $this->load->library('qr');
    }

    public function index($serial_number = null)
    {
        $serial_number = trim(rawurldecode((string) $serial_number));

        $data['title'] = 'Detail Aset';
        $data['asset'] = $serial_number !== ''
            ? $this->Assets->get_asset_detail_by_serial($serial_number)
            : null;

        if (!$data['asset']) {
            $this->output->set_status_header(404);
        }

        $data['user']        = null;
        $data['can_history'] = false;
        $data['movements']   = [];

        $username = $this->session->userdata('username');
        if ($username) {
            $data['user'] = $this->Auth->get_active_user($username);
        }

        if ($data['asset']) {
            $data['qr_image'] = $this->qr->png_data_uri($this->qr->asset_url($data['asset']['serial_number']), 5);

            if ($data['user']) {
                $data['can_history'] = $data['user']['role_id'] === '1'
                    || (string) $data['user']['building_id'] === (string) $data['asset']['building_id'];
            }
            if ($data['can_history']) {
                $data['movements'] = $this->Transactions->get_asset_movements($data['asset']['id_asset']);
            }
        }

        $this->load->view('scan/index', $data);
    }

    /**
     * Arahkan ke login, lalu kembali ke halaman scan setelah berhasil login.
     */
    public function login($serial_number = null)
    {
        $serial_number = trim(rawurldecode((string) $serial_number));
        $target        = 'scan/' . rawurlencode($serial_number);

        if ($this->session->userdata('username')) {
            redirect($target);
        }
        if ($serial_number !== '') {
            $this->session->set_userdata('redirect_after_login', $target);
        }
        redirect('auth');
    }
}
