<?php

class Transaction extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('Assets_model', 'Assets');
    $this->load->model('Rooms_model', 'Rooms');
    $this->load->model('Transactions_model', 'Transactions');
  }

  public function index()
  {
    $data['user'] = $this->Assets->login_model($this->session->userdata('email'));
    $data['amount_data'] = $this->Assets->get_total_row('IT', $data['user']['user_code']);

    $data['title']      = 'List Assets';
    $result['error']    = "Data Not Found!";
    $data['rooms']      = $this->Rooms->get_all_rooms();
    $data['assets']     = $this->Assets->menu_model($data['user']['user_code'], $data['user']['building_id']);

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
      $id       = $this->input->post('asset_id');
      $room_id  = $this->input->post('room');
      $source   = $this->input->post('source');

      $data = [
          'asset_id'    => $id,
          'room_id'     => $room_id,
          'status'      => 0,
          'sent'        => date('Y-m-d'),
          'created_at'  => date('Y-m-d')
      ];

      $transaction      = $this->Transactions->create_transaction_asset($data);
      $update_placement = $this->Assets->update_placement_status($data['asset_id']);
      if($transaction > 0) {
          if($update_placement > 0){
              $this->session->set_flashdata(
                'message',
                'added '
              );
              redirect('transaction');
          } else {
            $this->session->set_flashdata(
              'message',
              'failed'
            );
            redirect('transaction');
          }
      } else {
      $this->session->set_flashdata(
        'message',
        'failed'
      );
      redirect('transaction');
    }
  }

  public function bulk_transaction()
  {
      $asset_ids = $this->input->post('asset_id');
      $room_id = $this->input->post('room_id');

      $assets = $this->Assets->bulk_asset_transaction($asset_ids);
      $transactions = $this->Transactions->bulk_transaction($asset_ids, $room_id);
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
