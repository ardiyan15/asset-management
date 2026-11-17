<?php

class Buildings extends CI_Controller
{
    var $data;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Assets_model', 'Assets');
        $this->load->model('Auth_model', 'Auth');
        $this->load->model('Buildings_model', 'Buildings');
        is_logged_in();
    }

    public function index()
    {
        $data['user']       = $this->Auth->get_active_user($this->session->userdata('username'));
        $data['title']      = "Lokasi Bangunan";
        $data['role_id']    = $data['user']['role_id'];
        $building_id        = $data['user']['building_id'];
        $role_id            = $data['user']['role_id'];
        $data['buildings']  = $this->Buildings->get_active_buildings($role_id, $building_id);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('buildings/index', $data);
        $this->load->view('templates/footer');
    }

    public function edit($id)
    {
        $data['title']  = 'Edit Building';
        $data['user']   = $this->Auth->get_active_user($this->session->userdata('username'));
        $data['result'] = $this->Buildings->get_single_active_building_by_id($id);
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('buildings/edit_building', $data);
        $this->load->view('templates/footer');
    }

    public function update()
    {
        $data = [
            'id'            => $this->input->post('id'),
            'name'          => $this->input->post('name'),
            'updated_at'    => date("Y-m-d H:i:s")
        ];

        $before = $this->db->get_where('buildings', ['id' => (int) $data['id']])->row_array();
        $result = $this->Buildings->update_data_building_by_id($data);

        if($result > 0) {
            $this->audit->log('master.building_update', 'Ubah gedung ' . ($before['name'] ?? '') . ' menjadi ' . $data['name'], [
                'entity_type' => 'building', 'entity_id' => (int) $data['id'], 'entity_label' => $data['name'],
                'building_id' => (int) $data['id'], 'changes' => Audit::diff($before ?: [], ['name' => $data['name']]),
            ]);
            $this->session->set_flashdata('message', 'editBuilding');
            redirect('buildings');
        } else {
            $this->session->set_flashdata('message', 'failed ');
            redirect('buildings');
        }
    }

    public function delete($id)
    {
        $before = $this->db->get_where('buildings', ['id' => (int) $id])->row_array();
        $result = $this->Buildings->delete_building_by_id($id);

        if($result > 0) {
            $this->audit->log('master.building_delete', 'Hapus gedung ' . ($before['name'] ?? '#' . (int) $id), [
                'entity_type' => 'building', 'entity_id' => (int) $id, 'entity_label' => $before['name'] ?? null,
                'building_id' => (int) $id,
            ]);
            $this->session->set_flashdata('message', 'deleteBuilding');
            redirect('buildings');
        } else {
            $this->session->set_flashdata('message', 'failed ');
            redirect('buildings');
        }
    }
}