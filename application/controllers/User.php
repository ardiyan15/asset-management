<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Assets_model', 'Assets');
        is_logged_in();
    }

    public function index()
    {
        $data['title'] = 'My Profile';
        $data['user'] = $this->Assets->login_model($this->session->userdata('email'));

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('user/index', $data);
        $this->load->view('templates/footer');
    }

    public function editProfile()
    {
        $data['title'] = 'Edit Profile';
        $data['user'] = $this->Assets->login_model($this->session->userdata('email'));

        // Cek jika ada file gambar yang diupload
        $this->form_validation->set_rules('fullname', 'Full Name', 'required|trim');
        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('user/editProfile', $data);
            $this->load->view('templates/footer');
        } else {
            $name = $this->input->post('fullname');
            $email = $this->input->post('email');
            $upload_image = $_FILES['image']['name'];

            if ($upload_image) {
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size']     = '2048';
                $config['upload_path']  = './assets/img/profile/';

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
            $this->Assets->update_profile_model($name, $email, $newImage);

            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-success" role="alert">
                   Your profile has been updated
                </div>'
            );
            redirect('user');
        }
    }

    public function changePassword()
    {
        $data['title'] = 'Change Password';
        $data['user'] = $this->Assets->login_model($this->session->userdata('email'));

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
                       Wrong current password
                    </div>'
                );
                redirect('user/changepassword');
            } else {
                if ($currentPassword == $newPassword) {
                    $this->session->set_flashdata(
                        'message',
                        '<div class="alert alert-danger" role="alert">
                          New password cannot be the same as current password
                        </div>'
                    );
                    redirect('user/changepassword');
                } else {
                    // Password sudah ok
                    $password_hash = password_hash($newPassword, PASSWORD_DEFAULT);
                    $this->Assets->change_password_model($password_hash, $data['user']['email']);
                    $this->session->set_flashdata(
                        'message',
                        '<div class="alert alert-success" role="alert">
                          Password Changed
                        </div>'
                    );
                    redirect('user/changepassword');
                }
            }
        }
    }
}
