<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Email "ada kiriman aset masuk" ke petugas gedung tujuan.
 *
 *   $this->load->library('transaction_notifier');
 *   $result = $this->transaction_notifier->send($header_id);          // kirim
 *   $result = $this->transaction_notifier->send($header_id, true);    // dry run (tidak kirim / tidak menandai)
 *
 * Dipanggil langsung setelah transaksi dibuat (kalau $config['transaction_mail_instant'] aktif)
 * dan oleh `php index.php cron transaction_notifications` untuk mencoba ulang yang belum terkirim.
 * Gagal kirim tidak boleh menggagalkan pemindahan aset, jadi semua error hanya dicatat ke log.
 *
 * Hasil: ['status' => sent|partial|failed|no_recipient|nothing, 'code' => .., 'recipients' => [...], 'failed' => [...]]
 */
class Transaction_notifier
{
    /** @var CI_Controller */
    private $ci;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('Transactions_model', 'TrxNotify');
        $this->ci->load->library('encryption');
        $this->ci->load->helper('url_crypto');
    }

    public function send($header_id, $dry_run = false)
    {
        $result = ['status' => 'nothing', 'code' => null, 'recipients' => [], 'failed' => []];

        try {
            $trx = $this->ci->TrxNotify->notification_payload($header_id);
            if (!$trx) {
                return $result; // semua aset sudah diterima / ditolak
            }
            $result['code'] = $trx['code'];

            $recipients = $this->ci->TrxNotify->notification_recipients($trx['building_id'], $trx['created_by']);
            if (empty($recipients)) {
                $result['status'] = 'no_recipient';
                return $result;
            }

            $subject = '[Asset Management] ' . count($trx['items']) . ' aset dikirim ke ' . $trx['room_name']
                . ($trx['building_name'] ? ', ' . $trx['building_name'] : '') . ' (' . $trx['code'] . ')';
            // link langsung ke detail transaksi masuk (token sama dengan halaman TI)
            $detail_url = site_url('TI/detail/' . urlsafe_b64encode($this->ci->encryption->encrypt($trx['code'])));

            foreach ($recipients as $recipient) {
                if ($dry_run) {
                    $result['recipients'][] = $recipient['email'];
                    continue;
                }

                $this->ci->email->clear();
                $this->ci->email->set_mailtype('html');
                $this->ci->email->from($this->mail_from(), 'Asset Management');
                $this->ci->email->to($recipient['email']);
                $this->ci->email->subject($subject);
                $this->ci->email->message($this->ci->load->view('TI/email_incoming', [
                    'recipient'  => $recipient,
                    'trx'        => $trx,
                    'detail_url' => $detail_url,
                ], true));

                if ($this->ci->email->send(false)) {
                    $result['recipients'][] = $recipient['email'];
                } else {
                    $result['failed'][] = $recipient['email'];
                    log_message('error', 'Transaction_notifier ' . $trx['code'] . ': ' . strip_tags($this->ci->email->print_debugger(['headers'])));
                }
            }

            if ($dry_run) {
                $result['status'] = 'sent';
                $result['subject'] = $subject;
                $result['items'] = $trx['items'];
                return $result;
            }

            if (!empty($result['recipients'])) {
                // cukup satu penerima berhasil supaya tidak dikirim berulang ke yang sudah menerima
                $this->ci->TrxNotify->mark_notified($trx['id']);
                $result['status'] = empty($result['failed']) ? 'sent' : 'partial';
            } else {
                $result['status'] = 'failed';
            }
        } catch (\Throwable $e) {
            log_message('error', 'Transaction_notifier: ' . $e->getMessage());
            $result['status'] = 'failed';
        }

        return $result;
    }

    /**
     * Alamat pengirim: notification_mail_from > maintenance_mail_from > noreply.
     */
    public function mail_from()
    {
        return $this->ci->config->item('notification_mail_from')
            ?: ($this->ci->config->item('maintenance_mail_from') ?: 'noreply@asset-management.test');
    }
}
