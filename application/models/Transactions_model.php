<?php

class Transactions_model extends CI_Model {

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

    public function bulk_transaction($asset_ids, $room_id)
    {
        foreach($asset_ids as $asset_id){
            $result[] = [
                'asset_id' => $asset_id,
                'room_id'  => $room_id,
                'status'   => 0,
                'sent'     => date('Y-m-d'),
                'created_at' => date('Y-m-d'),
            ];
        }

        $this->db->insert_batch('transactions', $result);
        return $this->db->affected_rows();
    }
}