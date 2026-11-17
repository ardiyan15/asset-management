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
    is_logged_in();
  }

  function get_data_asset()
  {
    $data['user'] = $this->Auth->get_active_user($this->session->userdata('username'));
    $role_id = $this->session->userdata('role_id');

    $list = $this->Assets->get_datatables($role_id, $data['user']['building_id']);
    $result = [];
    $no = (int) $this->input->post('start');
    foreach ($list as $field) {
      $no++;
      $row = [];
      $row[] = $no;
      $row[] = $field->asset_name;
      $row[] = $field->merk;
      $row[] = $field->serial_number;
      if ($field->placement_status == 1) {
        $row[] = $field->name;
        $row[] = '<a href="javascript:;" class="btn btn-primary btn-sm rounded" data-toggle="modal" data-target="#takeout" onclick="takeout(' . $field->id_asset . ',' . $field->room_id . ')">Pindahkan</a>';
        ;
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
      'draw' => (int) $this->input->post('draw'),
      'recordsTotal' => $this->Assets->count_all($role_id, $data['user']['building_id']),
      'recordsFiltered' => $this->Assets->count_filtered($role_id, $data['user']['building_id']),
      'data' => $result
    ];

    echo json_encode($data);
  }

  public function index()
  {
    $data['user'] = $this->Auth->get_active_user($this->session->userdata('username'));
    $data['title'] = 'Transaksi Aset';
    $data['rooms'] = $this->Rooms->get_all_rooms();
    $data['buildings'] = $this->Buildings->get_active_buildings('1');

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('templates/topbar', $data);
    $this->load->view('transaction/index', $data);
    $this->load->view('templates/footer');

    unset($_SESSION['message']);
  }

  public function store()
  {
    $count = $this->_move_assets(
      [$this->input->post('asset_id')],
      $this->input->post('room'),
      $this->input->post('note', true)
    );

    $this->session->set_flashdata('message', $count > 0 ? 'take out' : 'move_failed');
    redirect('transaction');
  }

  public function bulk_transaction()
  {
    // response: jumlah aset yang dipindahkan (0 = gagal)
    echo json_encode($this->_move_assets(
      (array) $this->input->post('asset_id'),
      $this->input->post('room_id'),
      $this->input->post('note', true)
    ));
  }

  /**
   * Pindahkan aset ke ruangan tujuan dalam satu transaksi (TRX/AST/...).
   * Aset yang sedang dipindahkan, sudah di ruangan tujuan, atau di luar gedung user dilewati.
   *
   * @return int jumlah aset yang masuk ke transaksi
   */
  private function _move_assets(array $asset_ids, $room_id, $note)
  {
    $asset_ids = array_values(array_unique(array_filter(array_map('strval', $asset_ids), 'ctype_digit')));
    $room_id   = ctype_digit((string) $room_id) ? (int) $room_id : 0;

    if (empty($asset_ids) || !$room_id || !$this->Rooms->get_single_room_by_id($room_id)) {
      return 0;
    }

    $user   = $this->Auth->get_active_user($this->session->userdata('username'));
    $assets = $this->Transactions->get_movable_assets($user['role_id'], $user['building_id'], $asset_ids, $room_id);

    $code = $this->Transactions->create_transaction($assets, $room_id, $note);
    if ($code) {
      $room    = $this->Rooms->get_single_room_by_id($room_id);
      $serials = array_column($this->db->select('serial_number')->where_in('id_asset', array_column($assets, 'id_asset'))->get('asset')->result_array(), 'serial_number');
      $this->audit->log('transaction.create', 'Kirim ' . count($assets) . ' aset ke ruangan ' . $room['name'] . ' (' . $code . ')', [
        'entity_type'  => 'transaction',
        'entity_label' => $code,
        'building_id'  => $this->audit->building_of_asset($assets[0]['id_asset']),
        'changes'      => [
          'aset'    => ['old' => null, 'new' => implode(', ', $serials)],
          'tujuan'  => ['old' => null, 'new' => $room['name']],
          'catatan' => ['old' => null, 'new' => $note ?: null],
        ],
      ]);
    }
    return $code ? count($assets) : 0;
  }

  public function get_room_id($assetId)
  {
    echo $assetId;
  }

  public function get_rooms_ajax()
  {
    $building_id = $this->input->post('building_id');
    $rooms = $this->Rooms->get_rooms_by_building($building_id);
    echo json_encode($rooms);
  }
}
