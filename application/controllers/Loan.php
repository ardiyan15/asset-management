<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Peminjaman aset sementara (proyektor, laptop, dll).
 * Admin mengelola semua gedung; user mengelola aset di gedungnya sendiri.
 * Pengingat keterlambatan lewat email: Cron::loan_reminders (CLI).
 */
class Loan extends CI_Controller
{
    const TABS = [
        'open'     => 'Sedang dipinjam',
        'overdue'  => 'Terlambat',
        'returned' => 'Dikembalikan',
        'all'      => 'Semua',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'Auth');
        $this->load->model('Loan_model', 'Loan');
        $this->load->library('encryption');
        $this->load->helper(['url_crypto', 'loan']);
        is_logged_in();
    }

    public function index()
    {
        $user   = $this->Auth->get_active_user($this->session->userdata('username'));
        $status = (string) $this->input->get('status');
        $status = array_key_exists($status, self::TABS) ? $status : 'open';
        $building_filter = $user['role_id'] === '1' ? (int) $this->input->get('building_id') : 0;

        $loans = $this->Loan->get_loans($user['role_id'], $user['building_id'], $status, $building_filter);
        foreach ($loans as &$loan) {
            $loan['token']      = $this->_token($loan['id']);
            $loan['loan_state'] = loan_status($loan);
        }
        unset($loan);

        $data = [
            'title'           => 'Peminjaman Aset',
            'user'            => $user,
            'status'          => $status,
            'tabs'            => self::TABS,
            'counts'          => $this->Loan->count_by_status($user['role_id'], $user['building_id'], $building_filter),
            'loans'           => $loans,
            'assets'          => $this->Loan->available_assets($user['role_id'], $user['building_id']),
            'preselect'       => (int) $this->input->get('asset'),
            'building_filter' => $building_filter,
            'buildings'       => $user['role_id'] === '1' ? $this->db->order_by('name')->get_where('buildings', ['status' => 1])->result_array() : [],
        ];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('loan/index', $data);
        $this->load->view('templates/footer');
    }

    public function store()
    {
        $user  = $this->Auth->get_active_user($this->session->userdata('username'));
        $asset = $this->Loan->available_assets($user['role_id'], $user['building_id'], (int) $this->input->post('asset_id'));
        $input = $this->_loan_input();

        if (!$asset || !$input) {
            $this->session->set_flashdata('message', 'loan_invalid');
            redirect('loan');
        }

        $loan_id = $this->Loan->create($asset, $input, $user['id_user']);
        if (!$loan_id) {
            $this->session->set_flashdata('message', 'loan_failed');
            redirect('loan');
        }

        $this->audit->log('loan.create', 'Pinjamkan ' . $asset['asset_name'] . ' ke ' . $input['borrower_name'] . ' sampai ' . $input['due_date'], [
            'entity_type'  => 'asset',
            'entity_id'    => $asset['id_asset'],
            'entity_label' => $asset['serial_number'],
            'building_id'  => $asset['building_id'],
            'changes'      => [
                'kode'          => ['old' => null, 'new' => $this->Loan->get_loan($loan_id)['code'] ?? null],
                'peminjam'      => ['old' => null, 'new' => $input['borrower_name']],
                'batas_kembali' => ['old' => null, 'new' => $input['due_date']],
            ],
        ]);
        $this->session->set_flashdata('message', 'loanCreated');
        $this->session->set_flashdata('loan_slip', $this->_token($loan_id));
        redirect('loan');
    }

    public function return_loan()
    {
        $user = $this->Auth->get_active_user($this->session->userdata('username'));
        $loan = $this->_authorized_loan($this->input->post('token'), $user);

        $date      = (string) $this->input->post('returned_date');
        $condition = (int) $this->input->post('condition_in');
        $parsed    = DateTime::createFromFormat('Y-m-d', $date);
        $loan_day  = substr($loan['loaned_at'], 0, 10);

        $valid = (int) $loan['status'] === Loan_model::STATUS_OPEN
            && $parsed && $parsed->format('Y-m-d') === $date
            && $date >= $loan_day && $date <= date('Y-m-d')
            && is_valid_asset_condition($condition);

        if (!$valid) {
            $this->session->set_flashdata('message', 'loan_invalid');
            redirect($this->_back());
        }

        // Jam pengembalian = sekarang kalau hari ini, selain itu akhir hari tanggal yang dipilih
        $returned_at = $date === date('Y-m-d') ? date('Y-m-d H:i:s') : $date . ' 23:59:00';
        $ok = $this->Loan->mark_returned($loan, $returned_at, $condition, $this->input->post('return_notes', true), $user['id_user']);
        if ($ok) {
            $this->audit->log('loan.return', 'Pengembalian ' . $loan['asset_name'] . ' dari ' . $loan['borrower_name'] . ' (' . $loan['code'] . ')', [
                'entity_type'  => 'asset',
                'entity_id'    => $loan['asset_id'],
                'entity_label' => $loan['serial_number'],
                'building_id'  => $loan['building_id'],
                'changes'      => [
                    'kondisi'        => ['old' => asset_condition_label($loan['condition_out']), 'new' => asset_condition_label($condition)],
                    'tanggal_kembali' => ['old' => $loan['due_date'], 'new' => $date],
                ],
            ]);
        }

        $this->session->set_flashdata('message', $ok ? 'loanReturned' : 'loan_failed');
        redirect($this->_back());
    }

    public function extend()
    {
        $user = $this->Auth->get_active_user($this->session->userdata('username'));
        $loan = $this->_authorized_loan($this->input->post('token'), $user);
        $due  = (string) $this->input->post('due_date');
        $parsed = DateTime::createFromFormat('Y-m-d', $due);

        $valid = (int) $loan['status'] === Loan_model::STATUS_OPEN
            && $parsed && $parsed->format('Y-m-d') === $due
            && $due > $loan['due_date'] && $due >= date('Y-m-d');

        if (!$valid) {
            $this->session->set_flashdata('message', 'loan_extend_invalid');
            redirect($this->_back());
        }

        $ok = $this->Loan->extend($loan, $due);
        if ($ok) {
            $this->audit->log('loan.extend', 'Perpanjang peminjaman ' . $loan['asset_name'] . ' oleh ' . $loan['borrower_name'] . ' (' . $loan['code'] . ')', [
                'entity_type'  => 'asset',
                'entity_id'    => $loan['asset_id'],
                'entity_label' => $loan['serial_number'],
                'building_id'  => $loan['building_id'],
                'changes'      => ['batas_kembali' => ['old' => $loan['due_date'], 'new' => $due]],
            ]);
        }
        $this->session->set_flashdata('message', $ok ? 'loanExtended' : 'loan_failed');
        redirect($this->_back());
    }

    /**
     * Bukti peminjaman (PDF) untuk ditandatangani peminjam & petugas.
     */
    public function print_slip($token = null)
    {
        $user = $this->Auth->get_active_user($this->session->userdata('username'));
        $data['loan']  = $this->_authorized_loan($token, $user);
        $data['state'] = loan_status($data['loan']);
        $data['title'] = 'Bukti Peminjaman Aset';

        $this->load->library('pdf');
        $this->pdf->setPaper('A5', 'landscape');
        $this->pdf->filename = 'bukti-' . str_replace('/', '-', $data['loan']['code']) . '.pdf';
        $this->pdf->load_view('loan/print_slip', $data);
    }

    // ------------------------------------------------------------------

    /**
     * @return array|false
     */
    private function _loan_input()
    {
        $name    = trim((string) $this->input->post('borrower_name', true));
        $unit    = trim((string) $this->input->post('borrower_unit', true));
        $phone   = trim((string) $this->input->post('borrower_phone', true));
        $purpose = trim((string) $this->input->post('purpose', true));
        $loaned  = (string) $this->input->post('loaned_date');
        $due     = (string) $this->input->post('due_date');

        $l = DateTime::createFromFormat('Y-m-d', $loaned);
        $d = DateTime::createFromFormat('Y-m-d', $due);
        $valid = $name !== ''
            && $l && $l->format('Y-m-d') === $loaned && $loaned <= date('Y-m-d')
            && $d && $d->format('Y-m-d') === $due && $due >= $loaned
            && ($phone === '' || preg_match('/^[0-9+\-\s()]{6,30}$/', $phone));

        if (!$valid) {
            return false;
        }

        return [
            'borrower_name'  => mb_substr($name, 0, 100),
            'borrower_unit'  => $unit !== '' ? mb_substr($unit, 0, 100) : null,
            'borrower_phone' => $phone !== '' ? $phone : null,
            'purpose'        => $purpose !== '' ? mb_substr($purpose, 0, 255) : null,
            'loaned_at'      => $loaned === date('Y-m-d') ? date('Y-m-d H:i:s') : $loaned . ' 08:00:00',
            'due_date'       => $due,
        ];
    }

    private function _authorized_loan($token, $user)
    {
        $id   = $token ? $this->encryption->decrypt(urlsafe_b64decode((string) $token)) : false;
        $loan = $id ? $this->Loan->get_loan((int) $id) : null;
        $ok   = $loan && ($user['role_id'] === '1' || (string) $user['building_id'] === (string) $loan['building_id']);

        if (!$ok) {
            $this->session->set_flashdata('message', 'loan_failed');
            redirect('loan');
        }
        return $loan;
    }

    /** Kembali ke halaman asal form (loan atau scan/<serial>), hanya path internal. */
    private function _back()
    {
        $back = (string) $this->input->post('back');
        return preg_match('#^(loan|scan/)[A-Za-z0-9_\-/?=&%.]*$#', $back) ? $back : 'loan';
    }

    private function _token($id)
    {
        return urlsafe_b64encode($this->encryption->encrypt((string) $id));
    }
}
