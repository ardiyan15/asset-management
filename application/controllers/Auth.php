<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Assets_model', 'Assets');
        $this->load->model('Auth_model', 'Auth');
        $this->load->library('form_validation');
    }

    public function index()
    {
        if ($this->session->userdata('email')) {
            redirect('user');
        }
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');
        if ($this->form_validation->run() == false) {
            $data['title'] = 'Login Page';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/v_login');
            $this->load->view('templates/auth_footer');
        } else {
            // Validasinya berhasil
            // Method login merupakan method private yang hanya bisa diakses oleh method di dalam controller auth
            $this->_login();
        }
    }

    private function _login()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $user = $this->Auth->get_active_user($username);

        // User nya ada
        if ($user) {
            // Jika usernya aktif
            if ($user['is_active'] == 1) {
                // cek password
                if (password_verify($password, $user['password'])) {
                    $data = [
                        'username'  => $user['username'],
                        'role_id'   => $user['role_id'],
                    ];
                    $this->session->set_userdata($data);                    
                    redirect('admin');
                } else {
                    $this->session->set_flashdata(
                        'message',
                        '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong> Wrong email or password </strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>'
                    );
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata(
                    'message',
                    '<div class="alert alert-danger" role="alert">
                        The email has not been activated.
                    </div>'
                );
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-danger" role="alert">
                    Wrong email or password!.
                </div>'
            );
            redirect('auth');
        }
    }

    public function registration()
    {
        if ($this->session->userdata('email')) {
            redirect('user');
        }
        // parameter kedua dari set_rules digunakan sebagai alias / nama lain,
        // paramater ketiga dari set_rules digunakan sebagai validasi form registration
        $this->form_validation->set_rules('fullname', 'Name', 'required|trim');
        $this->form_validation->set_rules('store_code', 'Code', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.email]', [
            'is_unique' => 'this email has already registered!'
        ]);

        // is_unique digunakan untuk pengecekan duplikasi email

        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]', [
            'matches' => 'password dont match!',
            'min_length' => 'Password to short!'
        ]);

        $this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'User Registration';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/v_registration');
            $this->load->view('templates/auth_footer');
        } else {
            $data = [
                //parameter true digunkan untuk mencegah XSS (Cross Site Scripting) / serangan berupa injeksi code

                'user_code' => htmlspecialchars($this->input->post('store_code')),
                'fullname' => htmlspecialchars($this->input->post('fullname', true)),
                'email' => htmlspecialchars($this->input->post('email', true)),
                'image' => 'default.jpg',
                'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
                'role_id' => 2,
                'is_active' => 0,
                'date_created' => time()
            ];

            // siapkan token
            $token = base64_encode(random_bytes(32));
            $user_token = [
                'email' => $this->input->post('email'),
                'token' => $token,
            ];

            $this->Assets->registration_model($data, $user_token);

            $this->_sendEmail($token, 'verify');

            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-success" role="alert">
                    Congratulation! your account has been created. Please contact IT administrator to activate your account
                </div>'
            );
            redirect('auth');
        }
    }

    private function _sendEmail($token, $type)
    {
        $config = array();
        $config['protocol']  = 'smtp';
        $config['smtp_host'] = 'ssl://smtp.googlemail.com';
        $config['smtp_user'] = 'ardhiyan15@gmail.com';
        $config['smtp_pass'] = 'veteran768015';
        $config['smtp_port'] = 465;
        $config['mailtype']  = 'html';
        $config['charset']   = 'utf-8';
        $this->email->initialize($config);
        $this->email->set_newline("\r\n");

        $this->load->library('email', $config);

        $this->email->from('ardhiyan15@gmail.com', 'Ardiyan Agus Prayogo');

        if ($type == 'verify') {
            $this->email->to('ardhiyan02@gmail.com');
        } else {
            // $this->email->to($this->input->post('email'));
            $this->email->to('ardhiyan15@gmail.com');
        }

        if ($type == 'verify') {
            $this->email->subject('Account Verification');
            $this->email->message("<b>" . $this->input->post('fullname') . "</b>" . ' Has been register account, Pleas click this link to verify new account : <a href="' . base_url() . 'auth/verify?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '"> Activate </a> ');
        } else if ($type == 'forgot') {
            $this->email->subject('Reset Password');
            $this->email->message("<b>" . $this->input->post('email') . "</b>" . ' Has been register account, Pleas click this link to reset your password : <a href="' . base_url() . 'auth/resetPassword?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '"> Reset Password </a> ');
        }

        if ($this->email->send()) {
            return true;
        } else {
            echo $this->email->print_debugger();
            die;
        }
    }

    public function verify()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');

        $user = $this->Assets->login_model($email);

        if ($user) {
            // cek token user
            $user_token = $this->Assets->check_user_token($token);
            if ($user_token) {
                $this->Assets->activate_user($email);

                $this->session->set_flashdata(
                    'message',
                    '<div class="alert alert-success" role="alert">
                        ' . $email . ' has been activated.
                    </div>'
                );
                redirect('auth');
            } else {
                $this->session->set_flashdata(
                    'message',
                    '<div class="alert alert-danger" role="alert">
                        Account activation failed! Token invalid.
                    </div>'
                );
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-danger" role="alert">
                    Account activation failed! Wrong email.
                </div>'
            );
            redirect('auth');
        }
    }

    public function logout()
    {
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('role_id');

        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-success" role="alert">
                You have been logged out
            </div>'
        );
        redirect('auth');
    }

    public function blocked()
    {
        $this->load->view('auth/v_blocked');
    }

    public function forgotPassword()
    {

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        if ($this->form_validation->run() == false) {
            $data['title'] = 'Forgot Password';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/forgot-password');
            $this->load->view('templates/auth_footer');
        } else {
            $email = $this->input->post('email');
            $user = $this->Assets->forgot_password_model($email);

            if ($user) {
                $token = base64_encode(random_bytes(32));

                $user_token = [
                    'email' => $email,
                    'token' => $token,
                    // 'date_created' => time()
                ];

                $this->Assets->create_user_token($user_token);
                $this->_sendEmail($token, 'forgot');

                $this->session->set_flashdata(
                    'message',
                    '<div class="alert alert-success" role="alert">
                        Please check your email to reset your password.
                    </div>'
                );
                redirect('auth/forgotPassword');
            } else {
                $this->session->set_flashdata(
                    'message',
                    '<div class="alert alert-danger" role="alert">
                        Email is not registered or activated!.
                    </div>'
                );
                redirect('auth/forgotPassword');
            }
        }
    }

    public function resetPassword()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');

        $user = $this->Assets->login_model($email);

        if ($user) {
            $user_token = $this->Assets->check_user_token($token);

            if ($user_token) {
                $this->session->set_userdata('reset_email', $email);
                $this->changePassword();
            } else {
                $this->session->set_flashdata(
                    'message',
                    '<div class="alert alert-danger" role="alert">
                        Reset password failed! token invalid.
                    </div>'
                );
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-danger" role="alert">
                    Reset password failed! Wrong email.
                </div>'
            );
            redirect('auth');
        }
    }

    public function changePassword()
    {

        if (!$this->session->userdata('reset_email')) {
            redirect('auth');
        }

        $this->form_validation->set_rules('password1', 'Password', 'trim|required|min_length[6]|matches[password2]');
        $this->form_validation->set_rules('password2', 'Repeat Password', 'trim|required|min_length[6]|matches[password1]');
        if ($this->form_validation->run() == false) {
            $data['title'] = 'Change Password';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/change-password');
            $this->load->view('templates/auth_footer');
        } else {
            $password = password_hash($this->input->post('password1'), PASSWORD_DEFAULT);
            $email = $this->session->userdata('reset_email');

            $this->Assets->reset_password_model($email, $password);

            $this->session->unset_userdata('reset_email');

            $this->session->set_flashdata(
                'message',  
                '<div class="alert alert-success" role="alert">
                    Password has been changed, please login.
                </div>'
            );
            redirect('auth');
        }
    }
}
