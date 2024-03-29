<?php

class Assets_model extends CI_Model
{
    var $table = 'asset';
    var $column_order = ['asset.id_asset', 'asset.asset_name', 'asset.merk', 'asset.serial_number', 'asset.placement_status', 'rooms.name AS room_name', 'rooms.id AS room_id', 'asset.created'];
    var $column_search = ['asset.id_asset', 'asset.asset_name', 'asset.merk', 'asset.serial_number', 'rooms.name', 'asset.placement_status', 'asset.created'];
    var $order = ['id_asset' => 'desc'];

    private function _get_datatables_query($role_id, $building_id)
    {
        if ($role_id == 1) {
            $this->db->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number, asset.placement_status, rooms.name, rooms.id AS room_id, asset.created');
            $this->db->from('asset');
            $this->db->join('rooms', 'rooms.id = asset.room_id');
        } else {
            $this->db->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number, asset.placement_status, rooms.name, rooms.id AS room_id, asset.created');
            $this->db->from('asset');
            $this->db->join('rooms', 'rooms.id = asset.room_id');
            $this->db->join('floors', 'floors.id = rooms.floor_id');
            $this->db->join('buildings', 'buildings.id = floors.building_id');
            $this->db->where('buildings.id', $building_id);
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
        if (isset($_POST['order'])) { // here order processing
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
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
        return $this->db->count_all_results();
    }

    public function menu_model($role_id, $building_id)
    {
        if ($role_id == '1') {
            $this->db->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number, asset.placement_status, rooms.name, rooms.id AS room_id, asset.created');
            $this->db->from('asset');
            $this->db->join('rooms', 'rooms.id = asset.room_id');
            $this->db->order_by('id_asset', 'DESC');
            // return $this->db->generate();
            return $this->db->get()->result_array();
        } else {
            $this->db->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number, asset.placement_status, rooms.name, rooms.id AS room_id, asset.created');
            $this->db->from('asset');
            $this->db->join('rooms', 'rooms.id = asset.room_id');
            $this->db->join('floors', 'floors.id = rooms.floor_id');
            $this->db->join('buildings', 'buildings.id = floors.building_id');
            $this->db->order_by('id_asset', 'DESC');
            $this->db->where('buildings.id', $building_id);
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
            // return $this->db->get()->result_array();
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
            // return $this->db->get()->result_array();
        }
    }

    public function reject_model($location, $assetId, $id)
    {
        $this->db->set('status', 2);
        $this->db->where('id', $id);
        $this->db->update('transactions');
        $this->db->set('placement_status', 1);
        $this->db->where('id_asset', $assetId);
        $this->db->update('asset');
        return $this->db->affected_rows();
    }

    public function get_total_row($role, $room_id)
    {
        if ($role == '1') {
            $this->db->select('*');
            $this->db->from('asset');
            $this->db->join('rooms', 'rooms.id = asset.room_id');
            return $this->db->get()->num_rows();
        } else {
            return $this->db->get_where('asset', ['room_id' => $room_id])->num_rows();
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
        $this->db->order_by('asset_location', 'ASC');
        return $this->db->get('asset')->result_array();
    }

    public function filter_asset_by_room_id($role_id, $room_id, $building_id)
    {
        // when user running the filter by room name
        if ($room_id == null) {
            $query = "SELECT asset.asset_name, COUNT(*) AS total FROM asset INNER JOIN rooms ON asset.room_id = rooms.id INNER JOIN floors ON rooms.floor_id = floors.id INNER JOIN buildings ON floors.building_id = buildings.id WHERE buildings.id = $building_id GROUP BY asset_name LIMIT 10";
            // if user login as administrator
            if ($role_id == '1') {
                $query = "SELECT asset.asset_name, COUNT(*) AS total FROM asset INNER JOIN rooms ON asset.room_id = rooms.id INNER JOIN floors ON rooms.floor_id = floors.id INNER JOIN buildings ON floors.building_id = buildings.id GROUP BY asset_name ORDER BY RAND() LIMIT 10";
            }
        } else {
            $query = "SELECT asset.asset_name, COUNT(*) AS total FROM asset WHERE room_id = '$room_id' GROUP BY asset_name ORDER BY RAND() LIMIT 10";
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
        return $this->db->count_all_results('asset');
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
        $this->db->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number, asset.placement_status, rooms.name, rooms.id AS room_id');
        $this->db->from('asset');
        $this->db->join('rooms', 'rooms.id = asset.room_id');
        $this->db->where('asset.room_id', $room_id);
        return $this->db->get()->result_array();
    }

    public function update_placement_status($id)
    {
        $this->db->set('placement_status', 0);
        $this->db->where('id_asset', $id);
        $this->db->update('asset');
        return $this->db->affected_rows();
    }

    public function update_asset_location($id, $room_id)
    {
        $this->db->set('room_id', $room_id);
        $this->db->set('placement_status', 1);
        $this->db->where('id_asset', $id);
        $this->db->update('asset');
        return $this->db->affected_rows();
    }

    public function bulk_asset_transaction($asset_ids)
    {
        $this->db->set('placement_status', 0);
        $this->db->where_in('id_asset', $asset_ids);
        $this->db->update('asset');
        return $result = $this->db->affected_rows();
    }

    public function get_asset_by_id($id)
    {
        return $this->db->get_where('asset', ['id_asset' => $id])->row_array();
    }

    public function update_room_id_by_asset_id($room_ids, $asset_ids)
    {
        foreach (array_combine($asset_ids, $room_ids) as $asset_id => $room_id) {
            $result[] = [
                'id_asset' => $asset_id,
                'room_id' => $room_id,
                'placement_status' => 1,
            ];
        }
        $this->db->update_batch('asset', $result, 'id_asset');
        return $this->db->affected_rows();
    }

    public function delete_user($id)
    {
        $this->db->delete('user', ['id_user' => $id]);
        return $this->db->affected_rows();
    }
}
