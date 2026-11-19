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
        $this->_render_form('Daftar User Baru');
    }

    /**
     * Form tambah / ubah user. $edit = baris user yang diubah (null = tambah baru).
     */
    private function _render_form($title, $error = null, $edit = null, $token = null)
    {
        $data['user']       = $this->Auth->get_active_user($this->session->userdata('username'));
        // satu akun per gedung: gedung yang sudah punya akun lain tidak ditawarkan
        $data['buildings']  = $this->Buildings->get_assignable_buildings($edit['id_user'] ?? null);
        $data['title']      = $title;
        $data['error']      = $error;
        $data['edit']       = $edit;
        $data['token']      = $token;
        $data['is_self']    = $edit && (int) $edit['id_user'] === (int) $data['user']['id_user'];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('user/form', $data);
        $this->load->view('templates/footer');
    }

    public function store()
    {
        is_allowed();
        $this->_set_user_rules(null);
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[3]', [
            'min_length' => 'Password minimal 3 karakter.'
        ]);
        // Form tambah hanya membuat akun role User
        $this->form_validation->set_rules('role', 'Role', 'required|in_list[2]', [
            'in_list' => 'Pilih role.'
        ]);

        if ($this->form_validation->run() == false) {
            return $this->_render_form('Daftar User Baru', $this->form_validation->error_string());
        }

        // Foto opsional: tanpa upload pakai gambar default (kolom image NOT NULL)
        $image = $this->_upload_photo();
        if ($image === false) {
            return $this->_render_form('Daftar User Baru', 'Foto gagal diupload. Gunakan file gif/jpg/jpeg/png maksimal 2 MB.');
        }

        $email = trim((string) $this->input->post('email', true));
        $data = [
            'username'      => $this->input->post('username', true),
            'email'         => $email !== '' ? $email : null,
            'password'      => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'role_id'       => $this->input->post('role'),
            'is_active'     => 1,
            'building_id'   => $this->input->post('building'),
            'image'         => $image ?: 'default.jpg',
            'created'       => date('Y-m-d')
        ];

        $result = $this->Users->create($data);

        if($result > 0) {
            $this->audit->log('user.create', 'Buat akun ' . $data['username'] . ' (' . ($data['role_id'] == 1 ? 'admin' : 'user') . ')', [
                'entity_type' => 'user', 'entity_id' => $this->db->insert_id(), 'entity_label' => $data['username'],
                'building_id' => (int) $data['building_id'],
                'changes'     => [
                    'role_id'     => ['old' => null, 'new' => (int) $data['role_id']],
                    'building_id' => ['old' => null, 'new' => (int) $data['building_id']],
                    'email'       => ['old' => null, 'new' => $data['email']],
                ],
            ]);
            $this->session->set_flashdata('message', 'addUser');
            redirect('admin/list_user');
        } else {
            $this->session->set_flashdata('message','failed ');
            redirect('admin/list_user');
        }
    }

    /**
     * Halaman ubah user (admin): username, email, password (opsional), role, gedung, foto.
     * ID di URL berupa token url_crypto.
     */
    public function edit($token = null)
    {
        is_allowed();
        $target = $this->_user_from_token($token);
        if (!$target) {
            show_404();
        }
        $this->_render_form('Ubah User', null, $target, $token);
    }

    public function update($token = null)
    {
        is_allowed();
        $target = $this->_user_from_token($token);
        if (!$target) {
            show_404();
        }

        $me      = $this->Auth->get_active_user($this->session->userdata('username'));
        $is_self = (int) $target['id_user'] === (int) $me['id_user'];

        $this->_set_user_rules($target['id_user']);
        $this->form_validation->set_rules('password', 'Password', 'min_length[3]', [
            'min_length' => 'Password minimal 3 karakter.'
        ]);
        $this->form_validation->set_rules('role', 'Role', 'required|in_list[1,2]', [
            'in_list' => 'Pilih role.'
        ]);

        if ($this->form_validation->run() == false) {
            return $this->_render_form('Ubah User', $this->form_validation->error_string(), $target, $token);
        }

        // Admin tidak boleh mengubah role-nya sendiri (bisa mengunci diri dari menu admin)
        $role_id = $is_self ? (int) $target['role_id'] : (int) $this->input->post('role');
        // Admin tidak terikat gedung (building_id 0), user wajib punya gedung
        $building_id = $role_id === 1 ? 0 : (int) $this->input->post('building');

        $image = $this->_upload_photo();
        if ($image === false) {
            return $this->_render_form('Ubah User', 'Foto gagal diupload. Gunakan file gif/jpg/jpeg/png maksimal 2 MB.', $target, $token);
        }

        $email = trim((string) $this->input->post('email', true));
        $data  = [
            'username'    => trim((string) $this->input->post('username', true)),
            'email'       => $email !== '' ? $email : null,
            'role_id'     => $role_id,
            'building_id' => $building_id,
        ];
        $password = (string) $this->input->post('password');
        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        if ($image) {
            $data['image'] = $image;
        }

        $this->Users->update_user($target['id_user'], $data);

        if ($image && $target['image'] && $target['image'] !== 'default.jpg' && is_file(FCPATH . 'assets/img/profile/' . $target['image'])) {
            unlink(FCPATH . 'assets/img/profile/' . $target['image']);
        }
        // Username sendiri berubah: session ikut diperbarui supaya tidak ter-logout
        if ($is_self && $data['username'] !== $target['username']) {
            $this->session->set_userdata('username', $data['username']);
        }

        // Password tidak pernah dicatat, hanya tanda bahwa password direset
        $changes = Audit::diff($target, $data, ['username', 'email', 'role_id', 'building_id']);
        if ($password !== '') {
            $changes['password_direset'] = ['old' => null, 'new' => 'ya'];
        }
        if ($image) {
            $changes['foto'] = ['old' => null, 'new' => 'diganti'];
        }
        $this->audit->log('user.update', 'Ubah akun ' . $target['username'] . ($password !== '' ? ' (termasuk reset password)' : ''), [
            'entity_type'  => 'user',
            'entity_id'    => (int) $target['id_user'],
            'entity_label' => $data['username'],
            'building_id'  => $building_id ?: ($target['building_id'] ?: null),
            'changes'      => $changes,
        ]);

        $this->session->set_flashdata('message', 'editUser');
        redirect('admin/list_user');
    }

    /**
     * Aturan bersama tambah & ubah. $exclude_id = id user yang sedang diubah (cek unik selain dirinya).
     */
    private function _set_user_rules($exclude_id)
    {
        $this->form_validation->set_error_delimiters('', '<br>');
        $this->form_validation->set_message('required', '{field} wajib diisi.');
        $this->form_validation->set_rules('username', 'Username', 'required|trim|max_length[40]|callback__unique_field[username,' . (int) $exclude_id . ']');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[100]|callback__unique_field[email,' . (int) $exclude_id . ']', [
            'valid_email' => 'Format email tidak valid.',
        ]);
        // Gedung wajib untuk role User; admin boleh kosong
        if ((string) $this->input->post('role') !== '1') {
            $this->form_validation->set_rules('building', 'Gedung', 'required|is_natural_no_zero|callback__building_available[' . (int) $exclude_id . ']', [
                'is_natural_no_zero' => 'Pilih gedung.'
            ]);
        }
    }

    /**
     * Callback validasi: nilai kolom (username/email) belum dipakai user lain. Nilai kosong lolos.
     * $param = "<kolom>,<id user yang dikecualikan>"
     */
    public function _unique_field($value, $param)
    {
        [$field, $exclude_id] = array_pad(explode(',', (string) $param, 2), 2, 0);
        if (!in_array($field, ['username', 'email'], true) || trim((string) $value) === '') {
            return true;
        }
        $this->db->where($field, trim((string) $value));
        if ((int) $exclude_id > 0) {
            $this->db->where('id_user !=', (int) $exclude_id);
        }
        if ($this->db->count_all_results('user') > 0) {
            $this->form_validation->set_message('_unique_field', '{field} sudah dipakai user lain.');
            return false;
        }
        return true;
    }

    /**
     * Callback validasi: gedung aktif dan belum dipakai akun lain (satu akun per gedung).
     */
    public function _building_available($building_id, $exclude_id)
    {
        $available = array_column($this->Buildings->get_assignable_buildings((int) $exclude_id ?: null), 'id');
        if (!in_array((string) $building_id, array_map('strval', $available), true)) {
            $this->form_validation->set_message('_building_available', 'Gedung tidak tersedia atau sudah punya akun lain.');
            return false;
        }
        return true;
    }

    /**
     * Upload foto profil opsional. null = tidak ada file, false = gagal, string = nama file baru.
     */
    private function _upload_photo()
    {
        if (empty($_FILES['image']['name'])) {
            return null;
        }

        $this->load->library('upload', [
            'allowed_types' => 'gif|jpg|png|jpeg',
            'max_size'      => '2048',
            'upload_path'   => './assets/img/profile/',
            'encrypt_name'  => true,
        ]);

        return $this->upload->do_upload('image') ? $this->upload->data('file_name') : false;
    }

    /**
     * Token url_crypto -> baris user, atau null.
     */
    private function _user_from_token($token)
    {
        $this->load->library('encryption');
        $this->load->helper('url_crypto');

        $cipher = urlsafe_b64decode((string) $token);
        $id     = $cipher ? $this->encryption->decrypt($cipher) : false;
        if (!$id || !ctype_digit((string) $id)) {
            return null;
        }
        return $this->db->get_where('user', ['id_user' => (int) $id])->row_array() ?: null;
    }
}
