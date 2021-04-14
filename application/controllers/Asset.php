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
    is_logged_in();
    is_allowed();
  }

  public function index()
  {
    $data['user'] = $this->Auth->get_active_user($this->session->userdata('username'));
    $role_id = $this->session->userdata('role_id');
    if ($this->input->post('keyword')) {
      $data['assets'] = $this->Assets->search_data_model($data['user']['role_id'], $data['user']['building_id']);
    } else {
      $data['assets'] = $this->Assets->menu_model($role_id, $data['user']['building_id']);
    }

    // if($this->input->post('keyword')) {
    //     $data['total_asset'] = $this->Assets->search_data_model($data['user']['user_code'], $data['user']['building_id']);
    //     $data['amount_data'] = count($data['total_asset']);
    // } else {
    //     $data['amount_data'] = $this->Assets->get_total_row('IT', $data['user']['user_code']);
    // }

    $data['title']      = 'Daftar Aset';
    $result['error']    = "Data Not Found!";
    $data['rooms']      = $this->Rooms->get_all_rooms();
    $data['categories'] = $this->Categories->get_all_categories();
    $data['buildings']  = $this->Buildings->get_all_buildings();

    if ($data['assets']) {
      $this->load->view('templates/header', $data);
      $this->load->view('templates/sidebar', $data);
      $this->load->view('templates/topbar', $data);
      $this->load->view('menu/index', $data);
      $this->load->view('templates/footer');
    } else {
      $this->load->view('templates/header', $data);
      $this->load->view('templates/sidebar', $data);
      $this->load->view('templates/topbar', $data);
      $this->load->view('menu/index', $result);
      $this->load->view('templates/footer');
    }
  }

  public function add_asset()
  {
    $this->form_validation->set_rules('name', 'Name', 'required|trim');
    $this->form_validation->set_rules('merk', 'Merk', 'required|trim');
    // $this->form_validation->set_rules('sn', 'Serial Number', 'required|trim');
    $this->form_validation->set_rules('loc', 'Location', 'required');

    $roomId       = $this->input->post('loc');
    $location     = $this->Assets->get_name_location($roomId)['name'];
    $category     = $this->input->post('category');
    $codeLocation = $this->Assets->get_code_location($location)[0];
    $prefix       = substr($codeLocation['name'], 7, 1);
    $last_sn      = $this->Assets->get_last_asset_serial_number();

    if($last_sn == null) {
      $prefix_number    = str_pad(1, "5", 0, STR_PAD_LEFT);
      $serial_number    = $location.$category.date("y").date("m").$prefix_number;
    } else {
      $last_number          = substr($last_sn['serial_number'], 11);
      $convert_to_integer   = (int)$last_number;
      $generate_number      = sprintf("%'.05d", $convert_to_integer+1);
      $serial_number        = $location.$category.date("y").date("m").$generate_number;
    }
    
    $final_serial_number = str_replace('-', "", $serial_number);

    $data = [
      'asset_name'        => $this->input->post('name'),
      'merk'              => $this->input->post('merk'),
      'qty'               => 1,
      'serial_number'     => $final_serial_number,
      'room_id'           => $this->input->post('loc'),
      'status'            => 0,
      'placement_status'  => 1,
      'created'           => date('Y-m-d')
    ];

    if ($this->form_validation->run() == false) {
      $this->session->set_flashdata('message','failed');
      redirect('asset');
    } else {
      $add = $this->Assets->add_asset_model($data);
      if ($add > 0) {
        $this->session->set_flashdata('message','added ');
        redirect('asset');
      } else {
        $this->session->set_flashdata('message','failed');
        redirect('asset');
      }
    }
  }

  public function delete($id)
  {
    $result = $this->Assets->delete_ast_model($id);

    if ($result > 0) {
      $this->session->set_flashdata('message','deleted');
      redirect('asset');
    } else {
      $this->session->set_flashdata('failed', 'failed ');
      redirect('asset');
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
    $new_serial_number    = $serial_number.$remove_room_location;

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
    $role_id         = $this->session->userdata('role_id'); 
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
}
