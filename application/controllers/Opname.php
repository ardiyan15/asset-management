<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Stock opname: audit fisik aset per ruangan.
 *
 * Alur: pilih ruangan → sistem snapshot aset yang tercatat → petugas scan QR / ketik serial /
 * centang manual → selesai → berita acara PDF. Aset dari ruangan lain yang ikut ter-scan
 * dicatat sebagai "salah ruangan".
 */
class Opname extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'Auth');
        $this->load->model('Opname_model', 'Opname');
        $this->load->library('encryption');
        $this->load->helper('url_crypto');
        is_logged_in();
    }

    public function index()
    {
        $data['title'] = 'Stock Opname';
        $data['user']  = $this->Auth->get_active_user($this->session->userdata('username'));
        $data['list']  = $this->Opname->get_list($data['user']['role_id'], $data['user']['building_id']);
        $data['rooms'] = $this->Opname->get_rooms_for_user($data['user']['role_id'], $data['user']['building_id']);

        foreach ($data['list'] as &$row) {
            $row['token'] = $this->_token($row['id']);
        }
        unset($row);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('opname/index', $data);
        $this->load->view('templates/footer');
    }

    public function start()
    {
        $user    = $this->Auth->get_active_user($this->session->userdata('username'));
        $room_id = (int) $this->input->post('room_id');
        $notes   = trim((string) $this->input->post('notes', true));
        $room    = $room_id ? $this->Opname->get_room_location($room_id) : null;

        if (!$room || !$this->_can_access_building($user, $room['building_id'])) {
            $this->session->set_flashdata('message', 'opname_failed');
            redirect('opname');
        }

        // Satu ruangan hanya boleh punya satu opname yang sedang berjalan
        $open = $this->Opname->get_open_by_room($room_id);
        if ($open) {
            $this->session->set_flashdata('message', 'opname_exists');
            redirect('opname/detail/' . $this->_token($open['id']));
        }

        $opname_id = $this->Opname->create($room_id, $user['id_user'], $notes);
        if (!$opname_id) {
            $this->session->set_flashdata('message', 'opname_failed');
            redirect('opname');
        }

        $this->session->set_flashdata('message', 'opnameStarted');
        redirect('opname/detail/' . $this->_token($opname_id));
    }

    public function detail($token = null)
    {
        $data['user']    = $this->Auth->get_active_user($this->session->userdata('username'));
        $data['opname']  = $this->_authorized_opname($token, $data['user']);
        $data['token']   = $token;
        $data['title']   = 'Stock Opname ' . $data['opname']['code'];
        $data['details'] = $this->Opname->get_details($data['opname']['id']);

        // Dipakai halaman scan QR untuk menawarkan "tandai ditemukan" saat opname berjalan
        if ((int) $data['opname']['status'] === Opname_model::STATUS_OPEN) {
            $this->session->set_userdata('active_opname', (int) $data['opname']['id']);
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('opname/detail', $data);
        $this->load->view('templates/footer');
    }

    /**
     * AJAX: proses hasil scan / input nomor seri.
     */
    public function check()
    {
        $user   = $this->Auth->get_active_user($this->session->userdata('username'));
        $opname = $this->_authorized_opname($this->input->post('token'), $user, true);

        if (!$opname || (int) $opname['status'] !== Opname_model::STATUS_OPEN) {
            return $this->_json(['status' => 'closed', 'message' => 'Sesi opname tidak ditemukan atau sudah selesai.']);
        }

        $serial = $this->_extract_serial((string) $this->input->post('serial'));
        if ($serial === '') {
            return $this->_json(['status' => 'invalid', 'message' => 'Nomor seri kosong.']);
        }

        $result = $this->Opname->check_serial($opname, $serial);
        $result['serial']  = $serial;
        $result['message'] = $this->_check_message($result, $opname);
        $result['summary'] = $this->_summary($opname['id']);

        return $this->_json($result);
    }

    /**
     * AJAX: centang / batal centang manual.
     */
    public function toggle()
    {
        $user   = $this->Auth->get_active_user($this->session->userdata('username'));
        $opname = $this->_authorized_opname($this->input->post('token'), $user, true);

        if (!$opname || (int) $opname['status'] !== Opname_model::STATUS_OPEN) {
            return $this->_json(['status' => 'closed', 'message' => 'Sesi opname tidak ditemukan atau sudah selesai.']);
        }

        $ok = $this->Opname->set_found($opname['id'], (int) $this->input->post('detail_id'), (bool) $this->input->post('found'));

        return $this->_json([
            'status'  => $ok ? 'ok' : 'error',
            'summary' => $this->_summary($opname['id']),
        ]);
    }

    /**
     * Form biasa dari halaman scan QR (scan pakai kamera HP → tandai ditemukan).
     */
    public function mark_from_scan()
    {
        $user   = $this->Auth->get_active_user($this->session->userdata('username'));
        $opname = $this->_authorized_opname($this->input->post('token'), $user, true);
        $serial = $this->_extract_serial((string) $this->input->post('serial'));

        if (!$opname || (int) $opname['status'] !== Opname_model::STATUS_OPEN || $serial === '') {
            $this->session->set_flashdata('opname_scan', ['status' => 'closed', 'message' => 'Sesi opname tidak ditemukan atau sudah selesai.']);
        } else {
            $result = $this->Opname->check_serial($opname, $serial);
            $this->session->set_flashdata('opname_scan', [
                'status'  => $result['status'],
                'message' => $this->_check_message($result, $opname),
            ]);
        }

        redirect('scan/' . rawurlencode($serial));
    }

    public function finish()
    {
        $token  = $this->input->post('token');
        $user   = $this->Auth->get_active_user($this->session->userdata('username'));
        $opname = $this->_authorized_opname($token, $user);

        if ($this->Opname->finish($opname['id']) > 0) {
            if ((int) $this->session->userdata('active_opname') === (int) $opname['id']) {
                $this->session->unset_userdata('active_opname');
            }
            $this->session->set_flashdata('message', 'opnameFinished');
        } else {
            $this->session->set_flashdata('message', 'opname_failed');
        }
        redirect('opname/detail/' . $token);
    }

    public function cancel()
    {
        $user   = $this->Auth->get_active_user($this->session->userdata('username'));
        $opname = $this->_authorized_opname($this->input->post('token'), $user);

        if ($this->Opname->cancel($opname['id']) > 0) {
            if ((int) $this->session->userdata('active_opname') === (int) $opname['id']) {
                $this->session->unset_userdata('active_opname');
            }
            $this->session->set_flashdata('message', 'opnameCanceled');
        } else {
            $this->session->set_flashdata('message', 'opname_failed');
        }
        redirect('opname');
    }

    /**
     * Berita acara stock opname (PDF).
     */
    public function print_report($token = null)
    {
        $user              = $this->Auth->get_active_user($this->session->userdata('username'));
        $data['opname']    = $this->_authorized_opname($token, $user);
        $data['details']   = $this->Opname->get_details($data['opname']['id']);
        $data['title']     = 'Berita Acara Stock Opname';

        $this->load->library('pdf');
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->filename = 'BA-' . str_replace('/', '-', $data['opname']['code']) . '.pdf';
        $this->pdf->load_view('opname/print_report', $data);
    }

    // ------------------------------------------------------------------

    private function _token($id)
    {
        return urlsafe_b64encode($this->encryption->encrypt((string) $id));
    }

    private function _can_access_building($user, $building_id)
    {
        return $user['role_id'] === '1' || (string) $user['building_id'] === (string) $building_id;
    }

    /**
     * Decrypt token → header opname, sekaligus cek hak akses per gedung.
     * $soft = true: kembalikan null (untuk AJAX) alih-alih redirect.
     */
    private function _authorized_opname($token, $user, $soft = false)
    {
        $id     = $token ? $this->encryption->decrypt(urlsafe_b64decode((string) $token)) : false;
        $opname = $id ? $this->Opname->get_header((int) $id) : null;

        if (!$opname || !$this->_can_access_building($user, $opname['building_id'])) {
            if ($soft) {
                return null;
            }
            $this->session->set_flashdata('message', 'opname_failed');
            redirect('opname');
        }
        return $opname;
    }

    /**
     * Isi QR label berupa URL .../scan/<serial>; ambil serial-nya saja.
     */
    private function _extract_serial($raw)
    {
        $raw = trim($raw);
        $pos = strrpos($raw, '/scan/');
        if ($pos !== false) {
            $raw = substr($raw, $pos + 6);
            $raw = strtok($raw, '?#');
        }
        return trim(rawurldecode((string) $raw));
    }

    private function _check_message(array $result, array $opname)
    {
        $name = $result['asset'] ? $result['asset']['asset_name'] . ' (' . $result['asset']['serial_number'] . ')' : '';

        switch ($result['status']) {
            case 'found':
                return $name . ' ditemukan.';
            case 'already':
                return $name . ' sudah tercatat sebelumnya.';
            case 'wrong_room':
                return $name . ' tercatat di ruangan ' . ($result['asset']['room_name'] ?? '-') . ', bukan ' . $opname['room_name'] . '. Dicatat sebagai salah ruangan.';
            default:
                return 'Nomor seri tidak terdaftar di sistem.';
        }
    }

    private function _summary($opname_id)
    {
        $h = $this->Opname->get_header($opname_id);
        return [
            'expected'   => (int) $h['total_expected'],
            'found'      => (int) $h['total_found'],
            'missing'    => (int) $h['total_missing'],
            'wrong_room' => (int) $h['total_wrong_room'],
        ];
    }

    private function _json(array $payload)
    {
        $this->output->set_content_type('application/json')->set_output(json_encode($payload));
    }
}
