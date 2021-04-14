<?php

class Category extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'Auth');
        $this->load->model('Categories_model', 'Categories');
        $this->load->model('Assets_model', 'Assets');
        is_logged_in();
        is_allowed();
    }

    public function index()
    {
        $data['user']       = $this->Auth->get_active_user($this->session->userdata('username'));
        $data['categories'] = $this->Categories->get_all_categories();
        $data['title']      = 'Daftar Kategori';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('categories/index', $data);
        $this->load->view('templates/footer');
    }

    public function store()
    {
        $code = $this->input->post('code');
        $name = $this->input->post('name');

        $data = [
            'code'          => $code,
            'name'          => $name,
            'status'        => 1,
            'created_at'    => date("Y-m-d H:i:s")
        ];

        $result = $this->Categories->create($data);

        if($result > 0) {
            $this->session->set_flashdata('message', 'addCategory');
            redirect('category');
        } else {
            $this->session->set_flashdata('message','failed ');
            redirect('category');
        }
    }

    public function delete($id)
    {
        $result = $this->Categories->delete_category_by_id($id);

        if($result > 0) {
            $this->session->set_flashdata('message', 'deleteCategory');
            redirect('category');
        } else {
            $this->session->set_flashdata('message','failed ');
            redirect('category');
        }
    }

    public function edit($id)
    {
        $data['user']       = $this->Auth->get_active_user($this->session->userdata('username'));
        $data['title']      = 'Ubah Kategori';
        $data['category']   = $this->Categories->get_category_by_id($id);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('categories/edit', $data);
        $this->load->view('templates/footer');
    }

    public function update()
    {
        $id     = $this->input->post('id');
        $name   = $this->input->post('name');
        $code   = $this->input->post('code');

        $data = [
            'id'            => $id,
            'code'          => $code,
            'name'          => $name,
            'updated_at'    => date("Y-m-d H:i:s")
        ];

        $result = $this->Categories->update_category_by_id($data);

        if($result > 0) {
            $this->session->set_flashdata('message', 'editCategory');
            redirect('category');
        } else {
            $this->session->set_flashdata('message','failed ');
            redirect('category');
        }
    }
}