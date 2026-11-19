<?php

/**
 * Riwayat hidup satu aset: menggabungkan semua kejadian dari tiap modul ke satu timeline.
 *
 * Setiap event: [
 *   'at'    => 'Y-m-d H:i:s' (untuk urutan),
 *   'date_only' => bool (sumber hanya punya tanggal, jam tidak ditampilkan),
 *   'type'  => kunci jenis (lihat TYPES),
 *   'title' => kalimat utama, 'detail' => keterangan tambahan (boleh null),
 *   'user'  => username pelaku (boleh null), 'tone' => warna bootstrap untuk titik timeline
 * ]
 * Semua teks belum di-escape; escape dilakukan di view.
 */
class Asset_history_model extends CI_Model
{
    const TYPES = [
        'register'    => ['label' => 'Pendaftaran & pembelian', 'icon' => 'fa-box'],
        'transfer'    => ['label' => 'Perpindahan', 'icon' => 'fa-exchange-alt'],
        'condition'   => ['label' => 'Kondisi', 'icon' => 'fa-heartbeat'],
        'maintenance' => ['label' => 'Maintenance', 'icon' => 'fa-tools'],
        'loan'        => ['label' => 'Peminjaman', 'icon' => 'fa-hand-holding'],
        'opname'      => ['label' => 'Stock opname', 'icon' => 'fa-clipboard-check'],
        'disposal'    => ['label' => 'Penghapusan', 'icon' => 'fa-recycle'],
        'edit'        => ['label' => 'Perubahan data', 'icon' => 'fa-pen'],
    ];

    /** Nama field asset yang mudah dibaca, untuk event "Perubahan data". */
    const FIELD_LABELS = [
        'asset_name' => 'nama', 'merk' => 'merk', 'serial_number' => 'nomor seri', 'category_id' => 'kategori',
        'purchase_date' => 'tanggal perolehan', 'purchase_price' => 'harga', 'vendor' => 'vendor', 'warranty_until' => 'garansi',
    ];

