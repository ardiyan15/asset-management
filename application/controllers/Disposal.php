<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Penghapusan (disposal) aset.
 *
 * - Semua user: ajukan penghapusan aset Rusak Berat di gedungnya, lihat status pengajuan.
 * - Admin: setujui / tolak per aset (atau semua sekaligus), cetak BAST penghapusan.
 */
class Disposal extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'Auth');
        $this->load->model('Disposal_model', 'Disposal');
        $this->load->library('encryption');
        $this->load->helper('url_crypto');
        is_logged_in();
    }

    public function index()
    {
        $data['title'] = 'Penghapusan Aset';
        $data['user']  = $this->Auth->get_active_user($this->session->userdata('username'));
        $data['list']  = $this->Disposal->get_list($data['user']['role_id'], $data['user']['building_id']);

        foreach ($data['list'] as &$row) {
            $row['token'] = $this->_token($row['id']);
        }
        unset($row);

        $this->_render('disposal/index', $data);
    }

    /**
     * Form pengajuan. ?asset=<id> untuk langsung mencentang satu aset.
     */
    public function create()
    {
        $data['title']    = 'Ajukan Penghapusan Aset';
        $data['user']     = $this->Auth->get_active_user($this->session->userdata('username'));
        $data['assets']   = $this->Disposal->get_eligible_assets($data['user']['role_id'], $data['user']['building_id']);
        $data['methods']  = Disposal_model::methods();
        $data['selected'] = (int) $this->input->get('asset');

        $this->_render('disposal/create', $data);
    }

    public function store()
    {
        $user      = $this->Auth->get_active_user($this->session->userdata('username'));
        $asset_ids = array_values(array_filter(array_map('intval', (array) $this->input->post('asset_ids'))));
        $method    = (string) $this->input->post('method');
        $reason    = trim((string) $this->input->post('reason', true));

        if (empty($asset_ids) || !array_key_exists($method, Disposal_model::methods()) || $reason === '') {
            $this->session->set_flashdata('message', 'disposal_invalid');
            redirect('disposal/create');
        }

        // Ambil ulang dari DB: hanya aset yang benar-benar memenuhi syarat & di gedung user
        $assets = $this->Disposal->get_eligible_assets($user['role_id'], $user['building_id'], $asset_ids);
        if (empty($assets)) {
            $this->session->set_flashdata('message', 'disposal_invalid');
            redirect('disposal/create');
        }

        $disposal_id = $this->Disposal->create($assets, $method, mb_substr($reason, 0, 2000), $user['id_user']);
        if (!$disposal_id) {
            $this->session->set_flashdata('message', 'disposal_failed');
            redirect('disposal/create');
        }

        $header = $this->Disposal->get_header($disposal_id);
        $this->audit->log('disposal.request', 'Ajukan penghapusan ' . count($assets) . ' aset (' . Disposal_model::method_label($method) . ')', [
            'entity_type'  => 'disposal',
            'entity_id'    => $disposal_id,
            'entity_label' => $header['code'] ?? null,
            'building_id'  => $user['role_id'] === '1' ? null : $user['building_id'],
            'changes'      => [
                'aset'   => ['old' => null, 'new' => implode(', ', array_column($assets, 'serial_number'))],
                'alasan' => ['old' => null, 'new' => mb_substr($reason, 0, 200)],
            ],
        ]);
        $this->session->set_flashdata('message', 'disposalRequested');
        redirect('disposal/detail/' . $this->_token($disposal_id));
    }

    public function detail($token = null)
    {
        $data['user']     = $this->Auth->get_active_user($this->session->userdata('username'));
        $data['disposal'] = $this->_authorized_disposal($token, $data['user']);
        $data['token']    = $token;
        $data['title']    = 'Penghapusan ' . $data['disposal']['code'];
        $data['is_admin'] = $data['user']['role_id'] === '1';
        $data['details']  = $this->Disposal->get_details(
            $data['disposal']['id'],
            $data['is_admin'] ? null : $data['user']['building_id']
        );

        $this->_render('disposal/detail', $data);
    }

    /**
     * Admin: setujui detail terpilih (detail_ids[]) atau semua yang menunggu (all=1).
     */
    public function approve()
    {
        $this->_review(true);
    }

    /**
     * Admin: tolak detail terpilih, wajib dengan alasan.
     */
    public function reject()
    {
        $this->_review(false);
    }

    /**
     * BAST penghapusan: hanya aset yang disetujui.
     */
    public function print_bast($token = null)
    {
        $user             = $this->Auth->get_active_user($this->session->userdata('username'));
        $data['disposal'] = $this->_authorized_disposal($token, $user);
        $data['details']  = $this->Disposal->get_details(
            $data['disposal']['id'],
            $user['role_id'] === '1' ? null : $user['building_id'],
            Disposal_model::STATUS_APPROVED
        );

        if (empty($data['details'])) {
            $this->session->set_flashdata('message', 'disposal_no_approved');
            redirect('disposal/detail/' . $token);
        }

        $data['title'] = 'Berita Acara Penghapusan Aset';

        $this->load->library('pdf');
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->filename = 'BAST-' . str_replace('/', '-', $data['disposal']['code']) . '.pdf';
        $this->audit->log('report.export', 'Cetak BAST penghapusan ' . $data['disposal']['code'] . ' (PDF)', [
            'entity_type'  => 'disposal',
            'entity_id'    => $data['disposal']['id'],
            'entity_label' => $data['disposal']['code'],
        ]);
        $this->pdf->load_view('disposal/print_bast', $data);
    }

    // ------------------------------------------------------------------

    private function _review($approve)
    {
        $token = (string) $this->input->post('token');
        $user  = $this->Auth->get_active_user($this->session->userdata('username'));

        if ($user['role_id'] !== '1') {
            redirect('auth/blocked');
        }

        $disposal   = $this->_authorized_disposal($token, $user);
        $notes      = (string) $this->input->post('notes', true);
        $detail_ids = $this->input->post('all')
            ? []
            : array_values(array_filter(array_map('intval', (array) $this->input->post('detail_ids'))));

        if ((!$approve && trim($notes) === '') || (!$this->input->post('all') && empty($detail_ids))) {
            $this->session->set_flashdata('message', 'disposal_invalid');
            redirect('disposal/detail/' . $token);
        }

        $processed = $this->Disposal->review($disposal['id'], $detail_ids, $approve, $notes, $user['id_user']);

        if ($processed === false) {
            $this->session->set_flashdata('message', 'disposal_failed');
        } elseif ($processed === 0) {
            $this->session->set_flashdata('message', 'disposal_nothing');
        } else {
            $this->audit->log($approve ? 'disposal.approve' : 'disposal.reject',
                ($approve ? 'Setujui' : 'Tolak') . ' penghapusan ' . $processed . ' aset (' . $disposal['code'] . ')', [
                'entity_type'  => 'disposal',
                'entity_id'    => $disposal['id'],
                'entity_label' => $disposal['code'],
                'changes'      => ['catatan' => ['old' => null, 'new' => trim($notes) ?: null]],
            ]);
            $this->session->set_flashdata('message', $approve ? 'disposalApproved' : 'disposalRejected');
        }
        redirect('disposal/detail/' . $token);
    }

    private function _render($view, array $data)
    {
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view($view, $data);
        $this->load->view('templates/footer');
    }

    private function _token($id)
    {
        return urlsafe_b64encode($this->encryption->encrypt((string) $id));
    }

    /**
     * Decrypt token → header pengajuan + cek akses (admin, atau pengajuan berisi aset di gedung user).
     */
    private function _authorized_disposal($token, $user)
    {
        $id       = $token ? $this->encryption->decrypt(urlsafe_b64decode((string) $token)) : false;
        $disposal = $id ? $this->Disposal->get_header((int) $id) : null;

        $allowed = $disposal && ($user['role_id'] === '1'
            || in_array((string) $user['building_id'], explode(',', (string) $disposal['building_ids']), true));

        if (!$allowed) {
            $this->session->set_flashdata('message', 'disposal_failed');
            redirect('disposal');
        }
        return $disposal;
    }
}
