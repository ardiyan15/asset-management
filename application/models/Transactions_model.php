<?php

class Transactions_model extends CI_Model
{

    public function create_transaction_asset($data)
    {

        $this->db->trans_begin();

        try {
            $transactionId = generate_transaction_id('AST');

            $transaction = [
                'transaction_id' => $transactionId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'notes' => $data['notes'],
            ];

            $result = $this->db->insert('transaction', $transaction);

            unset($data['notes']);
            $data['transaction_id'] = $result['id'];

            $this->db->insert('transaction_details', $data);

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('DB Transaction Failed');
            }

            $this->db->trans_commit();

            // return $this->db->affected_rows();
            return $transactionId;
        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            throw $e;
        }
    }

    public function get_data_transaction_by_id($id)
    {
        return $this->db->get_where('transaction_details', ['id' => $id])->row_array();
    }

    public function update_status_transaction_by_id($id)
    {
        date_default_timezone_set('Asia/Jakarta');

        $this->db->set('status', 1);
        $this->db->set('received', date('Y-m-d'));
        $this->db->set('updated_at', date('Y-m-d H:i:s'));
        $this->db->where('id', $id);
        $this->db->update('transaction_details');
        return $this->db->affected_rows();
    }

    public function bulk_transaction($asset_ids, $room_id, $source_ids)
    {
        foreach (array_combine($asset_ids, $source_ids) as $asset_id => $source_id) {
            $result[] = [
                'asset_id' => $asset_id,
                'room_id' => $room_id,
                'source_id' => $source_id,
                'status' => 0,
                'sent' => date('Y-m-d'),
                'created_at' => date('Y-m-d'),
            ];
        }
        $this->db->insert_batch('transactions', $result);
        return $this->db->affected_rows();
    }

    public function transaction_out_process($role_id, $building_id)
    {
        $this->db->select('asset.asset_name, asset.merk, asset.serial_number, rooms.name, transaction_details.sent, source.name AS source_name');
        $this->db->from('transaction_details');
        $this->db->join('asset', 'asset.id_asset = transaction_details.asset_id');
        $this->db->join('rooms', 'rooms.id = transaction_details.room_id');
        $this->db->join('rooms AS source', 'source.id = transaction_details.source_id');
        if ($role_id !== '1') {
            $this->db->join('floors', 'floors.id = source.floor_id');
            $this->db->join('buildings', 'buildings.id = floors.building_id');
            $this->db->where('buildings.id', $building_id);
        }
        $this->db->where(['transaction_details.status' => 0]);
        return $this->db->get()->result_array();
    }

    public function transactions_complete_out($role_id, $building_id)
    {
        $this->db->select('asset.asset_name, asset.merk, asset.serial_number, rooms.name, transaction_details.sent, source.name AS source, transaction_details.status');
        $this->db->from('transaction_details');
        $this->db->join('asset', 'asset.id_asset = transaction_details.asset_id');
        $this->db->join('rooms', 'rooms.id = transaction_details.room_id');
        $this->db->join('rooms AS source', 'source.id = transaction_details.source_id');
        $this->db->join('floors', 'floors.id = source.floor_id');
        $this->db->join('buildings', 'buildings.id = floors.building_id');
        if ($role_id !== '1') {
            $this->db->where('transaction_details.status != ', 0);
            $this->db->where('buildings.id', $building_id);
        } else {
            $this->db->where('transaction_details.status !=', 0);
        }
        return $this->db->get()->result_array();
    }

    public function transactions_complete_in($role_id, $building_id)
    {
        $this->db->select('asset.asset_name, asset.merk, asset.serial_number, rooms.name, transaction_details.sent, source.name AS source, transaction_details.received, transaction_details.status');
        $this->db->from('transaction_details');
        $this->db->join('asset', 'asset.id_asset = transaction_details.asset_id');
        $this->db->join('rooms AS source', 'source.id = transaction_details.source_id');
        $this->db->join('rooms', 'rooms.id = transaction_details.room_id');
        $this->db->join('floors', 'floors.id = rooms.floor_id');
        $this->db->join('buildings', 'buildings.id = floors.building_id');
        if ($role_id != '1') {
            $this->db->where('transaction_details.status != ', 0);
            $this->db->where('buildings.id', $building_id);
        } else {
            $this->db->where('transaction_details.status !=', 0);
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
