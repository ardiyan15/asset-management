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
      // $this->_sendEmail($fullname, 'success', $email);
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


  // private function _sendEmail($fullname, $type, $email)
  // {
  //   $config = array();
  //   $config['protocol']  = 'smtp';
  //   $config['smtp_host'] = 'ssl://smtp.googlemail.com';
  //   $config['smtp_user'] = 'ardhiyan15@gmail.com';
  //   $config['smtp_pass'] = 'fingerstyle';
  //   $config['smtp_port'] = 465;
  //   $config['mailtype']  = 'html';
  //   $config['charset']   = 'utf-8';
  //   $this->email->initialize($config);
  //   $this->email->set_newline("\r\n");

  //   $this->load->library('email', $config);

  //   $this->email->from('ardhiyan15@gmail.com', 'Ardiyan Agus Prayogo');

  //   if ($type == 'success') {
  //     $this->email->to($email);
  //   } else {
  //     $this->email->to($this->input->post('email'));
  //   }

  //   if ($type == 'success') {
  //     $this->email->subject('Take In Asset');
  //     $this->email->message("<b>" . $fullname . "</b>" . ' 
  //     has received the asset you sent');
  //   }

  //   if ($this->email->send()) {
  //     return true;
  //   } else {
  //     echo $this->email->print_debugger();
  //     die;
  //   }
  // }
}
