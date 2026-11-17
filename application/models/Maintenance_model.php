<?php

/**
 * Jadwal maintenance aset + riwayat perawatan/perbaikan.
 * Semua query daftar dibatasi ke aset aktif (belum dihapus) dan, untuk non-admin, ke gedung user.
 */
class Maintenance_model extends CI_Model
{
    const TYPES = [
        'rutin'     => 'Perawatan rutin',
        'perbaikan' => 'Perbaikan',
    ];

    private function _join_location($alias = 'asset')
    {
        $this->db->join('rooms', 'rooms.id = ' . $alias . '.room_id');
        $this->db->join('floors', 'floors.id = rooms.floor_id');
        $this->db->join('buildings', 'buildings.id = floors.building_id');
    }

    private function _scope($role_id, $building_id, $filter_building = 0)
    {
        if ($role_id !== '1') {
            $this->db->where('buildings.id', $building_id);
        } elseif ($filter_building) {
            $this->db->where('buildings.id', (int) $filter_building);
        }
    }

    // ------------------------------------------------------------------
    // Jadwal
    // ------------------------------------------------------------------

    private function _schedule_select()
    {
        $this->db->select('s.*, asset.asset_name, asset.merk, asset.serial_number, asset.asset_condition,
            rooms.name AS room_name, buildings.id AS building_id, buildings.name AS building_name,
            DATEDIFF(s.next_due_date, CURDATE()) AS days_until', false);
        $this->db->from('maintenance_schedules AS s');
        $this->db->join('asset', 'asset.id_asset = s.asset_id');
        $this->_join_location();
        $this->db->where('asset.is_disposed', 0);
    }

    /**
     * $status: due (terlambat + segera), overdue, soon, upcoming, inactive, all
     */
    public function get_schedules($role_id, $building_id, $status = 'due', $filter_building = 0)
    {
        $this->_schedule_select();
        $this->_scope($role_id, $building_id, $filter_building);

        $remind = 'DATEDIFF(s.next_due_date, CURDATE()) <= s.remind_days_before';
        switch ($status) {
            case 'due':
                $this->db->where('s.is_active', 1)->where($remind, null, false);
                break;
            case 'overdue':
                $this->db->where('s.is_active', 1)->where('s.next_due_date <=', date('Y-m-d'));
                break;
            case 'soon':
                $this->db->where('s.is_active', 1)->where('s.next_due_date >', date('Y-m-d'))->where($remind, null, false);
                break;
            case 'upcoming':
                $this->db->where('s.is_active', 1)->where('DATEDIFF(s.next_due_date, CURDATE()) > s.remind_days_before', null, false);
                break;
            case 'inactive':
                $this->db->where('s.is_active', 0);
                break;
        }

        $this->db->order_by('s.is_active', 'DESC');
        $this->db->order_by('s.next_due_date', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Jumlah jadwal per status (untuk tab & kartu ringkasan).
     */
    public function count_by_status($role_id, $building_id, $filter_building = 0)
    {
        $this->db->select("SUM(s.is_active = 1 AND s.next_due_date <= CURDATE()) AS overdue,
            SUM(s.is_active = 1 AND s.next_due_date > CURDATE() AND DATEDIFF(s.next_due_date, CURDATE()) <= s.remind_days_before) AS soon,
            SUM(s.is_active = 1 AND DATEDIFF(s.next_due_date, CURDATE()) > s.remind_days_before) AS upcoming,
            SUM(s.is_active = 0) AS inactive, COUNT(*) AS total", false);
        $this->db->from('maintenance_schedules AS s');
        $this->db->join('asset', 'asset.id_asset = s.asset_id');
        $this->_join_location();
        $this->db->where('asset.is_disposed', 0);
        $this->_scope($role_id, $building_id, $filter_building);

        $row = $this->db->get()->row_array();
        $counts = array_map('intval', $row ?: []);
        $counts['due'] = ($counts['overdue'] ?? 0) + ($counts['soon'] ?? 0);
        return $counts;
    }

    /** Badge sidebar: jadwal yang terlambat atau masuk masa pengingat. */
    public function count_due($role_id, $building_id)
    {
        return $this->count_by_status($role_id, $building_id)['due'];
    }

    public function get_schedule($id)
    {
        $this->_schedule_select();
        $this->db->where('s.id', $id);
        return $this->db->get()->row_array();
    }

    public function create_schedule(array $data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('maintenance_schedules', $data);
        return (int) $this->db->insert_id();
    }

    public function update_schedule($id, array $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id)->update('maintenance_schedules', $data);
        return $this->db->error()['code'] == 0;
    }

    // ------------------------------------------------------------------
    // Riwayat perawatan / perbaikan
    // ------------------------------------------------------------------

    /**
     * Catat perawatan/perbaikan. Kalau berasal dari jadwal, jadwal digeser ke periode berikutnya
     * (dihitung dari tanggal servis). Kalau condition_after diisi, kondisi aset ikut diperbarui.
     *
     * @return int|false id log
     */
    public function record(array $log, $schedule = null)
    {
        $this->load->model('Assets_model', 'AssetsForMaintenance');

        $this->db->trans_begin();

        $log['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('maintenance_logs', $log);
        $log_id = (int) $this->db->insert_id();

        // Jadwal digeser ke periode berikutnya, dihitung dari tanggal servis. Kalau yang dicatat
        // servis lama (sebelum servis terakhir), jadwal dibiarkan supaya tidak mundur.
        $is_latest = $schedule && (empty($schedule['last_done_date']) || $log['performed_at'] >= $schedule['last_done_date']);
        if ($is_latest) {
            $this->db->where('id', $schedule['id'])->update('maintenance_schedules', [
                'last_done_date'   => $log['performed_at'],
                'next_due_date'    => maintenance_next_due($log['performed_at'], $schedule['interval_value'], $schedule['interval_unit']),
                'last_notified_at' => null,
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
        }

        if (!empty($log['condition_after'])) {
            $status = $this->AssetsForMaintenance->update_condition(
                $log['asset_id'],
                $log['condition_after'],
                ($schedule ? $schedule['title'] . ': ' : '') . mb_substr($log['description'], 0, 200),
                $log['performed_by'],
                'maintenance'
            );
            if ($status === 'failed') {
                $this->db->trans_rollback();
                return false;
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return $log_id;
    }

    /**
     * $filters: year, building_id (admin), type, asset_id
     */
    public function get_logs($role_id, $building_id, array $filters = [])
    {
        $this->db->select('l.*, asset.asset_name, asset.merk, asset.serial_number, asset.is_disposed,
            rooms.name AS room_name, buildings.name AS building_name,
            s.title AS schedule_title, user.username AS performed_by_name');
        $this->db->from('maintenance_logs AS l');
        $this->db->join('asset', 'asset.id_asset = l.asset_id');
        $this->_join_location();
        $this->db->join('maintenance_schedules AS s', 's.id = l.schedule_id', 'left');
        $this->db->join('user', 'user.id_user = l.performed_by', 'left');
        $this->_scope($role_id, $building_id, $filters['building_id'] ?? 0);

        if (!empty($filters['year'])) {
            $this->db->where('YEAR(l.performed_at)', (int) $filters['year'], false);
        }
        if (!empty($filters['type']) && isset(self::TYPES[$filters['type']])) {
            $this->db->where('l.type', $filters['type']);
        }
        if (!empty($filters['asset_id'])) {
            $this->db->where('l.asset_id', (int) $filters['asset_id']);
        }

        $this->db->order_by('l.performed_at', 'DESC');
        $this->db->order_by('l.id', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_log_years()
    {
        $rows = $this->db->select('DISTINCT YEAR(performed_at) AS y', false)->order_by('y', 'DESC')->get('maintenance_logs')->result_array();
        $years = array_map('intval', array_column($rows, 'y'));
        if (!in_array((int) date('Y'), $years, true)) {
            array_unshift($years, (int) date('Y'));
        }
        return $years;
    }

    // ------------------------------------------------------------------
    // Pendukung form, halaman scan, dan notifikasi
    // ------------------------------------------------------------------

    /** Aset aktif yang bisa dijadwalkan (untuk dropdown). */
    public function assets_for_select($role_id, $building_id)
    {
        $this->db->select('asset.id_asset, asset.asset_name, asset.serial_number, rooms.name AS room_name, buildings.name AS building_name');
        $this->db->from('asset');
        $this->_join_location();
        $this->db->where('asset.is_disposed', 0);
        $this->_scope($role_id, $building_id);
        $this->db->order_by('buildings.name', 'ASC');
        $this->db->order_by('asset.asset_name', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_asset_location($asset_id)
    {
        $this->db->select('asset.id_asset, asset.asset_name, asset.serial_number, asset.is_disposed, buildings.id AS building_id');
        $this->db->from('asset');
        $this->_join_location();
        $this->db->where('asset.id_asset', $asset_id);
        return $this->db->get()->row_array();
    }

    public function schedules_for_asset($asset_id)
    {
        return $this->db->order_by('is_active', 'DESC')->order_by('next_due_date', 'ASC')
            ->get_where('maintenance_schedules', ['asset_id' => $asset_id])->result_array();
    }

    public function logs_for_asset($asset_id, $limit = 5)
    {
        return $this->db->order_by('performed_at', 'DESC')->order_by('id', 'DESC')->limit($limit)
            ->get_where('maintenance_logs', ['asset_id' => $asset_id])->result_array();
    }

    public function total_cost_for_asset($asset_id)
    {
        $row = $this->db->select_sum('cost')->get_where('maintenance_logs', ['asset_id' => $asset_id])->row_array();
        return (float) ($row['cost'] ?? 0);
    }

    /**
     * Jadwal yang perlu diingatkan hari ini dan belum diingatkan hari ini.
     */
    public function due_for_notification()
    {
        $this->_schedule_select();
        $this->db->where('s.is_active', 1);
        $this->db->where('DATEDIFF(s.next_due_date, CURDATE()) <= s.remind_days_before', null, false);
        $this->db->group_start();
        $this->db->where('s.last_notified_at IS NULL', null, false);
        $this->db->or_where('DATE(s.last_notified_at) <', date('Y-m-d'));
        $this->db->group_end();
        $this->db->order_by('s.next_due_date', 'ASC');
        return $this->db->get()->result_array();
    }

    /** Penerima email: user aktif yang punya email (admin menerima semua gedung). */
    public function notification_recipients()
    {
        return $this->db->select('id_user, username, email, role_id, building_id')
            ->where(['is_active' => 1])
            ->where('email IS NOT NULL', null, false)
            ->where('email <>', '')
            ->get('user')->result_array();
    }

    public function mark_notified(array $schedule_ids)
    {
        if (empty($schedule_ids)) {
            return;
        }
        $this->db->where_in('id', $schedule_ids)->update('maintenance_schedules', ['last_notified_at' => date('Y-m-d H:i:s')]);
    }
}
