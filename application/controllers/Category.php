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
        $depr = $this->_depreciation_input();

        if ($depr === false) {
            $this->session->set_flashdata('message', 'depreciation_invalid');
            redirect('category');
        }

        $data = array_merge([
            'code'          => $code,
            'name'          => $name,
            'status'        => 1,
            'created_at'    => date("Y-m-d H:i:s")
        ], $depr);

        $result = $this->Categories->create($data);

        if($result > 0) {
            $this->audit->log('master.category_create', 'Tambah kategori ' . $data['code'] . ' - ' . $data['name'], [
                'entity_type' => 'category', 'entity_id' => $this->db->insert_id(), 'entity_label' => $data['code'],
            ]);
            $this->session->set_flashdata('message', 'addCategory');
            redirect('category');
        } else {
            $this->session->set_flashdata('message','failed ');
            redirect('category');
        }
    }

    public function delete($id)
    {
        $before = $this->Categories->get_category_by_id($id);
        $result = $this->Categories->delete_category_by_id($id);

        if($result > 0) {
            $this->audit->log('master.category_delete', 'Nonaktifkan kategori ' . ($before['code'] ?? '') . ' - ' . ($before['name'] ?? ''), [
                'entity_type' => 'category', 'entity_id' => (int) $id, 'entity_label' => $before['code'] ?? null,
            ]);
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
        $depr   = $this->_depreciation_input();

        if ($depr === false) {
            $this->session->set_flashdata('message', 'depreciation_invalid');
            redirect('category/edit/' . (int) $id);
        }

        $data = array_merge([
            'id'            => $id,
            'code'          => $code,
            'name'          => $name,
            'updated_at'    => date("Y-m-d H:i:s")
        ], $depr);

        $before = $this->Categories->get_category_by_id($id);
        $result = $this->Categories->update_category_by_id($data);

        if($result > 0) {
            $this->audit->log('master.category_update', 'Ubah kategori ' . $data['code'] . ' - ' . $data['name'], [
                'entity_type' => 'category', 'entity_id' => (int) $id, 'entity_label' => $data['code'],
                'changes'     => Audit::diff($before ?: [], $data, ['code', 'name', 'useful_life_years', 'residual_percent']),
            ]);
            $this->session->set_flashdata('message', 'editCategory');
            redirect('category');
        } else {
            $this->session->set_flashdata('message','failed ');
            redirect('category');
        }
    }

    /**
     * Masa manfaat (tahun, 1-50, kosong = tidak disusutkan) dan nilai sisa (0-100%).
     * @return array|false
     */
    private function _depreciation_input()
    {
        $life     = trim((string) $this->input->post('useful_life_years'));
        $residual = trim(str_replace(',', '.', (string) $this->input->post('residual_percent')));

        if ($life !== '' && (!ctype_digit($life) || (int) $life < 1 || (int) $life > 50)) {
            return false;
        }
        if ($residual !== '' && (!is_numeric($residual) || (float) $residual < 0 || (float) $residual > 100)) {
            return false;
        }

        return [
            'useful_life_years' => $life !== '' ? (int) $life : null,
            'residual_percent'  => $residual !== '' ? round((float) $residual, 2) : 0,
        ];
    }
}
