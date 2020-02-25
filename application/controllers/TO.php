<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TO extends CI_Controller
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

        $data['title'] = "Take Out";
        $data['user'] = $this->Assets->login_model($this->session->userdata('email'));
        $location = $data['user']['user_code'];
        $data['asset'] = $this->Assets->take_out_process($location);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('TO/index', $data);
        $this->load->view('templates/footer');
    }

    public function to_process($id)
    {

        $dataAst = [
            'asset_name' => $this->input->post('name'),
            'merk' => $this->input->post('merk'),
            'qty' => 1,
            'serial_number' => $this->input->post('sn'),
            'source' => $this->input->post('sendfrom'),
            'destination' => $this->input->post('loc'),
            'createtime' => date('Y-m-d')
        ];

        $dtlPrces = [
            'asset_id' => $this->input->post('id'),
            'source' => $this->input->post('source'),
            'destination' => $this->input->post('loc'),
            'createtime' => date('Y-m-d')
        ];

        if ($this->Assets->take_out_model($id, $dataAst, $dtlPrces)) {
            $this->session->set_flashdata(
                'message',
                'take out'
            );
            redirect('asset');
        } else {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Failed to take out your asset!.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>'
            );
            redirect('asset');
        }
    }

    public function print_page()
    {

        $data = $this->input->post('sndAst');

        $header['title'] = "Receipt Form";
        $result['asset'] = $this->Assets->print_model($data);
        $this->load->view('templates/header', $header);
        // var_dump($result['asset'][0]['destination']);
        // die;
        $this->load->view('TO/print', $result);
    }
}
