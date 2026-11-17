<?php

class Transactions_model extends CI_Model
{

    /**
     * Aset yang boleh dipindahkan ke $dest_room_id: ada di ruangan (placement_status 1),
     * belum berada di ruangan tujuan, dan untuk non-admin berada di gedung user.
     * Ruangan asal selalu diambil dari database, bukan dari input form.
     */
    public function get_movable_assets($role_id, $building_id, array $asset_ids, $dest_room_id)
    {
        if (empty($asset_ids)) {
            return [];
        }

        $this->db->select('asset.id_asset, asset.room_id');
        $this->db->from('asset');
        $this->db->where_in('asset.id_asset', $asset_ids);
        $this->db->where('asset.placement_status', 1);
        $this->db->where('asset.is_disposed', 0);
        $this->db->where('asset.room_id !=', $dest_room_id);
        // aset yang sedang diajukan penghapusan tidak boleh dipindah
        $this->db->where('NOT EXISTS (SELECT 1 FROM asset_disposal_details p WHERE p.asset_id = asset.id_asset AND p.status = 0)', null, false);
        // aset yang sedang dipinjam tidak boleh dipindah
        $this->db->where('NOT EXISTS (SELECT 1 FROM asset_loans al WHERE al.asset_id = asset.id_asset AND al.status = 0)', null, false);

        if ($role_id !== '1') {
            $this->db->join('rooms', 'rooms.id = asset.room_id');
            $this->db->join('floors', 'floors.id = rooms.floor_id');
            $this->db->where('floors.building_id', $building_id);
        }

        return $this->db->get()->result_array();
    }

