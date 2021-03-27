<?php

class Assets_model extends CI_Model
{

    public function login_model($email)
    {
        return $this->db->get_where('user', ['email' => $email])->row_array();
    }

    public function menu_model($loc, $building_id)
    {
        if ($loc == 'IT') {
            $this->db->select('*');
            $this->db->from('asset');
            $this->db->join('rooms', 'rooms.id = asset.room_id');
            $this->db->order_by('id_asset', 'DESC');
            return $this->db->get()->result_array();
        } else {
            $this->db->select('asset.id_asset, asset.asset_name, asset.merk, asset.serial_number, asset.placement_status, rooms.name');
            $this->db->from('asset');
            $this->db->join('rooms', 'rooms.id = asset.room_id');
            $this->db->join('floors', 'floors.id = rooms.floor_id');
            $this->db->join('buildings', 'buildings.id = floors.building_id');
            $this->db->order_by('id_asset', 'DESC');
            $this->db->where('buildings.id', $building_id);
            return $this->db->get()->result_array();
        }
    }

    public function update_profile_model($name, $email, $newImage)
    {
        if ($newImage !== null) {
            $this->db->set('image', $newImage);
        }
        $this->db->set('fullname', $name);
        $this->db->where('email', $email);
        $this->db->update('user');
    }

    public function change_password_model($password_hash, $email)
    {
        $this->db->set('password', $password_hash);
        $this->db->where('email', $email);
        $this->db->update('user');
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

    public function take_out_process($location)
    {
        $this->db->select('*');
        $this->db->from('detail_process');
        $this->db->join('asset', 'asset.id_asset = detail_process.asset_id');
        $this->db->where('source', $location);
        $this->db->where('deleted', 0);
        return $this->db->get()->result_array();
    }

    public function print_model($location)
    {
        $this->db->select('*');
        $this->db->from('detail_process');
        $this->db->join('asset', 'asset.id_asset = detail_process.asset_id');
        $this->db->where('deleted', 0);
        $this->db->where_in('id_detail_process', $location);
        return $this->db->get()->result_array();
        // return $this->db->get_where('detail_process', ['deleted' => 0])->result_array();
    }

    public function add_asset_model($data)
    {
        $check = $this->db->get_where('asset', ['serial_number' => $data['serial_number']])->row_array();

        if ($check) {
            return 0;
        } else {
            $this->db->insert('asset', $data);
            return $this->db->affected_rows();
        }
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

    // public function get_location()
    // {
    //     $this->db->order_by('store_code', 'ASC');
    //     return $this->db->get('store_location')->result_array();
    // }

    public function edit_asset_model($data, $id)
    {
        $this->db->where('id_asset', $id);
        $this->db->update('asset', $data);
        return $this->db->affected_rows();
    }

    public function search_data_model($loc)
    {
        if ($loc == 'IT') {
            $keyword = $this->input->post('keyword', true);
            $this->db->like('serial_number', $keyword);
            $this->db->or_like('asset_name', $keyword);
            $this->db->or_like('merk', $keyword);
            $this->db->or_like('asset_name', $keyword);
            $this->db->or_like('origin_location', $keyword);
            $this->db->or_like('asset_location', $keyword);
            if ($keyword == 'Bad' || $keyword == 'BAD' || $keyword == 'bad') {
                $keyword = 1;
            } elseif ($keyword == 'Good') {
                $keyword = 0;
            }
            $this->db->or_like('status', $keyword);

            return $this->db->get('asset')->result_array();
        } else {
            $keyword = $this->input->post('keyword', true);
            $this->db->group_start();
            $this->db->like('asset_name', $keyword);
            $this->db->or_like('merk', $keyword);
            $this->db->or_like('serial_number', $keyword);
            $this->db->or_like('asset_location', $keyword);
            $this->db->group_end();
            return $this->db->get_where('asset', ['asset_location' => $loc])->result_array();
        }
    }

    public function reject_model($location, $assetId, $id)
    {
        $this->db->delete('detail_process', ['id_detail_process' => $id]);
        $this->db->set('asset_location', $location);
        $this->db->where('id_asset', $assetId);
        $this->db->update('asset');
        return $this->db->affected_rows();
    }

    public function get_total_row($role, $room_id)
    {
        if ($role == 'IT') {
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

    public function count_asset($order)
    {
        if ($order == null) {
            $query = "SELECT asset.asset_name, COUNT(*) AS total FROM asset GROUP BY asset_name";
        } else {
            $query = "SELECT asset.asset_name, COUNT(*) AS total FROM asset WHERE asset_location = '$order' GROUP BY asset_name";
        }

        $sql = $this->db->query($query)->result_array();

        return $sql;
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
        // var_dump($location);
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
        $this->db->select('asset.asset_name, asset.merk, asset.serial_number, rooms.name');
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
}
