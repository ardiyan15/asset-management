<?php

class Assets_model extends CI_Model
{
    var $table = 'asset';
    // urutan sama dengan kolom tabel di views/menu/index.php (null = tidak bisa di-sort)
    var $column_order = [null, 'asset.asset_name', 'asset.merk', 'asset.serial_number', 'rooms.name', 'asset.asset_condition', 'asset.created', null];
    var $column_search = ['asset.id_asset', 'asset.asset_name', 'asset.merk', 'asset.serial_number', 'rooms.name', 'asset.placement_status', 'asset.created'];
    var $order = ['id_asset' => 'desc'];

    private function _get_datatables_query($role_id, $building_id)
    {
        if ($role_id == 1) {
            $this->db->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number, asset.placement_status, asset.asset_condition, rooms.name, rooms.id AS room_id, asset.created');
            $this->db->from('asset');
            $this->db->join('rooms', 'rooms.id = asset.room_id');
        } else {
            $this->db->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number, asset.placement_status, asset.asset_condition, rooms.name, rooms.id AS room_id, asset.created');
            $this->db->from('asset');
            $this->db->join('rooms', 'rooms.id = asset.room_id');
            $this->db->join('floors', 'floors.id = rooms.floor_id');
            $this->db->join('buildings', 'buildings.id = floors.building_id');
            $this->db->where('buildings.id', $building_id);
        }

        $this->db->where('asset.is_disposed', 0);

        $condition = (int) $this->input->post('condition');
        if (is_valid_asset_condition($condition)) {
            $this->db->where('asset.asset_condition', $condition);
        }

        $i = 0;

        foreach ($this->column_search as $item) {
            if ($_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }
        $order_col = $this->column_order[(int) ($_POST['order'][0]['column'] ?? -1)] ?? null;
        if (isset($_POST['order']) && $order_col) { // here order processing
            $this->db->order_by($order_col, $_POST['order'][0]['dir'] === 'asc' ? 'ASC' : 'DESC');
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables($role_id, $building_id)
    {
        $this->_get_datatables_query($role_id, $building_id);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($role_id, $building_id)
    {
        $this->_get_datatables_query($role_id, $building_id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        $this->db->where('is_disposed', 0);
        return $this->db->count_all_results();
    }

    public function menu_model($role_id, $building_id)
    {
        if ($role_id == '1') {
            $this->db->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number, asset.placement_status, asset.asset_condition, rooms.name, rooms.id AS room_id, asset.created');
            $this->db->from('asset');
            $this->db->join('rooms', 'rooms.id = asset.room_id');
            $this->db->where('asset.is_disposed', 0);
            $this->db->order_by('id_asset', 'DESC');
            // return $this->db->generate();
            return $this->db->get()->result_array();
        } else {
            $this->db->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number, asset.placement_status, asset.asset_condition, rooms.name, rooms.id AS room_id, asset.created');
            $this->db->from('asset');
            $this->db->join('rooms', 'rooms.id = asset.room_id');
            $this->db->join('floors', 'floors.id = rooms.floor_id');
            $this->db->join('buildings', 'buildings.id = floors.building_id');
            $this->db->order_by('id_asset', 'DESC');
            $this->db->where('buildings.id', $building_id);
            $this->db->where('asset.is_disposed', 0);
            return $this->db->get()->result_array();
        }
    }

    public function check_user_token($token)
    {
        return $this->db->get_where('user_token', ['token' => $token])->row_array();
    }

    public function activate_user($email)
    {
        $this->db->set('is_active', 1);
        $this->db->where('email', $email);
        $this->db->update('user');

        $this->db->delete('user_token', ['email' => $email]);
    }

    public function forgot_password_model($email)
    {
        return $this->db->get_where('user', ['email' => $email, 'is_active' => 1])->row_array();
    }

    public function create_user_token($user_token)
    {
        $this->db->insert('user_token', $user_token);
    }

    public function reset_password_model($email, $password)
    {
        $this->db->set('password', $password);
        $this->db->where('email', $email);
        $this->db->update('user');
    }

    public function take_out_model($id, $data, $dtlPrces)
    {
        $this->db->set('asset_location', 'In Shipping');
        $this->db->set('status', 0);
        $this->db->where('id_asset', $id);
        $this->db->update('asset');
        $this->db->insert('detail_process', $dtlPrces);
        return $this->db->affected_rows();
    }

    public function print_model($location)
    {
        $this->db->select('*');
        $this->db->from('detail_process');
        $this->db->join('asset', 'asset.id_asset = detail_process.asset_id');
        $this->db->where('deleted', 0);
        $this->db->where_in('id_detail_process', $location);
        return $this->db->get()->result_array();
    }

    public function add_asset_model($data)
    {
        // true if array is multidimensional
        if (count($data) !== count($data, COUNT_RECURSIVE)) {
            $this->db->insert_batch('asset', $data);
        } else {
            $this->db->insert('asset', $data);
        }
        return $this->db->affected_rows();
    }

    public function delete_ast_model($id)
    {
        $check_ti = $this->db->get_where('detail_process', ['id_detail_process' => $id])->row_array();
        if ($check_ti) {
            $this->db->where('id_detail_process', $id);
            $this->db->where('deleted', 0);
            $this->db->delete('detail_process');
        }

        $this->db->where('id_asset', $id);
        $this->db->delete('asset');
        return $this->db->affected_rows();
    }

    public function update_asset_by_id($data, $id)
    {
        $this->db->where('id_asset', $id);
        $this->db->update('asset', $data);
        return $this->db->affected_rows();
    }

    public function search_data_model($role_id, $building_id)
    {
        if ($role_id == "1") {
            $keyword = $this->input->post('keyword', true);
            $this->datatables->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number, rooms.name, asset.placement_status, asset.created');
            $this->datatables->from('asset');
            $this->datatables->join('rooms', 'rooms.id = asset.room_id');
            $this->datatables->like('asset.serial_number', $keyword);
            $this->datatables->or_like('asset.asset_name', $keyword);
            $this->datatables->or_like('asset.merk', $keyword);
            $this->datatables->or_like('asset.created', $keyword);
            $this->datatables->or_like('rooms.name', $keyword);
            $this->datatables->order_by('id_asset', 'DESC');
            return $this->datatables->generate();
        } else {
            $keyword = $this->input->post('keyword', true);
            $this->datatables->select('asset.asset_name, asset.merk, asset.serial_number, rooms.name, asset.placement_status, asset.created');
            $this->datatables->from('asset');
            $this->datatables->join('rooms', 'rooms.id = asset.room_id');
            $this->datatables->join('floors', 'floors.id = rooms.floor_id');
            $this->datatables->join('buildings', 'buildings.id = floors.building_id');
            $this->datatables->group_start();
            $this->datatables->like('asset_name', $keyword);
            $this->datatables->or_like('asset.serial_number', $keyword);
            $this->datatables->or_like('asset.merk', $keyword);
            $this->datatables->or_like('rooms.name', $keyword);
            $this->datatables->group_end();
            $this->datatables->where('buildings.id', $building_id);
            return $this->datatables->generate();
        }
    }

    public function get_total_row($role, $room_id)
    {
        if ($role == '1') {
            $this->db->select('*');
            $this->db->from('asset');
            $this->db->join('rooms', 'rooms.id = asset.room_id');
            $this->db->where('asset.is_disposed', 0);
            return $this->db->get()->num_rows();
        } else {
            return $this->db->get_where('asset', ['room_id' => $room_id, 'is_disposed' => 0])->num_rows();
        }
    }

    public function get_data_history_asset_out($location)
    {
        $this->db->select('*');
        $this->db->from('detail_process');
        $this->db->join('asset', 'asset.id_asset = detail_process.asset_id');
        $this->db->where('source', $location);
        $this->db->where('deleted', 1);
        return $this->db->get()->result_array();
    }

    public function get_data_history_asset_in($location)
    {
        $this->db->select('*');
        $this->db->from('detail_process');
        $this->db->join('asset', 'asset.id_asset = detail_process.asset_id');
        $this->db->where('destination', $location);
        $this->db->where('deleted', 1);
        return $this->db->get()->result_array();
    }

    public function get_report_model()
    {
        $this->db->where('is_disposed', 0);
        $this->db->order_by('asset_location', 'ASC');
        return $this->db->get('asset')->result_array();
    }

    public function filter_asset_by_room_id($role_id, $room_id, $building_id)
    {
        // when user running the filter by room name
        if ($room_id == null) {
            $query = "SELECT asset.asset_name, COUNT(*) AS total FROM asset INNER JOIN rooms ON asset.room_id = rooms.id INNER JOIN floors ON rooms.floor_id = floors.id INNER JOIN buildings ON floors.building_id = buildings.id WHERE buildings.id = $building_id AND asset.is_disposed = 0 GROUP BY asset_name LIMIT 10";
            // if user login as administrator
            if ($role_id == '1') {
                $query = "SELECT asset.asset_name, COUNT(*) AS total FROM asset INNER JOIN rooms ON asset.room_id = rooms.id INNER JOIN floors ON rooms.floor_id = floors.id INNER JOIN buildings ON floors.building_id = buildings.id WHERE asset.is_disposed = 0 GROUP BY asset_name ORDER BY RAND() LIMIT 10";
            }
        } else {
            $query = "SELECT asset.asset_name, COUNT(*) AS total FROM asset WHERE room_id = '$room_id' AND asset.is_disposed = 0 GROUP BY asset_name ORDER BY RAND() LIMIT 10";
        }
        return $this->db->query($query)->result_array();
    }

    public function activate_list_user($id)
    {
        $this->db->set('is_active', 1);
        $this->db->where('id_user', $id);
        $this->db->update('user');
        return $this->db->affected_rows();
    }

    public function deactivate_list_user($id)
    {
        $this->db->set('is_active', 0);
        $this->db->where('id_user', $id);
        $this->db->update('user');
        return $this->db->affected_rows();
    }

    public function get_all_store_location()
    {
        $this->db->order_by('store_code', 'ASC');
        return $this->db->get('store_location')->result_array();
    }

    public function addStrLocationMdl($data)
    {
        $this->db->insert('store_location', $data);
        return $this->db->affected_rows();
    }

    public function editStrLocationMdl($data, $id)
    {
        $this->db->where('id_location', $id);
        $this->db->update('store_location', $data);
        return $this->db->affected_rows();
    }

    public function deactivate_store($id)
    {
        $this->db->set('status_location', 0);
        $this->db->where('id_location', $id);
        $this->db->update('store_location');
        return $this->db->affected_rows();
    }

    public function activated_store($id)
    {
        $this->db->set('status_location', 1);
        $this->db->where('id_location', $id);
        $this->db->update('store_location');
        return $this->db->affected_rows();
    }

    public function get_total_asset()
    {
        return $this->db->where('is_disposed', 0)->count_all_results('asset');
    }

    public function get_total_user_row()
    {
        return $this->db->count_all_results('user');
    }

    public function search_data_user()
    {
        $keyword = $this->input->post('keyword', true);
        $this->db->like('user_code', $keyword);
        $this->db->or_like('fullname', $keyword);
        $this->db->or_like('email', $keyword);
        return $this->db->get('user')->result_array();
    }

    public function get_name_location($room_id)
    {
        $this->db->select('rooms.name');
        $this->db->from('rooms');
        $this->db->where('id', $room_id);
        return $this->db->get()->row_array();
    }

    public function get_code_location($location)
    {
        $this->db->select('buildings.name');
        $this->db->from('rooms');
        $this->db->join('floors', 'rooms.floor_id = floors.id');
        $this->db->join('buildings', 'floors.building_id = buildings.id');
        $this->db->where('rooms.name', $location);
        // $this->db->order_by('buildings.id', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_last_asset_serial_number()
    {
        $this->db->select('serial_number');
        $this->db->from('asset');
        $this->db->order_by('id_asset', 'DESC');
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function get_asset_by_room_id($room_id)
    {
        $this->db->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number, asset.placement_status, asset.asset_condition, rooms.name, rooms.id AS room_id');
        $this->db->from('asset');
        $this->db->join('rooms', 'rooms.id = asset.room_id');
        $this->db->where('asset.room_id', $room_id);
        $this->db->where('asset.is_disposed', 0);
        return $this->db->get()->result_array();
    }

    /**
     * Aset + gedungnya, untuk cek hak akses sebelum mengubah data aset.
     */
    public function get_asset_with_building($asset_id)
    {
        $this->db->select('asset.id_asset, asset.asset_name, asset.serial_number, asset.room_id,
            asset.asset_condition, asset.is_disposed, buildings.id AS building_id');
        $this->db->from('asset');
        $this->db->join('rooms', 'rooms.id = asset.room_id');
        $this->db->join('floors', 'floors.id = rooms.floor_id');
        $this->db->join('buildings', 'buildings.id = floors.building_id');
        $this->db->where('asset.id_asset', $asset_id);
        return $this->db->get()->row_array();
    }

    /**
     * Ubah kondisi aset dan catat log-nya dalam satu DB transaction.
     *
     * @param string   $source    manual | scan | opname
     * @return string  updated | unchanged | failed
     */
    public function update_condition($asset_id, $new_condition, $notes, $user_id, $source = 'manual', $opname_id = null)
    {
        $asset = $this->db->select('asset_condition')->get_where('asset', ['id_asset' => $asset_id])->row_array();
        if (!$asset) {
            return 'failed';
        }

        $old_condition = (int) $asset['asset_condition'];
        if ($old_condition === (int) $new_condition) {
            return 'unchanged';
        }

        $notes = trim((string) $notes);

        $this->db->trans_begin();

        $this->db->where('id_asset', $asset_id)->update('asset', ['asset_condition' => (int) $new_condition]);
        $this->db->insert('asset_condition_logs', [
            'asset_id'        => $asset_id,
            'old_condition'   => $old_condition,
            'new_condition'   => (int) $new_condition,
            'notes'           => $notes !== '' ? mb_substr($notes, 0, 255) : null,
            'source'          => $source,
            'stock_opname_id' => $opname_id,
            'user_id'         => $user_id,
            'created_at'      => date('Y-m-d H:i:s'),
        ]);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return 'failed';
        }

        $this->db->trans_commit();
        return 'updated';
    }

    /**
     * Riwayat perubahan kondisi satu aset (terbaru dulu).
     */
    public function get_condition_logs($asset_id)
    {
        $this->db->select('asset_condition_logs.*, user.username, stock_opname.code AS opname_code');
        $this->db->from('asset_condition_logs');
        $this->db->join('user', 'user.id_user = asset_condition_logs.user_id', 'left');
        $this->db->join('stock_opname', 'stock_opname.id = asset_condition_logs.stock_opname_id', 'left');
        $this->db->where('asset_condition_logs.asset_id', $asset_id);
        $this->db->order_by('asset_condition_logs.id', 'DESC');
        return $this->db->get()->result_array();
    }

    /**
     * Jumlah aset per kondisi (di-scope per gedung untuk non-admin), untuk dashboard.
     * @return array [1 => n, 2 => n, 3 => n]
     */
    public function count_by_condition($role_id, $building_id)
    {
        $this->db->select('asset.asset_condition, COUNT(*) AS total');
        $this->db->from('asset');
        if ($role_id !== '1') {
            $this->db->join('rooms', 'rooms.id = asset.room_id');
            $this->db->join('floors', 'floors.id = rooms.floor_id');
            $this->db->where('floors.building_id', $building_id);
        }
        $this->db->where('asset.is_disposed', 0);
        $this->db->group_by('asset.asset_condition');

        $result = array_fill_keys(array_keys(asset_condition_options()), 0);
        foreach ($this->db->get()->result_array() as $row) {
            $result[(int) $row['asset_condition']] = (int) $row['total'];
        }
        return $result;
    }



    /**
     * Detail aset lengkap dengan lokasi (ruangan → lantai → gedung) berdasarkan nomor seri.
     */
    public function get_asset_detail_by_serial($serial_number)
    {
        $this->db->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number,
            asset.placement_status, asset.asset_condition, asset.is_disposed, asset.disposed_at, asset.created,
            asset.purchase_date, asset.purchase_price, asset.vendor, asset.warranty_until,
            categories.name AS category_name, categories.useful_life_years, categories.residual_percent,
            rooms.id AS room_id, rooms.name AS room_name, rooms.description AS room_description,
            floors.name AS floor_name, buildings.id AS building_id, buildings.name AS building_name');
        $this->db->from('asset');
        $this->db->join('categories', 'categories.id = asset.category_id', 'left');
        $this->db->join('rooms', 'rooms.id = asset.room_id');
        $this->db->join('floors', 'floors.id = rooms.floor_id');
        $this->db->join('buildings', 'buildings.id = floors.building_id');
        $this->db->where('asset.serial_number', $serial_number);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    /**
     * Data aset untuk cetak label QR, di-scope per gedung untuk non-admin.
     * Filter berdasarkan ruangan dan/atau daftar id aset tertentu.
     */
    public function get_label_assets($role_id, $building_id, $room_id = null, array $asset_ids = [])
    {
        $this->db->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number,
            rooms.name AS room_name, floors.name AS floor_name, buildings.name AS building_name');
        $this->db->from('asset');
        $this->db->join('rooms', 'rooms.id = asset.room_id');
        $this->db->join('floors', 'floors.id = rooms.floor_id');
        $this->db->join('buildings', 'buildings.id = floors.building_id');

        $this->db->where('asset.is_disposed', 0);
        if ($role_id !== '1') {
            $this->db->where('buildings.id', $building_id);
        }
        if ($room_id !== null) {
            $this->db->where('asset.room_id', $room_id);
        }
        if (!empty($asset_ids)) {
            $this->db->where_in('asset.id_asset', $asset_ids);
        }

        $this->db->order_by('rooms.name', 'ASC');
        $this->db->order_by('asset.serial_number', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_asset_by_id($id)
    {
        return $this->db->get_where('asset', ['id_asset' => $id])->row_array();
    }

    public function delete_user($id)
    {
        $this->db->delete('user', ['id_user' => $id]);
        return $this->db->affected_rows();
    }
}