    public function timeline(array $asset)
    {
        $id     = (int) $asset['id_asset'];
        $events = [];

        // --- pendaftaran & pembelian
        if (!empty($asset['purchase_date'])) {
            $events[] = $this->_event($asset['purchase_date'], true, 'register', 'Dibeli' . (!empty($asset['vendor']) ? ' dari ' . $asset['vendor'] : ''),
                $asset['purchase_price'] !== null ? 'Harga perolehan ' . rupiah($asset['purchase_price']) : null, null, 'secondary');
        }
        if (!empty($asset['created'])) {
            $events[] = $this->_event($asset['created'], true, 'register', 'Didaftarkan ke sistem', 'Nomor seri ' . $asset['serial_number'], null, 'secondary');
        }

        // --- perpindahan (transaction_details)
        $rows = $this->db->select('td.status, td.sent, td.received, td.created_at, td.updated_at, t.transaction_id AS code, t.notes,
                src.name AS source_name, dst.name AS dest_name')
            ->from('transaction_details AS td')
            ->join('transaction AS t', 't.id = td.transaction_id', 'left')
            ->join('rooms AS src', 'src.id = td.source_id', 'left')
            ->join('rooms AS dst', 'dst.id = td.room_id', 'left')
            ->where('td.asset_id', $id)->order_by('td.id', 'ASC')->get()->result_array();
        foreach ($rows as $r) {
            $route = ($r['source_name'] ?? '-') . ' → ' . ($r['dest_name'] ?? '-');
            $sent_date_only = !$r['created_at'] || substr($r['created_at'], 11) === '00:00:00';
            $events[] = $this->_event($r['created_at'] ?: $r['sent'], $sent_date_only, 'transfer', 'Dikirim ' . $route,
                trim(($r['code'] ?? '') . ($r['notes'] ? ' · ' . $r['notes'] : '')) ?: null, null, (int) $r['status'] === 0 ? 'warning' : 'info');
            if ((int) $r['status'] === 1) {
                $events[] = $this->_event($r['updated_at'] ?: $r['received'], !$r['updated_at'], 'transfer', 'Diterima di ' . ($r['dest_name'] ?? '-'), $r['code'], null, 'info');
            } elseif ((int) $r['status'] === 2) {
                $events[] = $this->_event($r['updated_at'] ?: $r['sent'], !$r['updated_at'], 'transfer', 'Kiriman ke ' . ($r['dest_name'] ?? '-') . ' ditolak, aset tetap di ' . ($r['source_name'] ?? '-'), $r['code'], null, 'secondary');
            }
        }

        // --- kondisi
        $sources = ['manual' => 'daftar aset', 'scan' => 'scan QR', 'opname' => 'stock opname', 'maintenance' => 'maintenance', 'loan' => 'pengembalian pinjaman'];
        $rows = $this->db->select('l.*, u.username')->from('asset_condition_logs AS l')
            ->join('user AS u', 'u.id_user = l.user_id', 'left')
            ->where('l.asset_id', $id)->get()->result_array();
        foreach ($rows as $r) {
            $tone = (int) $r['new_condition'] === 1 ? 'success' : ((int) $r['new_condition'] === 2 ? 'warning' : 'danger');
            $events[] = $this->_event($r['created_at'], false, 'condition',
                'Kondisi ' . asset_condition_label($r['old_condition']) . ' → ' . asset_condition_label($r['new_condition']),
                trim(($r['notes'] ?? '') . ' (via ' . ($sources[$r['source']] ?? $r['source']) . ')'), $r['username'], $tone);
        }

        // --- maintenance
        $rows = $this->db->select('l.*, s.title, u.username')->from('maintenance_logs AS l')
            ->join('maintenance_schedules AS s', 's.id = l.schedule_id', 'left')
            ->join('user AS u', 'u.id_user = l.performed_by', 'left')
            ->where('l.asset_id', $id)->get()->result_array();
        foreach ($rows as $r) {
            $title = ($r['type'] === 'rutin' ? 'Servis' . ($r['title'] ? ' "' . $r['title'] . '"' : ' rutin') : 'Perbaikan') . ' · ' . rupiah($r['cost']);
            $events[] = $this->_event($r['performed_at'], true, 'maintenance', $title,
                trim($r['description'] . ($r['vendor'] ? ' · ' . $r['vendor'] : '')), $r['username'], $r['type'] === 'rutin' ? 'primary' : 'danger');
        }

        // --- peminjaman
        $rows = $this->db->select('l.*, c.username AS created_name, r.username AS returned_name')->from('asset_loans AS l')
            ->join('user AS c', 'c.id_user = l.created_by', 'left')
            ->join('user AS r', 'r.id_user = l.returned_by', 'left')
            ->where('l.asset_id', $id)->get()->result_array();
        foreach ($rows as $r) {
            $events[] = $this->_event($r['loaned_at'], false, 'loan',
                'Dipinjam ' . $r['borrower_name'] . ($r['borrower_unit'] ? ' (' . $r['borrower_unit'] . ')' : ''),
                'Batas kembali ' . date('d M Y', strtotime($r['due_date'])) . ($r['purpose'] ? ' · ' . $r['purpose'] : '') . ' · ' . $r['code'],
                $r['created_name'], 'info');
            if ($r['returned_at']) {
                $late = (int) ((strtotime(substr($r['returned_at'], 0, 10)) - strtotime($r['due_date'])) / 86400);
                $events[] = $this->_event($r['returned_at'], false, 'loan',
                    'Dikembalikan ' . $r['borrower_name'] . ($late > 0 ? ' (telat ' . $late . ' hari)' : ''),
                    'Kondisi ' . asset_condition_label($r['condition_in']) . ($r['return_notes'] ? ' · ' . $r['return_notes'] : ''),
                    $r['returned_name'], $late > 0 ? 'warning' : 'success');
            } elseif ($r['due_date'] < date('Y-m-d')) {
                $events[] = $this->_event($r['due_date'] . ' 23:59:59', true, 'loan', 'Batas kembali terlewat, belum dikembalikan', $r['code'], null, 'danger');
            }
        }

        // --- stock opname
        $results = [0 => ['tidak ditemukan', 'danger'], 1 => ['ditemukan', 'success'], 2 => ['ditemukan di ruangan lain', 'warning']];
        $rows = $this->db->select('d.result, d.checked_at, o.code, o.status, o.started_at, o.finished_at, rooms.name AS room_name, u.username')
            ->from('stock_opname_details AS d')
            ->join('stock_opname AS o', 'o.id = d.stock_opname_id')
            ->join('rooms', 'rooms.id = o.room_id', 'left')
            ->join('user AS u', 'u.id_user = o.user_id', 'left')
            ->where('d.asset_id', $id)->get()->result_array();
        foreach ($rows as $r) {
            $open = (int) $r['status'] === 0;
            if ($open && (int) $r['result'] === 0) {
                $label = ['belum dicek', 'secondary'];
            } else {
                $label = $results[(int) $r['result']] ?? ['-', 'secondary'];
            }
            $events[] = $this->_event($r['checked_at'] ?: ($r['finished_at'] ?: $r['started_at']), false, 'opname',
                'Stock opname ruang ' . ($r['room_name'] ?? '-') . ': ' . $label[0], $r['code'] . ($open ? ' (berjalan)' : ''), $r['username'], $label[1]);
        }

        // --- penghapusan
        $rows = $this->db->select('d.status, d.reviewed_at, d.review_notes, d.created_at, h.code, h.method, h.reason, req.username AS requester, rev.username AS reviewer')
            ->from('asset_disposal_details AS d')
            ->join('asset_disposals AS h', 'h.id = d.disposal_id')
            ->join('user AS req', 'req.id_user = h.requested_by', 'left')
            ->join('user AS rev', 'rev.id_user = d.reviewed_by', 'left')
            ->where('d.asset_id', $id)->get()->result_array();
        foreach ($rows as $r) {
            $events[] = $this->_event($r['created_at'], false, 'disposal', 'Diajukan untuk dihapus (' . $r['code'] . ')',
                mb_substr((string) $r['reason'], 0, 160), $r['requester'], 'warning');
            if ((int) $r['status'] === 1) {
                $events[] = $this->_event($r['reviewed_at'], false, 'disposal', 'Penghapusan disetujui, aset dihapus dari daftar aktif', $r['review_notes'], $r['reviewer'], 'dark');
            } elseif ((int) $r['status'] === 2) {
                $events[] = $this->_event($r['reviewed_at'], false, 'disposal', 'Pengajuan penghapusan ditolak', $r['review_notes'], $r['reviewer'], 'secondary');
            }
        }

        // --- perubahan data (dari audit log)
        $rows = $this->db->select('created_at, username, changes')->from('audit_logs')
            ->where(['entity_type' => 'asset', 'entity_id' => (string) $id, 'action' => 'asset.update'])
            ->get()->result_array();
        foreach ($rows as $r) {
            $changes = json_decode((string) $r['changes'], true) ?: [];
            $fields  = array_map(function ($f) {
                return self::FIELD_LABELS[$f] ?? $f;
            }, array_keys($changes));
            $events[] = $this->_event($r['created_at'], false, 'edit', 'Data aset diubah', $fields ? 'Field: ' . implode(', ', $fields) : null, $r['username'], 'secondary');
        }

        // Terbaru di atas. Kalau tanggal sama dan salah satu kejadian hanya punya tanggal (jam tidak
        // tercatat, mis. data lama), pakai urutan kejadian asli (seq) supaya "dikirim" tidak tampil
        // setelah "diterima" hanya karena jamnya 00:00.
        usort($events, function ($a, $b) {
            $day = strcmp(substr($b['at'], 0, 10), substr($a['at'], 0, 10));
            if ($day !== 0) {
                return $day;
            }
            if ($a['date_only'] || $b['date_only']) {
                return $b['seq'] <=> $a['seq'];
            }
            return strcmp($b['at'], $a['at']) ?: $b['seq'] <=> $a['seq'];
        });
        return $events;
    }

    private $seq = 0;

    private function _event($at, $date_only, $type, $title, $detail, $user, $tone)
    {
        $at = (string) $at;
        if (strlen($at) === 10) {
            $at .= ' 00:00:00';
        }
        return [
            'at'        => $at,
            'date_only' => (bool) $date_only,
            'type'      => $type,
            'title'     => $title,
            'detail'    => $detail !== '' ? $detail : null,
            'user'      => $user,
            'tone'      => $tone,
            'seq'       => ++$this->seq,
        ];
    }
}
