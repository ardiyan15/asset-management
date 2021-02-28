<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TI extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('Assets_model', 'Assets');
  }

  public function index()
  {

    $session = $this->session->userdata('email');
    if ($session === null) {
      redirect('auth');
    }

    $data['title'] = "Take In";
    $data['user'] = $this->Assets->login_model($this->session->userdata('email'));
    $location = $data['user']['user_code'];
    $data['asset'] = $this->Assets->take_in_model($location);

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar', $data);
    $this->load->view('templates/topbar', $data);
    $this->load->view('TI/index', $data);
    $this->load->view('templates/footer');
  }

  public function acc($id)
  {
    $data['user'] = $this->Assets->login_model($this->session->userdata('email'));
    $data['asset'] = $this->Assets->acc_model($id);

    $data['process'] = [
      'id_detail_process' => $data['asset']['id_detail_process'],
      'asset_id' => $data['asset']['asset_id'],
      'source' => $data['asset']['source'],
      'destination' => $data['asset']['destination'],
      'createtime' => $data['asset']['createtime'],
      'acctime' => date('Y-m-d'),
      'deleted' => 1
    ];

    $id = $data['asset']['id_detail_process'];

    $location = $data['user']['user_code'];
    $fullname = $data['user']['fullname'];
    $email = $data['asset']['email'];
    $assetId = $data['asset']['asset_id'];

    if ($this->Assets->move_asset($location, $assetId, $id) > 0) {
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
