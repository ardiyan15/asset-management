<?php

class User extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'Auth');
        $this->load->model('Assets_model', 'Assets');
        $this->load->model('Buildings_model', 'Buildings');
        $this->load->model('Users_model', 'Users');
        is_logged_in();
    }

    public function index()
    {
        $data['title']  = 'Profil Saya';
        $data['user']   = $this->Auth->get_active_user($this->session->userdata('username'));

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('user/index', $data);
        $this->load->view('templates/footer');
    }

    public function editProfile()
    {
        $data['title'] = 'Edit Profile';
        $data['user']  = $this->Auth->get_active_user($this->session->userdata('username'));
        // Cek jika ada file gambar yang diupload
        $this->form_validation->set_rules('username', 'username', 'required|trim');
        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('user/editProfile', $data);
            $this->load->view('templates/footer');
        } else {
            $id_user        = $this->input->post('id_user');
            $username       = $this->input->post('username');
            $upload_image   = $_FILES['image']['name'];

            if ($upload_image) {
                $config['allowed_types']    = 'gif|jpg|png|jpeg';
                $config['max_size']         = '2048';
                $config['upload_path']      = './assets/img/profile/';

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('image')) {
                    $newImage = $this->upload->data('file_name');

                    $old_image = $data['user']['image'];
                    if ($old_image !== 'default.jpg') {
                        // method unlink() digunakan Untuk menghapus gambar yang sebelumnya dipakai
                        unlink(FCPATH . 'assets/img/profile/' . $old_image);
                    }
                } else {
                    echo $this->upload->display_errors();
                }
            }

            $data = [
                'newImage'  => $newImage,
                'username'  => $username,
                'id_user'   => $id_user
            ];

            $this->Auth->update_profile_model($data);

            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-success" role="alert">
                   Profil berhasil diubah
                </div>'
            );
            redirect('user');
        }
    }

    public function changePassword()
    {
        $data['title']  = 'Ganti Password';
        $data['user']   = $this->Auth->get_active_user($this->session->userdata('username'));

        $this->form_validation->set_rules('current_password', 'Current password', 'required|trim');
        $this->form_validation->set_rules('new_password1', 'New password', 'required|trim|min_length[3]|matches[new_password2]');
        $this->form_validation->set_rules('new_password2', 'Confirm new password', 'required|trim|min_length[3]|matches[new_password1]');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('user/changepassword', $data);
            $this->load->view('templates/footer');
        } else {
            $currentPassword = $this->input->post('current_password');
            $newPassword = $this->input->post('new_password1');
            if (!password_verify($currentPassword, $data['user']['password'])) {
                $this->session->set_flashdata(
                    'message',
                    '<div class="alert alert-danger" role="alert">
                       Password saat ini salah
                    </div>'
                );
                redirect('user/changepassword');
            } else {
                if ($currentPassword == $newPassword) {
                    $this->session->set_flashdata(
                        'message',
                        '<div class="alert alert-danger" role="alert">
                          Password baru tidak boleh sama dengan password lama
                        </div>'
                    );
                    redirect('user/changepassword');
                } else {
                    // Password sudah ok
                    $password_hash = password_hash($newPassword, PASSWORD_DEFAULT);
                    $this->Auth->change_password_model($password_hash, $data['user']['id_user']);
                    $this->session->set_flashdata(
                        'message',
                        '<div class="alert alert-success" role="alert">
                          Password berhasil diubah
                        </div>'
                    );
                    redirect('user/changepassword');
                }
            }
        }
    }

    public function create()
    {
        $data['user']       = $this->Auth->get_active_user($this->session->userdata('username'));
        $data['buildings']  = $this->Buildings->get_all_buildings();
        $data['title']      = 'Daftar User Baru';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('user/create');
        $this->load->view('templates/footer');
    }

    public function store()
    {
        $upload_image   = $_FILES['image']['name'];

        if ($upload_image) {
            $config['allowed_types']    = 'gif|jpg|png|jpeg';
            $config['max_size']         = '2048';
            $config['upload_path']      = './assets/img/profile/';

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('image')) {
                $image = $this->upload->data('file_name');
            } else {
                echo $this->upload->display_errors();
            }
        }

        $data = [
            'username'      => $this->input->post('username'),
            'password'      => $this->input->post('password'),
            'password'      => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'role_id'       => $this->input->post('role'),
            'is_active'     => 1,
            'building_id'   => $this->input->post('building'),
            'image'         => $image
        ];

        $result = $this->Users->create($data);

        if($result > 0) {
            $this->session->set_flashdata('message', 'addUser');
            redirect('admin/list_user');
        } else {
            $this->session->set_flashdata('message','failed ');
            redirect('admin/list_user');
        }
    }
}
