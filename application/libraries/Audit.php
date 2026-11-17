<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Pencatat audit log: siapa melakukan apa, terhadap data apa, kapan, dan dari mana.
 *
 *   $this->audit->log('asset.update', 'Ubah data aset AC 2 PK', [
 *       'entity_type'  => 'asset',
 *       'entity_id'    => 270,
 *       'entity_label' => 'L505SAN21040027',
 *       'changes'      => Audit::diff($old, $new, ['asset_name', 'merk']),
 *       'building_id'  => 13,
 *   ]);
 *
 * Gagal mencatat tidak boleh menggagalkan aksi utama, jadi error hanya ditulis ke log aplikasi.
 * Jangan pernah memasukkan password / token ke description atau changes.
 */
class Audit
{
    /** @var CI_Controller */
    private $ci;

    /** user yang sedang login (di-cache per request); false = belum dicari */
    private $user = false;

    /** Field yang tidak boleh ikut tercatat di changes */
    const SENSITIVE = ['password', 'password1', 'password2', 'new_password1', 'new_password2', 'current_password', 'token', 'smtp_pass'];

    public function __construct()
    {
        $this->ci = &get_instance();
    }

    /**
     * @param string $action      kode bertitik, mis. 'asset.create'
     * @param string $description kalimat yang dibaca manusia
     * @param array  $opts        entity_type, entity_id, entity_label, changes (array), building_id,
     *                            user (array id_user/username/role_id, untuk login sebelum session terisi)
     */
    public function log($action, $description, array $opts = [])
    {
        try {
            $user    = $opts['user'] ?? $this->current_user();
            $changes = isset($opts['changes']) && is_array($opts['changes']) ? $this->strip_sensitive($opts['changes']) : null;
            $is_cli  = is_cli();

            $row = [
                'user_id'      => $user['id_user'] ?? null,
                'username'     => $user['username'] ?? ($is_cli ? 'system' : null),
                'role_id'      => $user['role_id'] ?? null,
                'action'       => mb_substr($action, 0, 60),
                'module'       => mb_substr(strtok($action, '.'), 0, 30),
                'description'  => mb_substr($description, 0, 255),
                'entity_type'  => isset($opts['entity_type']) ? mb_substr($opts['entity_type'], 0, 30) : null,
                'entity_id'    => isset($opts['entity_id']) ? mb_substr((string) $opts['entity_id'], 0, 64) : null,
                'entity_label' => isset($opts['entity_label']) ? mb_substr((string) $opts['entity_label'], 0, 150) : null,
                'changes'      => $changes ? json_encode($changes, JSON_UNESCAPED_UNICODE) : null,
                'building_id'  => isset($opts['building_id']) && $opts['building_id'] !== '' ? (int) $opts['building_id'] : null,
                'ip_address'   => $is_cli ? null : $this->ci->input->ip_address(),
                'user_agent'   => $is_cli ? 'cli' : mb_substr((string) $this->ci->input->user_agent(), 0, 255),
                'url'          => $is_cli ? null : mb_substr($this->ci->uri->uri_string(), 0, 255),
                'created_at'   => date('Y-m-d H:i:s'),
            ];

            // Jangan sampai error DB di sini memunculkan halaman error CI
            $debug = $this->ci->db->db_debug;
            $this->ci->db->db_debug = false;
            $ok = $this->ci->db->insert('audit_logs', $row);
            $this->ci->db->db_debug = $debug;

            if (!$ok) {
                log_message('error', 'Audit log gagal disimpan: ' . json_encode($this->ci->db->error()));
            }
            return (bool) $ok;
        } catch (\Throwable $e) {
            log_message('error', 'Audit log gagal: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Perbedaan dua array data. Hanya field yang berubah yang dikembalikan.
     * @return array ['field' => ['old' => .., 'new' => ..]]
     */
    public static function diff(array $old, array $new, array $fields = null)
    {
        $fields  = $fields ?? array_keys($new);
        $changes = [];
        foreach ($fields as $field) {
            if (!array_key_exists($field, $new)) {
                continue;
            }
            $before = $old[$field] ?? null;
            $after  = $new[$field];
            // Bandingkan sebagai teks supaya "12000000.00" vs "12000000" tidak dianggap berubah
            $norm = function ($v) {
                if ($v === null || $v === '') {
                    return '';
                }
                return is_numeric($v) ? (string) (0 + $v) : (string) $v;
            };
            if ($norm($before) !== $norm($after)) {
                $changes[$field] = ['old' => $before, 'new' => $after];
            }
        }
        return $changes;
    }

    /** Gedung dari aset (untuk building_id log). */
    public function building_of_asset($asset_id)
    {
        $row = $this->ci->db->select('floors.building_id')
            ->from('asset')
            ->join('rooms', 'rooms.id = asset.room_id')
            ->join('floors', 'floors.id = rooms.floor_id')
            ->where('asset.id_asset', (int) $asset_id)
            ->get()->row_array();
        return $row ? (int) $row['building_id'] : null;
    }

    private function current_user()
    {
        if ($this->user === false) {
            $this->user = null;
            $username = is_cli() ? null : $this->ci->session->userdata('username');
            if ($username) {
                $this->user = $this->ci->db->select('id_user, username, role_id, building_id')
                    ->get_where('user', ['username' => $username])->row_array();
            }
        }
        return $this->user;
    }

    private function strip_sensitive(array $changes)
    {
        foreach (array_keys($changes) as $field) {
            if (in_array(strtolower($field), self::SENSITIVE, true)) {
                unset($changes[$field]);
            }
        }
        return $changes;
    }
}
