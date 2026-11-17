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

        // Aktivitas terakhir user ini (dari audit log)
        $this->load->model('Audit_model', 'AuditLog');
        $data['activities'] = $this->AuditLog->for_user($data['user']['id_user'], 15);

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
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[100]', [
            'valid_email' => 'Format email tidak valid',
        ]);
        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('user/editProfile', $data);
            $this->load->view('templates/footer');
        } else {
            // Selalu ubah profil user yang sedang login, bukan id dari form (bisa dimanipulasi)
            $id_user        = $data['user']['id_user'];
            $old_email      = $data['user']['email'] ?? null;
            $username       = $data['user']['username'];
            $email          = trim((string) $this->input->post('email'));
            $upload_image   = $_FILES['image']['name'] ?? '';
            $newImage       = null; // null = gambar lama tidak diganti

            if ($upload_image) {
                $config['allowed_types']    = 'gif|jpg|png|jpeg';
                $config['max_size']         = '2048';
                $config['upload_path']      = './assets/img/profile/';

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('image')) {
                    $newImage = $this->upload->data('file_name');

                    $old_image = $data['user']['image'];
                    if ($old_image && $old_image !== 'default.jpg' && is_file(FCPATH . 'assets/img/profile/' . $old_image)) {
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
                'email'     => $email !== '' ? $email : null,
                'id_user'   => $id_user
            ];

            $this->Auth->update_profile_model($data);
            $this->audit->log('user.profile_update', 'Ubah profil sendiri' . ($newImage !== null ? ' (foto diganti)' : ''), [
                'entity_type' => 'user', 'entity_id' => $id_user, 'entity_label' => $username,
                'changes'     => Audit::diff(['email' => $old_email], ['email' => $data['email']]),
            ]);

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
                    $this->audit->log('user.password_change', 'Ganti password sendiri', [
                        'entity_type' => 'user', 'entity_id' => $data['user']['id_user'], 'entity_label' => $data['user']['username'],
                    ]);
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
        is_allowed();
        $this->_render_create();
    }

    private function _render_create($error = null)
    {
        $data['user']       = $this->Auth->get_active_user($this->session->userdata('username'));
        $data['buildings']  = $this->Buildings->get_all_buildings();
        $data['title']      = 'Daftar User Baru';
        $data['error']      = $error;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('user/create', $data);
        $this->load->view('templates/footer');
    }

    public function store()
    {
        is_allowed();
        $this->form_validation->set_error_delimiters('', '<br>');
        $this->form_validation->set_message('required', '{field} wajib diisi.');
        $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[user.username]', [
            'is_unique' => 'Username sudah dipakai.'
        ]);
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[3]', [
            'min_length' => 'Password minimal 3 karakter.'
        ]);
        $this->form_validation->set_rules('role', 'Role', 'required|in_list[1,2]', [
            'in_list' => 'Pilih role.'
        ]);
        $this->form_validation->set_rules('building', 'Gedung', 'required|is_natural_no_zero', [
            'is_natural_no_zero' => 'Pilih gedung.'
        ]);

        if ($this->form_validation->run() == false) {
            return $this->_render_create($this->form_validation->error_string());
        }

        // Foto opsional: tanpa upload pakai gambar default (kolom image NOT NULL)
        $image = 'default.jpg';

        if (!empty($_FILES['image']['name'])) {
            $config['allowed_types']    = 'gif|jpg|png|jpeg';
            $config['max_size']         = '2048';
            $config['upload_path']      = './assets/img/profile/';

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('image')) {
                return $this->_render_create('Foto gagal diupload. Gunakan file gif/jpg/jpeg/png maksimal 2 MB.');
            }

            $image = $this->upload->data('file_name');
        }

        $data = [
            'username'      => $this->input->post('username', true),
            'password'      => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'role_id'       => $this->input->post('role'),
            'is_active'     => 1,
            'building_id'   => $this->input->post('building'),
            'image'         => $image,
            'created'       => date('Y-m-d')
        ];

        $result = $this->Users->create($data);

        if($result > 0) {
            $this->audit->log('user.create', 'Buat akun ' . $data['username'] . ' (' . ($data['role_id'] == 1 ? 'admin' : 'user') . ')', [
                'entity_type' => 'user', 'entity_id' => $this->db->insert_id(), 'entity_label' => $data['username'],
                'building_id' => (int) $data['building_id'],
                'changes'     => ['role_id' => ['old' => null, 'new' => (int) $data['role_id']], 'building_id' => ['old' => null, 'new' => (int) $data['building_id']]],
            ]);
            $this->session->set_flashdata('message', 'addUser');
            redirect('admin/list_user');
        } else {
            $this->session->set_flashdata('message','failed ');
            redirect('admin/list_user');
        }
    }
}
