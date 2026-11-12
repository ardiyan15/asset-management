<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TI extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('Auth_model', 'Auth');
    $this->load->model('Assets_model', 'Assets');
    $this->load->model('Take_In_model', 'Take_in');
    $this->load->model('Transactions_model', 'Transactions');
    $this->load->library('encryption');
    $this->load->helper('url_crypto');
    is_logged_in();
  }

  public function index()
  {
    $data['title'] = "Transaksi Masuk";
    $data['user'] = $this->Auth->get_active_user($this->session->userdata('username'));
    $building_id = $data['user']['building_id'];
    $role_id = $data['user']['role_id'];
    $data['asset'] = $this->Take_in->get_asset_take_in_by_building_id($role_id, $building_id);

    foreach ($data['asset'] as &$row) {
      $row['trx_token'] = urlsafe_b64encode($this->encryption->encrypt($row['transaction_id']));
      // token per detail transaksi untuk tombol Terima / Tolak
      $row['detail_token'] = urlsafe_b64encode($this->encryption->encrypt($row['id']));
    }
    unset($row);

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('templates/topbar', $data);
    $this->load->view('TI/index', $data);
    $this->load->view('templates/footer');

    unset($_SESSION['message']);
  }

  public function detail($transaction_id)
  {
    $data['title'] = "Detail Transaksi Masuk";
    $data['user'] = $this->Auth->get_active_user($this->session->userdata('username'));

    $cipher = urlsafe_b64decode($transaction_id);
    $trxId = $this->encryption->decrypt($cipher);

    $data['assets'] = $this->Take_in->get_detail_take_in($trxId);

    foreach ($data['assets'] as &$row) {
      $cipher = $this->encryption->encrypt($row['id']);
      $row['trx_token'] = urlsafe_b64encode($cipher);
    }

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('templates/topbar', $data);
    $this->load->view('TI/detail', $data);
    $this->load->view('templates/footer');
  }

  public function acc($token = null)
  {
    $detail = $this->_pending_detail_from_token($token);

    if ($detail && $this->Transactions->accept_details([$detail])) {
      $this->session->set_flashdata('message', 'ti_success');
    } else {
      $this->session->set_flashdata('message', 'ti_failed');
    }
    redirect('ti');
  }

  public function reject($token = null)
  {
    $detail = $this->_pending_detail_from_token($token);

    if ($detail && $this->Transactions->reject_detail($detail)) {
      $this->session->set_flashdata('message', 'reject');
    } else {
      $this->session->set_flashdata('message', 'ti_failed');
    }
    redirect('ti');
  }

  public function bulk_acc()
  {
    $asset_ids = array_filter((array) $this->input->post('asset_ids'), 'ctype_digit');
    if (empty($asset_ids)) {
      echo json_encode(0);
      return;
    }

    $user = $this->Auth->get_active_user($this->session->userdata('username'));
    $details = $this->Transactions->get_pending_details($user['role_id'], $user['building_id'], [], $asset_ids);

    echo json_encode($this->Transactions->accept_details($details) ? count($details) : 0);
  }

  /**
   * Token terenkripsi (urlsafe base64) -> detail transaksi yang masih pending, atau null.
   */
  private function _pending_detail_from_token($token)
  {
    $cipher = urlsafe_b64decode((string) $token);
    $detail_id = $cipher ? $this->encryption->decrypt($cipher) : false;

    if (!$detail_id || !ctype_digit((string) $detail_id)) {
      return null;
    }

    $user = $this->Auth->get_active_user($this->session->userdata('username'));
    $details = $this->Transactions->get_pending_details(
      $user['role_id'],
      $user['building_id'],
      ['transaction_details.id' => (int) $detail_id]
    );

    return $details[0] ?? null;
  }
}
