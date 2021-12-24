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
    $this->load->model('Categories_model', 'Categories');
    $this->load->model('Buildings_model', 'Buildings');
  }

  function get_data_asset()
  {
    $data['user'] = $this->Auth->get_active_user($this->session->userdata('username'));
    $role_id = $this->session->userdata('role_id');

    $list = $this->Assets->get_datatables($role_id, $data['user']['building_id']);
    $result = [];
    $no = $_POST['start'];
    foreach ($list as $field) {
      $no++;
      $row = [];
      $row[] = $no;
      $row[] = $field->asset_name;
      $row[] = $field->merk;
      $row[] = $field->serial_number;
      if ($field->placement_status == 1) {
        $row[] = $field->name;
        $row[] = '<a href="javascript:;" class="btn btn-warning btn-sm rounded" data-toggle="modal" data-target="#takeout" onclick="takeout(' . $field->id_asset .  ','  . $field->room_id . ')">Pindahkan></a>';;
      } else {
        $row[] = "Sedang Dipindahkan";
        $row[] = null;
      }
      if ($field->placement_status == 1) {
        $row[] = '<input class="form-check-input" name="check" type="checkbox" value=' . $field->id_asset . '>';
      } else {
        $row[] = null;
      }
      $result[] = $row;
    }
    $data = [
      'title' => 'Daftar Aset',
      'rooms' => $this->Rooms->get_all_rooms(),
      'categories' => $this->Categories->get_all_categories(),
      'buldings' => $this->Buildings->get_all_buildings(),
      'draw' => $_POST['draw'],
      'recordsTotal' => $this->Assets->count_all($role_id, $data['user']['building_id']),
      'recordsFiltered' => $this->Assets->count_filtered($role_id, $data['user']['building_id']),
      'data' => $result
    ];

    echo json_encode($data);
  }

  public function index()
  {
    $data['user']       = $this->Auth->get_active_user($this->session->userdata('username'));
    $data['title']      = 'Transaksi Aset';
    $data['rooms']      = $this->Rooms->get_all_rooms();

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('templates/topbar', $data);
    $this->load->view('transaction/index', $data);
    $this->load->view('templates/footer');

    unset($_SESSION['message']);
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

    if ($transaction > 0) {
      if ($update_placement > 0) {
        $this->session->set_flashdata('message', 'added ');
        redirect('transaction');
      } else {
        $this->session->set_flashdata('message', 'failed');
        redirect('transaction');
      }
    } else {
      $this->session->set_flashdata('message', 'failed');
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
    if ($assets > 0) {
      if ($transactions > 0) {
        echo json_encode(1);
      } else {
        echo json_encode(0);
      }
    } else {
      echo json_encode(0);
    }
  }

  public function get_room_id($assetId)
  {
    echo $assetId;
  }
}
