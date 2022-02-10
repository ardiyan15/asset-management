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
    $result = [];
    $no = $_POST['start'];
    foreach ($list as $field) {
      $no++;
      $row = [];
      $row[] = $no;
      $row[] = $field->asset_name;
      $row[] = $field->merk;
      $row[] = $field->serial_number;
      $row[] = $field->name;
      $row[] = $field->created;
      $row[] = '<a href="' . site_url('asset/update/' . $field->id_asset) . '" class="btn btn-info btn-sm rounded mr-1"><i class="fas fa-edit"></i></a><a href="javascript:;" class="btn btn-danger btn-sm" onclick="hapus(' . $field->id_asset . ')"><i class="fas fa-trash-alt"></i></a>';;
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

    if ($this->form_validation->run() == false) {
      $this->session->set_flashdata('message', 'failed');
      redirect('asset');
    } else {
      $add = $this->Assets->add_asset_model($data);
      if ($add > 0) {
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
    $id = $this->input->post('assetId');
    $result = $this->Assets->delete_ast_model($id);

    if ($result > 0) {
      echo json_encode(200);
    } else {
      echo json_encode(500);
    }
  }

  public function update($id)
  {
    $data['user']           = $this->Auth->get_active_user($this->session->userdata('username'));
    $data['title']          = 'Edit Aset';
    $data['asset']          = $this->Assets->get_asset_by_id($id);
    $data['serial_number']  = substr($data['asset']['serial_number'], 0, 4);

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
    $asset                = $this->Assets->get_asset_by_id($id);
    $remove_room_location = substr($asset['serial_number'], 4);
    $new_serial_number    = $serial_number . $remove_room_location;

    $data = [
      'asset_name'    => $this->input->post('nama'),
      'merk'          => $this->input->post('merk'),
      'serial_number' => $new_serial_number
    ];

    $result = $this->Assets->update_asset_by_id($data, $id);

    if ($result > 0) {
      $this->session->set_flashdata('message', 'edited ');
      redirect('asset');
    } else {
      $this->session->set_flashdata('message', 'Failed ');
      redirect('asset');
    }
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
    $file_mimes = ['application/vnd.ms-excel'];
    if (isset($_FILES['import']['name']) && in_array($_FILES['import']['type'], $file_mimes)) {

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
            'status' => 0,
            'placement_status' => 1,
            'created' => date('Y-m-d')
          ];

          $this->db->insert('asset', $result);
        }
      }
      $this->session->set_flashdata('message', 'added ');
      redirect('asset');
    } else {
      $this->session->set_flashdata('message', 'errorImport');
      redirect('asset');
    }
  }

  public function download()
  {
    force_download('csv_file/base_import_format.xls', NULL);
  }

  public function print_report($room_id)
  {
    $data['room']    = $this->Rooms->get_single_room_by_id($room_id);
    $data['assets']  = $this->Assets->get_asset_by_room_id($room_id);
    $this->load->library('pdf');
    $this->pdf->setPaper('A4', 'portrait');
    $this->pdf->filename = 'laporan-aset-ruangan.pdf';
    $this->pdf->load_view('assets/report_asset', $data);
  }
}
