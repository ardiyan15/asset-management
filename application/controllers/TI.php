<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TI extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('Assets_model', 'Assets');
    $this->load->model('Take_In_model', 'Take_in');
    $this->load->model('Transactions_model', 'Transactions');
  }

  public function index()
  {

    $session = $this->session->userdata('email');
    if ($session === null) {
      redirect('auth');
    }

    $data['title']  = "Take In";
    $data['user']   = $this->Assets->login_model($this->session->userdata('email'));
    $building_id    = $data['user']['building_id'];
    $data['asset']  = $this->Take_in->get_asset_take_in_by_building_id($building_id);

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('templates/topbar', $data);
    $this->load->view('TI/index', $data);
    $this->load->view('templates/footer');
  }

  public function acc($id)
  {
    $data['user']       = $this->Assets->login_model($this->session->userdata('email'));
    $transaction        = $this->Transactions->get_data_transaction_by_id($id);
    $update_transaction = $this->Transactions->update_status_transaction_by_id($transaction['id']);

    $update_asset       = $this->Assets->update_asset_location($transaction['asset_id'], $transaction['room_id']);

    if ($update_transaction > 0) {
      if($update_asset) {
        $this->session->set_flashdata(
          'message',
          'ti_success'
        );
        redirect('asset');
      } else {
        $this->session->set_flashdata(
          'message',
          'failed'
        );
        redirect('asset');  
      }
    } else {
      $this->session->set_flashdata(
        'message',
        'failed'
      );
      redirect('asset');
    }
  }

  public function reject($id)
  {
    $data['user'] = $this->Assets->login_model($this->session->userdata('email'));
    $data['asset'] = $this->Assets->acc_model($id);

    $location = $data['asset']['source'];
    $assetId = $data['asset']['asset_id'];

    if ($this->Assets->reject_model($location, $assetId, $id) > 0) {
      $this->session->set_flashdata(
        'message',
        'reject'
      );
      redirect('asset');
    } else {
      $this->session->set_flashdata(
        'message',
        'failed '
      );
      redirect('asset');
    }
  }
}
