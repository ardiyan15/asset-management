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
    is_logged_in();
  }

  public function index()
  {
    $data['title']  = "Transaksi Masuk";
    $data['user']   = $this->Auth->get_active_user($this->session->userdata('username'));
    $building_id    = $data['user']['building_id'];
    $role_id        = $data['user']['role_id'];
    $data['asset']  = $this->Take_in->get_asset_take_in_by_building_id($role_id, $building_id);

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('templates/topbar', $data);
    $this->load->view('TI/index', $data);
    $this->load->view('templates/footer');
  }

  public function acc($id)
  {
    $transaction        = $this->Transactions->get_data_transaction_by_id($id);
    $update_transaction = $this->Transactions->update_status_transaction_by_id($transaction['id']);
    $update_asset       = $this->Assets->update_asset_location($transaction['asset_id'], $transaction['room_id']);

    if ($update_transaction > 0) {
      if($update_asset) {
        $this->session->set_flashdata('message','ti_success');
        redirect('asset');
      } else {
        $this->session->set_flashdata('message','failed');
        redirect('asset');  
      }
    } else {
      $this->session->set_flashdata('message','failed');
      redirect('asset');
    }
  }

  public function reject($id)
  {
    $data['user']   = $this->Assets->login_model($this->session->userdata('email'));
    $data['asset']  = $this->Assets->acc_model($id);

    $location  = $data['asset']['source'];
    $assetId   = $data['asset']['asset_id'];

    if ($this->Assets->reject_model($location, $assetId, $id) > 0) {
      $this->session->set_flashdata('message','reject');
      redirect('asset');
    } else {
      $this->session->set_flashdata('message','failed ');
      redirect('asset');
    }
  }

  public function bulk_acc()
  {
      $asset_ids          = $this->input->post('asset_ids');
      $rooms_ids          = $this->Transactions->get_transaction_room_id_by_asset_id($asset_ids);
      $value_room_ids     = array_column($rooms_ids, 'room_id');
      $asset_result       = $this->Assets->update_room_id_by_asset_id($value_room_ids, $asset_ids);
      $result_transaction = $this->Transactions->bulk_update($asset_ids, $value_room_ids);
      
      if($asset_result && $result_transaction > 0){
        echo json_encode(1);
      } else {
        echo json_encode(0);
      }
  }
}
