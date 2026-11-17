<?php

/**
 * Pembacaan audit log (penulisan lewat library Audit).
 */
class Audit_model extends CI_Model
{
    /** Label modul untuk filter & tampilan */
    const MODULES = [
        'auth'        => 'Login / logout',
        'asset'       => 'Data aset',
        'transaction' => 'Pindah aset',
        'opname'      => 'Stock opname',
        'disposal'    => 'Penghapusan aset',
        'maintenance' => 'Maintenance',
        'loan'        => 'Peminjaman',
        'master'      => 'Data master',
        'user'        => 'Pengguna',
        'report'      => 'Laporan & ekspor',
        'audit'       => 'Audit log',
    ];

    private function _filtered(array $f)
    {
        $this->db->from('audit_logs AS a');
        $this->db->join('buildings AS b', 'b.id = a.building_id', 'left');

        if (!empty($f['date_from'])) {
            $this->db->where('a.created_at >=', $f['date_from'] . ' 00:00:00');
        }
        if (!empty($f['date_to'])) {
            $this->db->where('a.created_at <=', $f['date_to'] . ' 23:59:59');
        }
        if (!empty($f['user_id'])) {
            $this->db->where('a.user_id', (int) $f['user_id']);
        }
        if (!empty($f['module'])) {
            $this->db->where('a.module', $f['module']);
        }
        if (!empty($f['action'])) {
            $this->db->where('a.action', $f['action']);
        }
        if (!empty($f['building_id'])) {
            $this->db->where('a.building_id', (int) $f['building_id']);
        }
        if (!empty($f['entity_type']) && isset($f['entity_id'])) {
            $this->db->where(['a.entity_type' => $f['entity_type'], 'a.entity_id' => (string) $f['entity_id']]);
        }
        if (!empty($f['q'])) {
            $this->db->group_start();
            $this->db->like('a.description', $f['q']);
            $this->db->or_like('a.entity_label', $f['q']);
            $this->db->or_like('a.username', $f['q']);
            $this->db->or_like('a.ip_address', $f['q']);
            $this->db->group_end();
        }
    }

    public function count($f)
    {
        $this->_filtered($f);
        return (int) $this->db->count_all_results();
    }

    public function get($f, $limit = 50, $offset = 0)
    {
        $this->db->select('a.*, b.name AS building_name');
        $this->_filtered($f);
        $this->db->order_by('a.id', 'DESC');
        if ($limit) {
            $this->db->limit((int) $limit, (int) $offset);
        }
        return $this->db->get()->result_array();
    }

    /** Ringkasan untuk kartu: jumlah aktivitas, user aktif, login gagal, ekspor. */
    public function summary($f)
    {
        $this->db->select("COUNT(*) AS total, COUNT(DISTINCT a.user_id) AS users,
            SUM(a.action = 'auth.login_failed') AS login_failed,
            SUM(a.module = 'report') AS exports,
            SUM(a.action IN ('asset.delete', 'user.delete', 'master.building_delete', 'disposal.approve')) AS critical", false);
        $this->_filtered($f);
        return array_map('intval', $this->db->get()->row_array() ?: []);
    }

    /** Daftar aksi yang pernah tercatat (untuk dropdown filter). */
    public function actions()
    {
        return array_column($this->db->select('DISTINCT action', false)->order_by('action')->get('audit_logs')->result_array(), 'action');
    }

    public function users()
    {
        return $this->db->select('id_user, username')->order_by('username')->get('user')->result_array();
    }

    /** Aktivitas terakhir satu user (halaman profil). */
    public function for_user($user_id, $limit = 15)
    {
        return $this->db->order_by('id', 'DESC')->limit($limit)->get_where('audit_logs', ['user_id' => $user_id])->result_array();
    }

    /** Hapus log lebih lama dari N hari (retensi). @return int jumlah baris terhapus */
    public function purge_older_than($days)
    {
        $this->db->where('created_at <', date('Y-m-d 00:00:00', strtotime('-' . (int) $days . ' days')))->delete('audit_logs');
        return $this->db->affected_rows();
    }
}
