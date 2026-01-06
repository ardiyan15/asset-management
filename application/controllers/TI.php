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
      $cipher = $this->encryption->encrypt($row['transaction_id']);
      $row['trx_token'] = urlsafe_b64encode($cipher);
    }

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

  public function acc($id)
  {
    $cipher = urlsafe_b64decode($id);
    $asset_id = $this->encryption->decrypt($cipher);

    $transaction = $this->Transactions->get_data_transaction_by_id($asset_id);
    $update_transaction = $this->Transactions->update_status_transaction_by_id($transaction['id']);
    $update_asset = $this->Assets->update_asset_location($transaction['asset_id'], $transaction['room_id']);

    if ($update_transaction > 0) {
      if ($update_asset) {
        $this->session->set_flashdata('message', 'ti_success');
        redirect('ti');
      } else {
        $this->session->set_flashdata('message', 'failed');
        redirect('ti');
      }
    } else {
      $this->session->set_flashdata('message', 'failed');
      redirect('ti');
    }
  }

  public function reject($id)
  {
    $data['asset'] = $this->Take_in->get_transaction_data_by_id($id);

    $location = $data['asset']['source'];
    $assetId = $data['asset']['asset_id'];

    if ($this->Assets->reject_model($location, $assetId, $id) > 0) {
      $this->session->set_flashdata('message', 'reject');
      redirect('ti');
    } else {
      $this->session->set_flashdata('message', 'failed ');
      redirect('ti');
    }
  }

  public function bulk_acc()
  {
    $asset_ids = $this->input->post('asset_ids');
    $rooms_ids = $this->Transactions->get_transaction_room_id_by_asset_id($asset_ids);
    $value_room_ids = array_column($rooms_ids, 'room_id');
    $asset_result = $this->Assets->update_room_id_by_asset_id($value_room_ids, $asset_ids);
    $result_transaction = $this->Transactions->bulk_update($asset_ids, $value_room_ids);

    if ($asset_result && $result_transaction > 0) {
      echo json_encode(1);
    } else {
      echo json_encode(0);
    }
  }
}
