<?php

/**
 * Foto aset. File fisik ada di uploads/assets/<asset_id>/ (lihat Photo_model::dir()).
 */
class Photo_model extends CI_Model
{
    const MAX_PER_ASSET = 20;

    const SOURCES = [
        'manual' => 'Halaman aset',
        'scan'   => 'Scan QR',
        'opname' => 'Bukti stock opname',
    ];

    /** Folder fisik foto satu aset */
    public static function dir($asset_id)
    {
        return FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . (int) $asset_id;
    }

    /** URL publik satu file foto */
    public static function url($asset_id, $file_name)
    {
        return base_url('uploads/assets/' . (int) $asset_id . '/' . rawurlencode($file_name));
    }

    public function for_asset($asset_id)
    {
        $this->db->select('p.*, user.username AS uploaded_by_name, stock_opname.code AS opname_code');
        $this->db->from('asset_photos AS p');
        $this->db->join('user', 'user.id_user = p.uploaded_by', 'left');
        $this->db->join('stock_opname', 'stock_opname.id = p.stock_opname_id', 'left');
        $this->db->where('p.asset_id', (int) $asset_id);
        $this->db->order_by('p.is_primary', 'DESC');
        $this->db->order_by('p.id', 'DESC');
        return $this->db->get()->result_array();
    }

    public function count_for_asset($asset_id)
    {
        return $this->db->where('asset_id', (int) $asset_id)->count_all_results('asset_photos');
    }

    public function primary_for_asset($asset_id)
    {
        return $this->db->order_by('is_primary', 'DESC')->order_by('id', 'DESC')->limit(1)
            ->get_where('asset_photos', ['asset_id' => (int) $asset_id])->row_array();
    }

    /**
     * Foto utama banyak aset sekaligus (untuk daftar).
     * @return array [asset_id => photo row]
     */
    public function primaries(array $asset_ids)
    {
        $asset_ids = array_values(array_filter(array_map('intval', $asset_ids)));
        if (empty($asset_ids)) {
            return [];
        }
        $rows = $this->db->where_in('asset_id', $asset_ids)
            ->order_by('is_primary', 'DESC')->order_by('id', 'DESC')
            ->get('asset_photos')->result_array();
        $out = [];
        foreach ($rows as $row) {
            if (!isset($out[$row['asset_id']])) {
                $out[$row['asset_id']] = $row;
            }
        }
        return $out;
    }

    /** Foto bukti per aset dalam satu sesi opname. @return array [asset_id => jumlah] */
    public function count_for_opname($opname_id)
    {
        $rows = $this->db->select('asset_id, COUNT(*) AS total')->where('stock_opname_id', (int) $opname_id)
            ->group_by('asset_id')->get('asset_photos')->result_array();
        return array_column($rows, 'total', 'asset_id');
    }

    public function get($id)
    {
        return $this->db->get_where('asset_photos', ['id' => (int) $id])->row_array();
    }

    public function create(array $data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        // Foto pertama otomatis jadi foto utama
        $data['is_primary'] = $this->count_for_asset($data['asset_id']) === 0 ? 1 : 0;
        $this->db->insert('asset_photos', $data);
        return (int) $this->db->insert_id();
    }

    public function set_primary(array $photo)
    {
        $this->db->trans_start();
        $this->db->where('asset_id', $photo['asset_id'])->update('asset_photos', ['is_primary' => 0]);
        $this->db->where('id', $photo['id'])->update('asset_photos', ['is_primary' => 1]);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Hapus record + file. Kalau yang dihapus foto utama, foto terbaru berikutnya jadi utama.
     */
    public function delete(array $photo)
    {
        $this->db->delete('asset_photos', ['id' => $photo['id']]);
        $this->_unlink($photo);

        if ((int) $photo['is_primary'] === 1) {
            $next = $this->db->order_by('id', 'DESC')->limit(1)->get_where('asset_photos', ['asset_id' => $photo['asset_id']])->row_array();
            if ($next) {
                $this->db->where('id', $next['id'])->update('asset_photos', ['is_primary' => 1]);
            }
        }
        return true;
    }

    /** Dipanggil saat aset dihapus permanen: hapus semua foto & foldernya. */
    public function delete_all_for_asset($asset_id)
    {
        foreach ($this->db->get_where('asset_photos', ['asset_id' => (int) $asset_id])->result_array() as $photo) {
            $this->_unlink($photo);
        }
        $this->db->delete('asset_photos', ['asset_id' => (int) $asset_id]);

        $dir = self::dir($asset_id);
        @unlink($dir . DIRECTORY_SEPARATOR . 'index.html');
        @rmdir($dir);
    }

    private function _unlink(array $photo)
    {
        $dir = self::dir($photo['asset_id']);
        foreach ([$photo['file_name'], $photo['thumb_name']] as $name) {
            // Nama file dari DB tetap divalidasi supaya tidak bisa keluar dari folder aset
            if (preg_match('/^[a-f0-9]{24}(_t)?\.jpg$/', (string) $name)) {
                @unlink($dir . DIRECTORY_SEPARATOR . $name);
            }
        }
    }
}
