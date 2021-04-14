<?php

class Transaction extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('Auth_model', 'Auth');
    $this->load->model('Assets_model', 'Assets');
    $this->load->model('Rooms_model', 'Rooms');
    $this->load->model('Transactions_model', 'Transactions');
  }

  public function index()
  {
    $data['user']       = $this->Auth->get_active_user($this->session->userdata('username'));
    $building_id        = $data['user']['building_id'];
    $data['title']      = 'List Assets';
    $result['error']    = "Data Not Found!";
    $data['rooms']      = $this->Rooms->get_all_rooms();

    if ($this->input->post('keyword')) {
      $data['assets'] = $this->Assets->search_data_model($data['user']['role_id'], $data['user']['building_id']);
    } else {
      $data['assets'] = $this->Assets->menu_model($data['user']['role_id'], $data['user']['building_id']);
    }

    if ($data['assets']) {
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('transaction/index', $data);
        $this->load->view('templates/footer');
      } else {
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('transaction/index', $result);
        $this->load->view('templates/footer');
      }
  }

  public function store()
  {
      $id           = $this->input->post('asset_id');
      $room_id      = $this->input->post('room');
      $source_id    = $this->input->post('source');

      $data = [
          'asset_id'    => $id,
          'room_id'     => $room_id,
          'source_id'   => $source_id,
          'status'      => 0,
          'sent'        => date('Y-m-d'),
          'created_at'  => date('Y-m-d')
      ];

      $transaction      = $this->Transactions->create_transaction_asset($data);
      $update_placement = $this->Assets->update_placement_status($data['asset_id']);
      
      if($transaction > 0) {
          if($update_placement > 0){
              $this->session->set_flashdata('message','added ');
              redirect('transaction');
          } else {
            $this->session->set_flashdata('message','failed');
            redirect('transaction');
          }
      } else {
      $this->session->set_flashdata('message','failed');
      redirect('transaction');
    }
  }

  public function bulk_transaction()
  {
      $asset_ids      = $this->input->post('asset_id');
      $room_id        = $this->input->post('room_id');
      $assets         = $this->Assets->bulk_asset_transaction($asset_ids);
      $source_id      = $this->Rooms->get_room_by_asset_id($asset_ids);
      $new_source_id  = array_column($source_id, 'room_id');
      $transactions   = $this->Transactions->bulk_transaction($asset_ids, $room_id, $new_source_id);
        if($assets > 0){
          if($transactions > 0){
              echo json_encode(1);
          } else {
            echo json_encode(0);
          }
        } else {
          echo json_encode(0);
        }
  }
}
