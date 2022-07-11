<?php

class Transactions_model extends CI_Model
{

    public function create_transaction_asset($data)
    {
        $this->db->insert('transactions', $data);
        return $this->db->affected_rows();
    }

    public function get_data_transaction_by_id($id)
    {
        return $this->db->get_where('transactions', ['id' => $id])->row_array();
    }

    public function update_status_transaction_by_id($id)
    {
        date_default_timezone_set('Asia/Jakarta');

        $this->db->set('status', 1);
        $this->db->set('received', date('Y-m-d'));
        $this->db->set('updated_at', date('Y-m-d H:i:s'));
        $this->db->where('id', $id);
        $this->db->update('transactions');
        return $this->db->affected_rows();
    }

    public function bulk_transaction($asset_ids, $room_id, $source_ids)
    {
        foreach (array_combine($asset_ids, $source_ids) as $asset_id => $source_id) {
            $result[] = [
                'asset_id'      => $asset_id,
                'room_id'       => $room_id,
                'source_id'     => $source_id,
                'status'        => 0,
                'sent'          => date('Y-m-d'),
                'created_at'    => date('Y-m-d'),
            ];
        }
        $this->db->insert_batch('transactions', $result);
        return $this->db->affected_rows();
    }

    public function transaction_out_process($role_id, $building_id)
    {
        $this->db->select('asset.asset_name, asset.merk, asset.serial_number, rooms.name, transactions.sent, source.name AS source_name');
        $this->db->from('transactions');
        $this->db->join('asset', 'asset.id_asset = transactions.asset_id');
        $this->db->join('rooms', 'rooms.id = transactions.room_id');
        $this->db->join('rooms AS source', 'source.id = transactions.source_id');
        if ($role_id !== '1') {
            $this->db->join('floors', 'floors.id = source.floor_id');
            $this->db->join('buildings', 'buildings.id = floors.building_id');
            $this->db->where('buildings.id', $building_id);
        }
        $this->db->where(['transactions.status' => 0]);
        return $this->db->get()->result_array();
    }

    public function transactions_complete_out($role_id, $building_id)
    {
        $this->db->select('asset.asset_name, asset.merk, asset.serial_number, rooms.name, transactions.sent, source.name AS source, transactions.status');
        $this->db->from('transactions');
        $this->db->join('asset', 'asset.id_asset = transactions.asset_id');
        $this->db->join('rooms', 'rooms.id = transactions.room_id');
        $this->db->join('rooms AS source', 'source.id = transactions.source_id');
        $this->db->join('floors', 'floors.id = source.floor_id');
        $this->db->join('buildings', 'buildings.id = floors.building_id');
        if ($role_id !== '1') {
            $this->db->where('transactions.status != ', 0);
            $this->db->where('buildings.id', $building_id);
        } else {
            $this->db->where('transactions.status !=', 0);
        }
        return $this->db->get()->result_array();
    }

    public function transactions_complete_in($role_id, $building_id)
    {
        $this->db->select('asset.asset_name, asset.merk, asset.serial_number, rooms.name, transactions.sent, source.name AS source, transactions.received, transactions.status');
        $this->db->from('transactions');
        $this->db->join('asset', 'asset.id_asset = transactions.asset_id');
        $this->db->join('rooms AS source', 'source.id = transactions.source_id');
        $this->db->join('rooms', 'rooms.id = transactions.room_id');
        $this->db->join('floors', 'floors.id = rooms.floor_id');
        $this->db->join('buildings', 'buildings.id = floors.building_id');
        if ($role_id != '1') {
            $this->db->where('transactions.status != ', 0);
            $this->db->where('buildings.id', $building_id);
        } else {
            $this->db->where('transactions.status !=', 0);
        }
        return $this->db->get()->result_array();
    }

    public function get_transaction_room_id_by_asset_id($asset_ids)
    {
        $this->db->select('room_id');
        $this->db->from('transactions');
        $this->db->where('status', 0);
        $this->db->where_in('asset_id', $asset_ids);
        return $this->db->get()->result_array();
    }

    public function bulk_update($asset_ids)
    {
        $this->db->set('status', 1);
        $this->db->set('updated_at', date('Y-m-d H:i:s'));
        $this->db->set('received', date('Y-m-d'));
        $this->db->where_in('asset_id', $asset_ids);
        $this->db->update('transactions');
        return $this->db->affected_rows();
    }
}
