<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Tugas terjadwal, hanya bisa dijalankan dari CLI (Task Scheduler / cron), tidak lewat browser.
 *
 *   php index.php cron maintenance_reminders        kirim email pengingat jatuh tempo
 *   php index.php cron maintenance_reminders dry    tampilkan isi email tanpa mengirim / menandai
 *
 * Penerima: user aktif yang mengisi email di Edit Profil. Admin menerima semua gedung,
 * user lain hanya jadwal di gedungnya. Satu jadwal hanya diingatkan sekali per hari.
 * Pengaturan SMTP dibaca dari application/config/email.php (lihat email.example.php).
 */
class Cron extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            show_404();
        }
        $this->load->model('Maintenance_model', 'Maintenance');
        $this->load->helper('maintenance');
    }

    public function maintenance_reminders($mode = '')
    {
        $dry_run   = $mode === 'dry';
        $schedules = $this->Maintenance->due_for_notification();

        if (empty($schedules)) {
            $this->_out('Tidak ada jadwal maintenance yang perlu diingatkan hari ini.');
            return;
        }

        $recipients = $this->Maintenance->notification_recipients();
        if (empty($recipients)) {
            $this->_out(count($schedules) . ' jadwal jatuh tempo, tapi belum ada user aktif yang mengisi email. Isi email di menu Edit Profil.');
            return;
        }

        $sent_schedule_ids = [];
        $failed            = 0;

        foreach ($recipients as $recipient) {
            $items = array_values(array_filter($schedules, function ($s) use ($recipient) {
                return $recipient['role_id'] == 1 || (string) $recipient['building_id'] === (string) $s['building_id'];
            }));
            if (empty($items)) {
                continue;
            }

            $subject = '[Asset Management] ' . count($items) . ' jadwal maintenance perlu dikerjakan';
            $body    = $this->load->view('maintenance/email_reminder', [
                'recipient' => $recipient,
                'items'     => $items,
            ], true);

            if ($dry_run) {
                $this->_out('--- [DRY RUN] Ke: ' . $recipient['email'] . ' (' . $recipient['username'] . ')');
                $this->_out('Subjek: ' . $subject);
                foreach ($items as $s) {
                    $due = maintenance_due_status($s['next_due_date'], $s['remind_days_before']);
                    $this->_out('  - ' . $s['title'] . ' | ' . $s['asset_name'] . ' (' . $s['serial_number'] . ') | '
                        . $s['room_name'] . ', ' . $s['building_name'] . ' | ' . $s['next_due_date'] . ' | ' . $due['label']);
                }
                continue;
            }

            $this->email->clear();
            $this->email->set_mailtype('html');
            $this->email->from($this->config->item('maintenance_mail_from') ?: 'noreply@asset-management.test', 'Asset Management');
            $this->email->to($recipient['email']);
            $this->email->subject($subject);
            $this->email->message($body);

            if ($this->email->send(false)) {
                $this->_out('Terkirim ke ' . $recipient['email'] . ' (' . count($items) . ' jadwal)');
                $sent_schedule_ids = array_merge($sent_schedule_ids, array_column($items, 'id'));
            } else {
                $failed++;
                $this->_out('GAGAL kirim ke ' . $recipient['email'] . '. Cek pengaturan SMTP di application/config/email.php.');
                log_message('error', 'Cron maintenance_reminders: ' . strip_tags($this->email->print_debugger(['headers'])));
            }
        }

        if (!$dry_run) {
            $this->Maintenance->mark_notified(array_unique($sent_schedule_ids));
        }
        $this->_out(sprintf('Selesai: %d jadwal jatuh tempo, %d ditandai sudah diingatkan, %d pengiriman gagal.%s',
            count($schedules), count(array_unique($sent_schedule_ids)), $failed, $dry_run ? ' (dry run, tidak ada yang dikirim)' : ''));
    }

    /**
     * Pengingat peminjaman aset yang terlambat dikembalikan, ke petugas gedung asal aset.
     *   php index.php cron loan_reminders [dry]
     */
    public function loan_reminders($mode = '')
    {
        $this->load->model('Loan_model', 'Loan');
        $this->load->helper('loan');

        $dry_run = $mode === 'dry';
        $loans   = $this->Loan->overdue_for_notification();

        if (empty($loans)) {
            $this->_out('Tidak ada peminjaman terlambat yang perlu diingatkan hari ini.');
            return;
        }

        $recipients = $this->Maintenance->notification_recipients();
        if (empty($recipients)) {
            $this->_out(count($loans) . ' peminjaman terlambat, tapi belum ada user aktif yang mengisi email. Isi email di menu Edit Profil.');
            return;
        }

        $sent_ids = [];
        $failed   = 0;

        foreach ($recipients as $recipient) {
            $items = array_values(array_filter($loans, function ($l) use ($recipient) {
                return $recipient['role_id'] == 1 || (string) $recipient['building_id'] === (string) $l['building_id'];
            }));
            if (empty($items)) {
                continue;
            }

            $subject = '[Asset Management] ' . count($items) . ' peminjaman aset terlambat dikembalikan';

            if ($dry_run) {
                $this->_out('--- [DRY RUN] Ke: ' . $recipient['email'] . ' (' . $recipient['username'] . ')');
                $this->_out('Subjek: ' . $subject);
                foreach ($items as $l) {
                    $this->_out('  - ' . $l['code'] . ' | ' . $l['asset_name'] . ' (' . $l['serial_number'] . ') | ' . $l['borrower_name']
                        . ($l['borrower_phone'] ? ' ' . $l['borrower_phone'] : '') . ' | batas ' . $l['due_date'] . ' | ' . loan_status($l)['label']);
                }
                continue;
            }

            $this->email->clear();
            $this->email->set_mailtype('html');
            $this->email->from($this->config->item('maintenance_mail_from') ?: 'noreply@asset-management.test', 'Asset Management');
            $this->email->to($recipient['email']);
            $this->email->subject($subject);
            $this->email->message($this->load->view('loan/email_overdue', ['recipient' => $recipient, 'items' => $items], true));

            if ($this->email->send(false)) {
                $this->_out('Terkirim ke ' . $recipient['email'] . ' (' . count($items) . ' peminjaman)');
                $sent_ids = array_merge($sent_ids, array_column($items, 'id'));
            } else {
                $failed++;
                $this->_out('GAGAL kirim ke ' . $recipient['email'] . '. Cek pengaturan SMTP di application/config/email.php.');
                log_message('error', 'Cron loan_reminders: ' . strip_tags($this->email->print_debugger(['headers'])));
            }
        }

        if (!$dry_run) {
            $this->Loan->mark_notified(array_unique($sent_ids));
        }
        $this->_out(sprintf('Selesai: %d peminjaman terlambat, %d ditandai sudah diingatkan, %d pengiriman gagal.%s',
            count($loans), count(array_unique($sent_ids)), $failed, $dry_run ? ' (dry run, tidak ada yang dikirim)' : ''));
    }

    /**
     * Jalankan semua pengingat harian sekaligus (untuk satu entri Task Scheduler).
     *   php index.php cron daily [dry]
     */
    public function daily($mode = '')
    {
        $this->_out('== Maintenance ==');
        $this->maintenance_reminders($mode);
        $this->_out('== Peminjaman ==');
        $this->loan_reminders($mode);
        $this->_out('== Audit log ==');
        $this->audit_cleanup($mode);
    }

    /**
     * Hapus audit log yang lebih lama dari $config['audit_retention_days'] (0 = tidak dihapus).
     *   php index.php cron audit_cleanup [dry]
     */
    public function audit_cleanup($mode = '')
    {
        $days = (int) $this->config->item('audit_retention_days');
        if ($days <= 0) {
            $this->_out('Retensi audit log tidak diatur (audit_retention_days = 0), semua log disimpan.');
            return;
        }

        $this->load->model('Audit_model', 'AuditLog');
        $cutoff = date('Y-m-d', strtotime('-' . $days . ' days'));
        if ($mode === 'dry') {
            $count = $this->db->where('created_at <', $cutoff . ' 00:00:00')->count_all_results('audit_logs');
            $this->_out('[DRY RUN] ' . $count . ' log sebelum ' . $cutoff . ' akan dihapus.');
            return;
        }

        $deleted = $this->AuditLog->purge_older_than($days);
        // Penghapusan log juga dicatat supaya tidak ada celah yang tidak terlihat
        $this->audit->log('audit.purge', 'Hapus ' . $deleted . ' audit log sebelum ' . $cutoff . ' (retensi ' . $days . ' hari)');
        $this->_out('Dihapus ' . $deleted . ' audit log sebelum ' . $cutoff . '.');
    }

    private function _out($line)
    {
        echo $line . PHP_EOL;
    }
}
