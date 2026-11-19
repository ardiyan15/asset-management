<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Asset extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('Assets_model', 'Assets');
    $this->load->model('Auth_model', 'Auth');
    $this->load->model('Rooms_model', 'Rooms');
    $this->load->model('Buildings_model', 'Buildings');
    $this->load->model('Categories_model', 'Categories');
    $this->load->helper(['url', 'download']);
    is_logged_in();
  }

  public function index()
  {
    $data['user'] = $this->Auth->get_active_user($this->session->userdata('username'));
    $data['title']      = 'Daftar Aset';
    $data['rooms']      = $this->Rooms->get_all_rooms();
    $data['categories'] = $this->Categories->get_all_categories();
    $data['buildings']  = $this->Buildings->get_all_buildings();

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('templates/topbar', $data);
    $this->load->view('menu/index');
    $this->load->view('templates/footer');

    unset($_SESSION['message']);
  }

  function get_data_asset()
  {
    $data['user'] = $this->Auth->get_active_user($this->session->userdata('username'));
    $role_id = $this->session->userdata('role_id');

    $list = $this->Assets->get_datatables($role_id, $data['user']['building_id']);

    // Thumbnail foto utama untuk baris di halaman ini saja
    $this->load->model('Photo_model', 'Photos');
    $thumbs = $this->Photos->primaries(array_column($list, 'id_asset'));
    $result = [];
    $no = (int) $this->input->post('start');
    foreach ($list as $field) {
      $no++;
      $row = [];
      $row[] = $no;
      $thumb = $thumbs[$field->id_asset] ?? null;
      $row[] = ($thumb
        ? '<img src="' . Photo_model::url($thumb['asset_id'], $thumb['thumb_name']) . '" alt="" loading="lazy" class="rounded mr-2" style="width:36px;height:36px;object-fit:cover;vertical-align:middle;">'
        : '<span class="d-inline-flex align-items-center justify-content-center rounded bg-light text-gray-400 mr-2" style="width:36px;height:36px;vertical-align:middle;"><i class="fas fa-image"></i></span>')
        . '<a href="' . site_url('asset/detail/' . $field->id_asset) . '" class="font-weight-bold">' . html_escape($field->asset_name) . '</a>';
      $row[] = $field->merk;
      $row[] = $field->serial_number;
      $row[] = $field->name;
      $row[] = asset_condition_badge($field->asset_condition)
        . ((int) $field->asset_condition === 3
          ? ' <a href="' . site_url('disposal/create?asset=' . $field->id_asset) . '" class="badge badge-light text-danger" title="Ajukan penghapusan aset ini"><i class="fas fa-recycle"></i> Ajukan hapus</a>'
          : '');
      $row[] = $field->created;
      $row[] = '<a href="' . site_url('asset/detail/' . $field->id_asset) . '" class="btn btn-primary btn-sm rounded mr-1" title="Detail aset"><i class="fas fa-eye"></i></a>'
        . '<a href="javascript:;" class="btn btn-warning btn-sm rounded mr-1 js-condition" title="Ubah kondisi"'
        . ' data-id="' . $field->id_asset . '" data-condition="' . (int) $field->asset_condition . '"'
        . ' data-name="' . html_escape($field->asset_name . ' (' . $field->serial_number . ')') . '"><i class="fas fa-heartbeat"></i></a>'
        . '<a href="' . site_url('scan/' . rawurlencode($field->serial_number)) . '" target="_blank" class="btn btn-secondary btn-sm rounded mr-1" title="Halaman scan QR"><i class="fas fa-qrcode"></i></a>'
        . '<a href="' . site_url('asset/print_labels?ids=' . $field->id_asset) . '" target="_blank" class="btn btn-dark btn-sm rounded mr-1" title="Cetak label QR"><i class="fas fa-print"></i></a>'
        . '<a href="' . site_url('asset/update/' . $field->id_asset) . '" class="btn btn-info btn-sm rounded mr-1"><i class="fas fa-edit"></i></a><a href="javascript:;" class="btn btn-danger btn-sm" onclick="hapus(' . $field->id_asset . ')"><i class="fas fa-trash-alt"></i></a>';
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

  public function add_asset()
  {
    $this->form_validation->set_rules('name', 'Name', 'required|trim');
    $this->form_validation->set_rules('merk', 'Merk', 'required|trim');
    $this->form_validation->set_rules('loc', 'Location', 'required');

    $roomId       = $this->input->post('loc');
    $location     = $this->Assets->get_name_location($roomId)['name'];
    $category     = $this->input->post('category');
    $qty          = $this->input->post('qty');
    $last_sn      = $this->Assets->get_last_asset_serial_number();

    if ($last_sn == null) {
      $prefix_number = str_pad(1, "5", 0, STR_PAD_LEFT);
      $serial_number = $location . $category . date("y") . date("m") . $prefix_number;
      if ($qty > 1) {
        $number = 1;
        for ($i = 0; $i < $qty; $i++) {
          $prefix_number = str_pad($number, "5", 0, STR_PAD_LEFT);
          $result[] = [
            "asset_name"         => $this->input->post('name'),
            'merk'               => $this->input->post('merk'),
            'serial_number'      => str_replace("-", "", $location . $category . date('y') . date("m") . $prefix_number),
            'room_id'            => $this->input->post('loc'),
            'status'             => 0,
            'placement_status'   => 1,
            'created'            => date('Y-m-d')
          ];
          $number++;
        }
      }
    } else {
      $string_length = strlen($last_sn['serial_number']);
      // if room's name is equal 5 character, e.g L-201
      if ($string_length === 15 || $string_length === 17) {
        $last_number = substr($last_sn['serial_number'], 12);
        // if room's name is equal 6 character, e.g Lobby
      } else if ($string_length === 16) {
        $last_number = substr($last_sn['serial_number'], 11);
        // if room's name is less than 5 and 6 character, e.g H-U
      } else if ($string_length === 14) {
        $last_number = substr($last_sn['serial_number'], 10);
      } else {
        $last_number = substr($last_sn['serial_number'], 9);
      }

      $convert_to_integer = (int)$last_number;
      $generate_number = sprintf("%'.05d", $convert_to_integer + 1);
      $serial_number = $location . $category . date("y") . date("m") . $generate_number;
      if ($qty > 1) {
        for ($i = 0; $i < $qty; $i++) {
          $prefix_number = str_pad($generate_number, "5", 0, STR_PAD_LEFT);
          $result[] = [
            "asset_name"         => $this->input->post('name'),
            'merk'               => $this->input->post('merk'),
            'serial_number'      => str_replace("-", "", $location . $category . date('y') . date("m") . $prefix_number),
            'room_id'            => $this->input->post('loc'),
            'status'             => 0,
            'placement_status'   => 1,
            'created'            => date('Y-m-d')
          ];
          $generate_number++;
        }
      }
    }

    $final_serial_number = str_replace('-', "", $serial_number);

    $data = [
      'asset_name'        => $this->input->post('name'),
      'merk'              => $this->input->post('merk'),
      'serial_number'     => $final_serial_number,
      'room_id'           => $this->input->post('loc'),
      'status'            => 0,
      'placement_status'  => 1,
      'created'           => date('Y-m-d')
    ];

    if ($qty > 1) {
      $data = $result;
    }

    // Kondisi awal aset (default Baik)
    $condition = (int) $this->input->post('condition');
    $condition = is_valid_asset_condition($condition) ? $condition : 1;
    if ($qty > 1) {
      foreach ($data as &$row) {
        $row['asset_condition'] = $condition;
      }
      unset($row);
    } else {
      $data['asset_condition'] = $condition;
    }

    // Data pembelian (opsional) + kategori, sama untuk semua unit yang dibuat
    $purchase = $this->_purchase_input();
    if ($purchase === false) {
      $this->session->set_flashdata('message', 'purchase_invalid');
      redirect('asset');
    }
    $category_row        = $this->Categories->get_active_category_by_code($category);
    $purchase['category_id'] = $category_row ? (int) $category_row['id'] : null;
    if ($qty > 1) {
      foreach ($data as &$row) {
        $row = array_merge($row, $purchase);
      }
      unset($row);
    } else {
      $data = array_merge($data, $purchase);
    }

    if ($this->form_validation->run() == false) {
      $this->session->set_flashdata('message', 'failed');
      redirect('asset');
    } else {
      $add = $this->Assets->add_asset_model($data);
      if ($add > 0) {
        $rows    = $qty > 1 ? $data : [$data];
        $serials = array_column($rows, 'serial_number');
        $this->audit->log('asset.create', 'Tambah ' . count($rows) . ' aset "' . $rows[0]['asset_name'] . '" (' . implode(', ', $serials) . ')', [
          'entity_type'  => 'asset',
          'entity_id'    => count($rows) === 1 ? $this->db->insert_id() : null,
          'entity_label' => count($rows) === 1 ? $serials[0] : $serials[0] . ' s.d. ' . end($serials),
          'building_id'  => $this->Rooms->get_building_id_by_room($roomId),
          'changes'      => ['jumlah' => ['old' => null, 'new' => count($rows)], 'ruangan' => ['old' => null, 'new' => $location]],
        ]);
        $this->session->set_flashdata('message', 'added ');
        redirect('asset');
      } else {
        $this->session->set_flashdata('message', 'failed');
        redirect('asset');
      }
    }
  }

  public function delete()
  {
    $id    = (int) $this->input->post('assetId');
    $user  = $this->Auth->get_active_user($this->session->userdata('username'));
    $scope = $id ? $this->Assets->get_asset_with_building($id) : null;

    // Hapus permanen hanya untuk admin atau aset di gedung user sendiri
    if (!$scope || ($user['role_id'] !== '1' && (string) $user['building_id'] !== (string) $scope['building_id'])) {
      echo json_encode(403);
      return;
    }

    $before = $this->Assets->get_asset_by_id($id);
    $result = $this->Assets->delete_ast_model($id);

    if ($result > 0) {
      $this->load->model('Photo_model', 'Photos');
      $this->Photos->delete_all_for_asset($id);
      $this->audit->log('asset.delete', 'Hapus permanen aset "' . $before['asset_name'] . '"', [
        'entity_type'  => 'asset',
        'entity_id'    => $id,
        'entity_label' => $before['serial_number'],
        'building_id'  => $scope['building_id'],
        'changes'      => Audit::diff($before, array_fill_keys(['asset_name', 'merk', 'serial_number', 'room_id', 'asset_condition', 'purchase_price'], null)),
      ]);
      echo json_encode(200);
    } else {
      echo json_encode(500);
    }
  }

  /**
   * Halaman detail aset (hanya lihat): ringkasan, riwayat gabungan, foto, maintenance & peminjaman,
   * plus tombol aksi sesuai status aset. Admin semua gedung; user hanya aset di gedungnya.
   */
  public function detail($id = null)
  {
    $user  = $this->Auth->get_active_user($this->session->userdata('username'));
    $asset = $this->Assets->get_asset_detail((int) $id);

    if (!$asset || ($user['role_id'] !== '1' && (string) $user['building_id'] !== (string) $asset['building_id'])) {
      $this->session->set_flashdata('message', 'failed ');
      redirect('asset');
    }

    $this->load->model('Photo_model', 'Photos');
    $this->load->model('Asset_history_model', 'History');
    $this->load->model('Loan_model', 'Loan');
    $this->load->model('Maintenance_model', 'Maintenance');
    $this->load->model('Disposal_model', 'Disposal');
    $this->load->helper(['loan', 'maintenance']);
    $this->load->library('qr');

    $asset_id    = (int) $asset['id_asset'];
    $is_disposed = (int) $asset['is_disposed'] === 1;

    $open_loan = $this->Loan->open_loan_for_asset($asset_id);
    if ($open_loan) {
      $open_loan['state'] = loan_status($open_loan);
    }
    $disposal = $this->Disposal->get_latest_for_asset($asset_id);
    $disposal_pending = $disposal && (int) $disposal['status'] === Disposal_model::STATUS_PENDING;

    // Kiriman yang sedang menunggu diterima (aset berstatus "sedang dipindahkan")
    $pending_transfer = (int) $asset['placement_status'] !== 1
      ? $this->db->select('dst.name AS dest_name, t.transaction_id AS code')
        ->from('transaction_details AS td')
        ->join('transaction AS t', 't.id = td.transaction_id', 'left')
        ->join('rooms AS dst', 'dst.id = td.room_id', 'left')
        ->where(['td.asset_id' => $asset_id, 'td.status' => 0])
        ->order_by('td.id', 'DESC')->limit(1)->get()->row_array()
      : null;

    $schedules = $this->Maintenance->schedules_for_asset($asset_id);
    foreach ($schedules as &$s) {
      $s['due'] = maintenance_due_status($s['next_due_date'], $s['remind_days_before'], $s['is_active']);
    }
    unset($s);

    $data = [
      'title'            => 'Detail Aset',
      'user'             => $user,
      'asset'            => $asset,
      'is_disposed'      => $is_disposed,
      'qr_image'         => $this->qr->png_data_uri($this->qr->asset_url($asset['serial_number']), 4),
      'depreciation'     => asset_depreciation($asset['purchase_price'], $asset['purchase_date'], $asset['useful_life_years'], $asset['residual_percent'] ?? 0),
      'warranty'         => warranty_status($asset['warranty_until']),
      'photos'           => $this->Photos->for_asset($asset_id),
      'timeline'         => $this->History->timeline($asset),
      'timeline_types'   => Asset_history_model::TYPES,
      'open_loan'        => $open_loan,
      'loan_history'     => $this->Loan->history_for_asset($asset_id, 20),
      'disposal'         => $disposal,
      'pending_transfer' => $pending_transfer,
      'schedules'        => $schedules,
      'maintenance_logs' => $this->Maintenance->logs_for_asset($asset_id, 20),
      'maintenance_cost' => $this->Maintenance->total_cost_for_asset($asset_id),
      'rooms'            => $this->Rooms->get_all_rooms(),
      // Aksi yang boleh tampil sesuai status aset
      'can'              => [
        'edit'    => !$is_disposed,
        'move'    => !$is_disposed && (int) $asset['placement_status'] === 1 && !$open_loan && !$disposal_pending,
        'lend'    => !$is_disposed && (bool) $this->Loan->available_assets($user['role_id'], $user['building_id'], $asset_id),
        'dispose' => !$is_disposed && (bool) $this->Disposal->get_eligible_assets($user['role_id'], $user['building_id'], [$asset_id]),
      ],
    ];

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('templates/topbar', $data);
    $this->load->view('assets/detail', $data);
    $this->load->view('templates/footer');
  }

  public function update($id)
  {
    $data['user']           = $this->Auth->get_active_user($this->session->userdata('username'));
    $data['title']          = 'Edit Aset';
    $data['asset']          = $this->_editable_asset($id, $data['user']);
    $data['serial_number']  = substr($data['asset']['serial_number'], 0, 4);
    $data['categories']     = $this->Categories->get_all_categories();

    $this->load->model('Photo_model', 'Photos');
    $data['photos']         = $this->Photos->for_asset($id);

    // Garansi disimpan sebagai tanggal; form memakai jumlah bulan sejak tanggal perolehan
    $data['warranty_months'] = '';
    if ($data['asset']['purchase_date'] && $data['asset']['warranty_until']) {
      $diff = (new DateTime($data['asset']['purchase_date']))->diff(new DateTime($data['asset']['warranty_until']));
      $data['warranty_months'] = $diff->y * 12 + $diff->m;
    }

    $category = $data['asset']['category_id'] ? $this->Categories->get_category_by_id($data['asset']['category_id']) : null;
    $data['depreciation'] = asset_depreciation(
      $data['asset']['purchase_price'],
      $data['asset']['purchase_date'],
      $category['useful_life_years'] ?? null,
      $category['residual_percent'] ?? 0
    );

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('templates/topbar', $data);
    $this->load->view('menu/update', $data);
    $this->load->view('templates/footer');
  }


  public function edit_asset()
  {
    $this->form_validation->set_rules('name', 'Name', 'required|trim');
    $this->form_validation->set_rules('merk', 'Merk', 'required|trim');
    $this->form_validation->set_rules('seri', 'Serial Number', 'required|trim');

    $id                   = $this->input->post('id');
    $serial_number        = $this->input->post('seri');
    $user                 = $this->Auth->get_active_user($this->session->userdata('username'));
    $asset                = $this->_editable_asset($id, $user);
    $remove_room_location = substr($asset['serial_number'], 4);
    $new_serial_number    = $serial_number . $remove_room_location;

    $purchase = $this->_purchase_input();
    if ($purchase === false) {
      $this->session->set_flashdata('message', 'purchase_invalid');
      redirect('asset/update/' . (int) $id);
    }

    $category_id = (int) $this->input->post('category_id');

    $data = array_merge([
      'asset_name'    => $this->input->post('nama'),
      'merk'          => $this->input->post('merk'),
      'serial_number' => $new_serial_number,
      'category_id'   => $category_id > 0 ? $category_id : null,
    ], $purchase);

    $result = $this->Assets->update_asset_by_id($data, $id);

    // affected_rows 0 = tidak ada perubahan, bukan error
    if ($result > 0 || $this->db->error()['code'] == 0) {
      $changes = Audit::diff($asset, $data);
      if ($changes) {
        $this->audit->log('asset.update', 'Ubah data aset "' . $data['asset_name'] . '"', [
          'entity_type'  => 'asset',
          'entity_id'    => (int) $id,
          'entity_label' => $new_serial_number,
          'building_id'  => $this->audit->building_of_asset($id),
          'changes'      => $changes,
        ]);
      }
      $this->session->set_flashdata('message', 'edited ');
      redirect('asset');
    } else {
      $this->session->set_flashdata('message', 'Failed ');
      redirect('asset');
    }
  }

  private function _category_id_by_code($code)
  {
    $category = $this->Categories->get_active_category_by_code(trim((string) $code));
    return $category ? (int) $category['id'] : null;
  }

  /**
   * Aset yang boleh diedit user: ada, belum dihapus, dan (non-admin) di gedungnya sendiri.
   */
  private function _editable_asset($id, $user)
  {
    $scoped = $this->Assets->get_asset_with_building((int) $id);
    $allowed = $scoped && (int) $scoped['is_disposed'] === 0
      && ($user['role_id'] === '1' || (string) $user['building_id'] === (string) $scoped['building_id']);

    if (!$allowed) {
      $this->session->set_flashdata('message', 'failed ');
      redirect('asset');
    }
    return $this->Assets->get_asset_by_id((int) $id);
  }

  /**
   * Input data pembelian dari form tambah/edit aset.
   * Harga diterima sebagai angka rupiah bulat (titik/koma pemisah ribuan dibuang).
   * Garansi diisi dalam bulan sejak tanggal perolehan, disimpan sebagai tanggal akhir garansi.
   *
   * @return array|false false kalau ada input yang tidak valid
   */
  private function _purchase_input()
  {
    $date   = trim((string) $this->input->post('purchase_date'));
    $price  = preg_replace('/[^0-9]/', '', (string) $this->input->post('purchase_price'));
    $vendor = trim((string) $this->input->post('vendor', true));
    $months = trim((string) $this->input->post('warranty_months'));

    if ($date !== '') {
      $parsed = DateTime::createFromFormat('Y-m-d', $date);
      if (!$parsed || $parsed->format('Y-m-d') !== $date || $date > date('Y-m-d')) {
        return false;
      }
    }
    if ($months !== '' && (!ctype_digit($months) || (int) $months > 240 || $date === '')) {
      return false;
    }
    if (strlen($price) > 13) {
      return false;
    }

    return [
      'purchase_date'  => $date !== '' ? $date : null,
      'purchase_price' => $price !== '' ? $price : null,
      'vendor'         => $vendor !== '' ? mb_substr($vendor, 0, 100) : null,
      'warranty_until' => ($date !== '' && (int) $months > 0)
        ? (new DateTime($date))->modify('+' . (int) $months . ' months')->format('Y-m-d')
        : null,
    ];
  }

  public function location($room_id)
  {
    $data['room_id'] = $room_id;
    $data['room']    = $this->Rooms->get_single_room_by_id($room_id);
    $data['user']    = $this->Auth->get_active_user($this->session->userdata('username'));
    $data['assets']  = $this->Assets->get_asset_by_room_id($room_id);

    if ($this->input->post('keyword')) {
      $data['assets'] = $this->Assets->search_data_model($data['user']['role_id'], $data['user']['building_id']);
    }

    $data['rooms']        = $this->Rooms->get_all_rooms();
    $data['title']        = "Lokasi Asset";
    $data['error']        = "Data Not Found!";

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('templates/topbar', $data);
    $this->load->view('assets/index', $data);
    $this->load->view('templates/footer');
  }

  public function import()
  {
    // $file_mimes = ['application/vnd.ms-excel'];
    $file_mimes = ['text/csv'];
    if (isset($_FILES['import']['name']) && in_array($_FILES['import']['type'], $file_mimes)) {
      $count_before = $this->db->count_all('asset');

      $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
      $spreadsheet = $reader->load($_FILES['import']['tmp_name']);
      $sheetData = $spreadsheet->getActiveSheet()->toArray();

      for ($i = 1; $i < count($sheetData); $i++) {
        if ($sheetData[$i][4] > 1) {
          $number = 1;

          for ($j = 0; $j < $sheetData[$i][4]; $j++) {
            $last_sn = $this->Assets->get_last_asset_serial_number();

            if ($last_sn == null) {
              $prefix_number = str_pad($number, "5", 0, STR_PAD_LEFT);
              $serial_number = str_replace("-", "", $sheetData[$i][3] . $sheetData[$i][2] . date("y") . date("m") . $prefix_number);
            } else {
              $string_length = strlen($last_sn['serial_number']);
              // if room's name is equal 5 character, e.g L-201
              if ($string_length === 15 || $string_length === 17) {
                $last_number = substr($last_sn['serial_number'], 12);
                // if room's name is equal 6 character, e.g Lobby
              } else if ($string_length === 16) {
                $last_number = substr($last_sn['serial_number'], 11);
                // if room's name is less than 5 and 6 character, e.g H-U
              } else if ($string_length === 14) {
                $last_number = substr($last_sn['serial_number'], 10);
              } else {
                $last_number = substr($last_sn['serial_number'], 11);
              }
              $convert_to_integer = (int)$last_number;
              $generate_number = sprintf("%'.05d", $convert_to_integer + 1);
              $serial_number = str_replace("-", "", $sheetData[$i][3] . $sheetData[$i][2] . date("y") . date("m") . $generate_number);
            }

            $prefix_number = str_pad($number, "5", 0, STR_PAD_LEFT);
            $room_id = $this->Rooms->get_room_id_by_name($sheetData[$i][3])['id'];
            $result = [
              "asset_name"         => $sheetData[$i][0],
              'merk'               => $sheetData[$i][1],
              'serial_number'      => $serial_number,
              'room_id'            => $room_id,
              'category_id'        => $this->_category_id_by_code($sheetData[$i][2]),
              'status'             => 0,
              'placement_status'   => 1,
              'created'            => date('Y-m-d')
            ];
            $number++;
            $this->db->insert('asset', $result);
          }
        } else {
          $last_sn = $this->Assets->get_last_asset_serial_number();

          if ($last_sn == null) {
            $generate_number = 1;
          } else {
            $string_length = strlen($last_sn['serial_number']);
            // if room's name is equal 5 character, e.g L-201
            if ($string_length === 15 || $string_length === 17) {
              $last_number = substr($last_sn['serial_number'], 12);
            } else if ($string_length === 14) {
              $last_number = substr($last_sn['serial_number'], 10);
            } else {
              $last_number = substr($last_sn['serial_number'], 11);
            }
            $convert_to_integer = (int)$last_number;
            $generate_number = sprintf("%'.05d", $convert_to_integer + 1);
          }
          $suffix_number = str_pad($generate_number, "5", 0, STR_PAD_LEFT);
          $serial_number = $sheetData[$i][3] . $sheetData[$i][2] . date("y") . date("m") . $suffix_number;
          $final_serial_number = str_replace('-', "", $serial_number);

          $room_id = $this->Rooms->get_room_id_by_name($sheetData[$i][3])['id'];

          $result = [
            'asset_name' => $sheetData[$i][0],
            'merk' => $sheetData[$i][1],
            'serial_number' => $final_serial_number,
            'room_id' => $room_id,
            'category_id' => $this->_category_id_by_code($sheetData[$i][2]),
            'status' => 0,
            'placement_status' => 1,
            'created' => date('Y-m-d')
          ];
          $this->db->insert('asset', $result);
        }
      }
      $imported = $this->db->count_all('asset') - $count_before;
      $this->audit->log('asset.import', 'Import ' . $imported . ' aset dari file CSV "' . $_FILES['import']['name'] . '"', [
        'entity_type' => 'asset',
        'changes'     => ['jumlah' => ['old' => null, 'new' => $imported]],
      ]);
      $this->session->set_flashdata('message', 'added ');
      redirect('asset');
    } else {
      $this->session->set_flashdata('message', 'errorImport');
      redirect('asset');
    }
  }

  public function download()
  {
    force_download('csv_file/base_import_format.csv', NULL);
  }

  /**
   * Ubah kondisi aset + catat log.
   * AJAX (daftar aset) → JSON. Form biasa (halaman scan QR) → redirect balik ke scan/<serial>.
   * Non-admin hanya boleh mengubah aset di gedungnya sendiri.
   */
  public function update_condition()
  {
    $user      = $this->Auth->get_active_user($this->session->userdata('username'));
    $asset_id  = (int) $this->input->post('asset_id');
    $condition = (int) $this->input->post('condition');
    $notes     = (string) $this->input->post('notes', true);
    $asset     = $asset_id ? $this->Assets->get_asset_with_building($asset_id) : null;
    $is_ajax   = $this->input->is_ajax_request();

    $allowed = $asset && (int) $asset['is_disposed'] === 0
      && ($user['role_id'] === '1' || (string) $user['building_id'] === (string) $asset['building_id']);

    if (!$allowed || !is_valid_asset_condition($condition)) {
      $status  = 'failed';
      $message = !$allowed ? 'Aset tidak ditemukan atau di luar gedung Anda.' : 'Kondisi tidak valid.';
    } else {
      $source  = $is_ajax ? 'manual' : 'scan';
      $status  = $this->Assets->update_condition($asset_id, $condition, $notes, $user['id_user'], $source);
      $messages = [
        'updated'   => 'Kondisi ' . $asset['asset_name'] . ' diubah menjadi ' . asset_condition_label($condition) . '.',
        'unchanged' => 'Kondisi ' . $asset['asset_name'] . ' sudah ' . asset_condition_label($condition) . '.',
        'failed'    => 'Gagal menyimpan kondisi aset.',
      ];
      $message = $messages[$status];

      if ($status === 'updated') {
        $this->audit->log('asset.condition', 'Ubah kondisi "' . $asset['asset_name'] . '" menjadi ' . asset_condition_label($condition), [
          'entity_type'  => 'asset',
          'entity_id'    => $asset_id,
          'entity_label' => $asset['serial_number'],
          'building_id'  => $asset['building_id'],
          'changes'      => [
            'kondisi'    => ['old' => asset_condition_label($asset['asset_condition']), 'new' => asset_condition_label($condition)],
            'keterangan' => ['old' => null, 'new' => trim($notes) ?: null],
          ],
        ]);
      }
    }

    if ($is_ajax) {
      $this->output->set_content_type('application/json')->set_output(json_encode([
        'status'  => $status,
        'message' => $message,
        'badge'   => $status === 'failed' ? null : asset_condition_badge($condition),
      ]));
      return;
    }

    $this->session->set_flashdata('condition_scan', ['status' => $status, 'message' => $message]);
    redirect($asset ? 'scan/' . rawurlencode($asset['serial_number']) : 'asset');
  }

  /**
   * Cetak label QR aset ke PDF (A4, 3 kolom x 7 baris).
   *  - asset/print_labels/<room_id>        : semua aset di ruangan
   *  - asset/print_labels?ids=1,2,3         : aset terpilih (GET/POST, boleh array ids[])
   * Non-admin hanya bisa mencetak aset di gedungnya sendiri.
   */
  public function print_labels($room_id = null)
  {
    $user = $this->Auth->get_active_user($this->session->userdata('username'));

    $ids = $this->input->get_post('ids');
    if (is_string($ids)) {
      $ids = explode(',', $ids);
    }
    $ids = array_values(array_filter(array_map('intval', (array) $ids)));

    if ($room_id === null && empty($ids)) {
      $this->session->set_flashdata('message', 'labelEmpty');
      redirect('asset');
    }

    $room_id = $room_id !== null ? (int) $room_id : null;
    $assets  = $this->Assets->get_label_assets($user['role_id'], $user['building_id'], $room_id, $ids);

    if (empty($assets)) {
      $this->session->set_flashdata('message', 'labelEmpty');
      redirect($room_id !== null ? 'asset/location/' . $room_id : 'asset');
    }

    $this->load->library('qr');
    foreach ($assets as &$asset) {
      $asset['qr'] = $this->qr->png_data_uri($this->qr->asset_url($asset['serial_number']), 5);
    }
    unset($asset);

    $data['assets'] = $assets;
    $data['title']  = 'Label QR Aset';

    $this->audit->log('asset.print_labels', 'Cetak ' . count($assets) . ' label QR' . ($room_id !== null ? ' ruangan ' . ($assets[0]['room_name'] ?? '') : ''), [
      'entity_type'  => $room_id !== null ? 'room' : 'asset',
      'entity_id'    => $room_id,
      'entity_label' => count($assets) === 1 ? $assets[0]['serial_number'] : null,
    ]);

    $this->load->library('pdf');
    $this->pdf->setPaper('A4', 'portrait');
    $this->pdf->filename = 'label-qr-aset-' . date('YmdHis') . '.pdf';
    $this->pdf->load_view('assets/print_labels', $data);
  }

  public function print_report($room_id)
  {
    $data['room']    = $this->Rooms->get_single_room_by_id($room_id);
    $data['assets']  = $this->Assets->get_asset_by_room_id($room_id);
    $this->audit->log('report.export', 'Cetak laporan aset ruangan ' . ($data['room']['name'] ?? '') . ' (PDF)', [
      'entity_type'  => 'room',
      'entity_id'    => (int) $room_id,
      'entity_label' => $data['room']['name'] ?? null,
      'building_id'  => $this->Rooms->get_building_id_by_room($room_id),
    ]);
    $this->load->library('pdf');
    $this->pdf->setPaper('A4', 'portrait');
    $this->pdf->filename = 'laporan-aset-ruangan.pdf';
    $this->pdf->load_view('assets/report_asset', $data);
  }
}
