<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Asset extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('Assets_model', 'Assets');
    $this->load->model('Rooms_model', 'Rooms');
    $this->load->model('Buildings_model', 'Buildings');
    // is_logged_in();
  }

  public function index()
  {
    $data['user'] = $this->Assets->login_model($this->session->userdata('email'));
    if ($this->input->post('keyword')) {
      $data['assets'] = $this->Assets->search_data_model($data['user']['user_code']);
    } else {
      $data['assets'] = $this->Assets->menu_model($data['user']['user_code']);
    }

    if($this->input->post('keyword')) {
        $data['total_asset'] = $this->Assets->search_data_model($data['user']['user_code']);
        $data['amount_data'] = count($data['total_asset']);
    } else {
        $data['amount_data'] = $this->Assets->get_total_row($data['user']['user_code']);
    }

    $data['title'] = 'List Assets';
    $result['error'] = "Data Not Found!";
    $data['rooms'] = $this->Rooms->get_all_rooms();
    $data['buildings'] = $this->Buildings->get_all_buildings();

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

    $roomId = $this->input->post('loc');
    $location = $this->Assets->get_name_location($roomId)['name'];
    $codeLocation = $this->Assets->get_code_location($location)[0];
    $prefix = substr($codeLocation['name'], 7, 1);
    $last_sn = $this->Assets->get_last_asset_serial_number()[0];
    
    if($last_sn['serial_number'] === '') {
      $serialNumber = $location.date("Y").date("m").+1;
    } else {
      $lastNumber = (int)substr($last_sn['serial_number'], 11, 1);
      $serialNumber = $location.date("Y").date("m").+($lastNumber+1);
    }

    $data = [
      'asset_name' => $this->input->post('name'),
      'merk' => $this->input->post('merk'),
      'qty' => 1,
      'serial_number' => $serialNumber,
      // 'origin_location' => $this->input->post('origin'),
      'asset_location' => $this->input->post('loc'),
      'status' => 0
    ];

    if ($this->form_validation->run() == false) {
      $this->session->set_flashdata(
        'message',
        'failed'
      );
      redirect('asset');
    } else {
      $add = $this->Assets->add_asset_model($data);
      if ($add > 0) {
        $this->session->set_flashdata(
          'message',
          'added '
        );
        redirect('asset');
      } else {
        $this->session->set_flashdata(
          'message',
          'failed'
        );
        redirect('asset');
      }
    }
  }


  public function delete($id)
  {
    $result = $this->Assets->delete_ast_model($id);

    if ($result > 0) {
      $this->session->set_flashdata(
        'message',
        'deleted'
      );
      redirect('asset');
    } else {
      $this->session->set_flashdata(
        'failed',
        'failed '
      );
      redirect('asset');
    }
  }


  public function edit_asset($id)
  {
    $this->form_validation->set_rules('name', 'Name', 'required|trim');
    $this->form_validation->set_rules('merk', 'Merk', 'required|trim');
    $this->form_validation->set_rules('sn', 'Serial Number', 'required|trim');
    $this->form_validation->set_rules('loc', 'Location', 'required');

    $data = [
      'asset_name' => $this->input->post('name'),
      'merk' => $this->input->post('merk'),
      'qty' => 1,
      'serial_number' => $this->input->post('sn'),
      'asset_location' => $this->input->post('loc'),
      'status' => $this->input->post('status')
    ];

    $edit = $this->Assets->edit_asset_model($data, $id);

    if ($edit > 0) {
      $this->session->set_flashdata(
        'message',
        'edited '
      );
      redirect('asset');
    } else {
      $this->session->set_flashdata(
        'message',
        'Failed '
      );
      redirect('asset');
    }
  }
}