    /**
     * Buat satu transaksi pemindahan (header + detail per aset) dan tandai aset
     * sedang dipindahkan. Dipakai untuk pindah satuan maupun massal.
     *
     * @param array $assets  hasil get_movable_assets(): [['id_asset' => .., 'room_id' => ..], ...]
     * @return string|false  kode transaksi (TRX/AST/...) atau false jika gagal
     */
    public function create_transaction(array $assets, $dest_room_id, $notes = null)
    {
        if (empty($assets)) {
            return false;
        }

        $now = date('Y-m-d H:i:s');

        $this->db->trans_begin();

        try {
            $code = generate_transaction_id('AST');

            $this->db->insert('transaction', [
                'transaction_id' => $code,
                'created_at'     => $now,
                'updated_at'     => $now,
                'notes'          => ($notes !== null && trim($notes) !== '') ? trim($notes) : null,
            ]);
            $header_id = $this->db->insert_id();

            $details = [];
            foreach ($assets as $asset) {
                $details[] = [
                    'transaction_id' => $header_id,
                    'asset_id'       => $asset['id_asset'],
                    'source_id'      => $asset['room_id'],
                    'room_id'        => $dest_room_id,
                    'status'         => 0,
                    'sent'           => date('Y-m-d'),
                    'created_at'     => $now,
                ];
            }
            $this->db->insert_batch('transaction_details', $details);

            $this->db->set('placement_status', 0);
            $this->db->where_in('id_asset', array_column($assets, 'id_asset'));
            $this->db->update('asset');

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('DB Transaction Failed');
            }

            $this->db->trans_commit();
            return $code;
        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            log_message('error', 'create_transaction: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Detail transaksi yang masih menunggu diterima (status 0).
     * Non-admin hanya boleh memproses kiriman ke ruangan di gedungnya sendiri.
     *
     * @param array $where  kondisi tambahan, mis. ['transaction_details.id' => 5]
     * @param array|null $asset_ids  filter where_in asset_id
     */
    public function get_pending_details($role_id, $building_id, array $where = [], $asset_ids = null)
    {
        $this->db->select('transaction_details.id, transaction_details.asset_id, transaction_details.room_id, transaction_details.source_id');
        $this->db->from('transaction_details');
        $this->db->where('transaction_details.status', 0);
        $this->db->where($where);

        if ($asset_ids !== null) {
            $this->db->where_in('transaction_details.asset_id', $asset_ids);
        }

        if ($role_id !== '1') {
            $this->db->join('rooms', 'rooms.id = transaction_details.room_id');
            $this->db->join('floors', 'floors.id = rooms.floor_id');
            $this->db->where('floors.building_id', $building_id);
        }

        return $this->db->get()->result_array();
    }

    /**
     * Terima kiriman: detail jadi status 1 dan aset pindah ke ruangan tujuan.
     */
    public function accept_details(array $details)
    {
        if (empty($details)) {
            return false;
        }

        $now = date('Y-m-d H:i:s');

        $this->db->trans_begin();

        foreach ($details as $detail) {
            $this->db->set('status', 1);
            $this->db->set('received', date('Y-m-d'));
            $this->db->set('updated_at', $now);
            $this->db->where('id', $detail['id']);
            $this->db->where('status', 0);
            $this->db->update('transaction_details');

            $this->db->set('room_id', $detail['room_id']);
            $this->db->set('placement_status', 1);
            $this->db->where('id_asset', $detail['asset_id']);
            $this->db->update('asset');
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();
        return true;
    }

    /**
     * Tolak kiriman: detail jadi status 2, aset tetap di ruangan asal dan bisa dipindah lagi.
     */
    public function reject_detail(array $detail)
    {
        $this->db->trans_begin();

        $this->db->set('status', 2);
        $this->db->set('updated_at', date('Y-m-d H:i:s'));
        $this->db->where('id', $detail['id']);
        $this->db->where('status', 0);
        $this->db->update('transaction_details');

        $this->db->set('placement_status', 1);
        $this->db->where('id_asset', $detail['asset_id']);
        $this->db->update('asset');

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();
        return true;
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
        $this->db->distinct();
        $this->db->select('transaction.transaction_id, transaction.created_at, transaction.notes');
        $this->db->from('transaction');
        $this->db->join('transaction_details', 'transaction_details.transaction_id = transaction.id');
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
        $this->db->distinct();
        $this->db->select('transaction.transaction_id, transaction.created_at, transaction.notes');
        $this->db->from('transaction');
        $this->db->join('transaction_details', 'transaction_details.transaction_id = transaction.id');
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

    public function get_transaction_in_by_id($transaction_id)
    {
        // Get transaction basic info
        $this->db->select('transaction.transaction_id, transaction.created_at, transaction.notes');
        $this->db->from('transaction');
        $this->db->where('transaction.transaction_id', $transaction_id);
        $transaction = $this->db->get()->row_array();

        if (!$transaction) {
            return null;
        }

        // Get assets in this transaction
        $this->db->select('asset.asset_name, asset.merk, asset.serial_number, transaction_details.status, source.name AS source_name, rooms.name AS destination_name');
        $this->db->from('transaction_details');
        $this->db->join('transaction', 'transaction.id = transaction_details.transaction_id');
        $this->db->join('asset', 'asset.id_asset = transaction_details.asset_id');
        $this->db->join('rooms AS source', 'source.id = transaction_details.source_id');
        $this->db->join('rooms', 'rooms.id = transaction_details.room_id');
        $this->db->where('transaction.transaction_id', $transaction_id);
        $assets = $this->db->get()->result_array();

        $transaction['assets'] = $assets;

        return $transaction;
    }

    public function get_transaction_out_by_id($transaction_id)
    {
        // Get transaction basic info
        $this->db->select('transaction.transaction_id, transaction.created_at, transaction.notes');
        $this->db->from('transaction');
        $this->db->where('transaction.transaction_id', $transaction_id);
        $transaction = $this->db->get()->row_array();

        if (!$transaction) {
            return null;
        }

        // Get assets in this transaction
        $this->db->select('asset.asset_name, asset.merk, asset.serial_number, transaction_details.status, source.name AS source_name, rooms.name AS destination_name');
        $this->db->from('transaction_details');
        $this->db->join('transaction', 'transaction.id = transaction_details.transaction_id');
        $this->db->join('asset', 'asset.id_asset = transaction_details.asset_id');
        $this->db->join('rooms AS source', 'source.id = transaction_details.source_id');
        $this->db->join('rooms', 'rooms.id = transaction_details.room_id');
        $this->db->where('transaction.transaction_id', $transaction_id);
        $assets = $this->db->get()->result_array();

        $transaction['assets'] = $assets;

        return $transaction;
    }

    /**
     * Riwayat perpindahan satu aset (terbaru dulu), dipakai halaman scan QR.
     * status detail: 0 = dikirim, 1 = diterima, 2 = ditolak.
     */
    public function get_asset_movements($asset_id)
    {
        $this->db->select('transaction.transaction_id AS trx_code, transaction.notes,
            transaction_details.status, transaction_details.sent, transaction_details.received,
            source.name AS source_name, rooms.name AS destination_name');
        $this->db->from('transaction_details');
        $this->db->join('transaction', 'transaction.id = transaction_details.transaction_id', 'left');
        $this->db->join('rooms AS source', 'source.id = transaction_details.source_id', 'left');
        $this->db->join('rooms', 'rooms.id = transaction_details.room_id', 'left');
        $this->db->where('transaction_details.asset_id', $asset_id);
        $this->db->order_by('transaction_details.id', 'DESC');
        return $this->db->get()->result_array();
    }
}

