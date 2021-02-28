<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Asset extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('Assets_model', 'Assets');
    is_logged_in();
  }

  public function index()
  {
    $this->load->library('pagination');

    $data['user'] = $this->Assets->login_model($this->session->userdata('email'));

    if ($this->input->post('keyword')) {
      $data['assets'] = $this->Assets->search_data_model($data['user']['user_code']);
    } else {
    $data['assets'] = $this->Assets->menu_model($data['user']['user_code']);
      // $config['base_url'] = base_url('asset/index'); //base url
      // $config['total_rows'] = $this->Assets->get_total_row($data['user']['user_code']); //total row
      // $config['per_page'] = 10;  //show record per halaman

      // $config['full_tag_open'] = '<nav><ul class ="pagination">';
      // $config['full_tag_close'] = '</ul></nav>';

      // $config['first_link'] = 'First';
      // $config['first_tag_open'] = '<li class="page-item">';
      // $config['first_tag_close'] = '</li>';

      // $config['last_link'] = 'Last';
      // $config['last_tag_open'] = '<li class="page-item">';
      // $config['last_tag_close'] = '</li>';

      // $config['next_link'] = '&raquo';
      // $config['next_tag_open'] = '<li class="page-item">';
      // $config['next_tag_close'] = '</li>';

      // $config['prev_link'] = '&laquo';
      // $config['prev_tag_open'] = '<li class="page-item">';
      // $config['prev_tag_close'] = '</li>';

      // $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
      // $config['cur_tag_close'] = '</a></li>';

      // $config['num_tag_open'] = '<li class="page-item">';
      // $config['num_tag_close'] = '</li>';

      // $config['attributes'] = array('class' => 'page-link');

      // $this->pagination->initialize($config);
      // $data['start'] = $this->uri->segment(3);
      // $data['page'] = $this->Assets->menu_model($data['user']['user_code'], $config['per_page'], $data['start']);
      // $data['assets'] = $this->Assets->menu_model($data['user']['user_code'], $config['per_page'], $data['start']);
    }

    $data['title'] = 'List Assets';
    $result['error'] = "Data Not Found!";
    // $data['pagination'] = $this->pagination->create_links();

    // $data['store'] = $this->Assets->get_all_store_location();

    if ($this->input->post('keyword')) {
      $data['total_asset'] = $this->Assets->search_data_model($data['user']['user_code']);
      $data['amount_data'] = count($data['total_asset']);
    } else {
      $data['amount_data'] = $this->Assets->get_total_row($data['user']['user_code']);
    }

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
    $this->form_validation->set_rules('sn', 'Serial Number', 'required|trim');
    $this->form_validation->set_rules('loc', 'Location', 'required');

    $data = [
      'asset_name' => $this->input->post('name'),
      'merk' => $this->input->post('merk'),
      'qty' => 1,
      'serial_number' => $this->input->post('sn'),
      'origin_location' => $this->input->post('origin'),
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
