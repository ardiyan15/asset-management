<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Upload / hapus / pilih foto utama aset.
 * Dipakai dari halaman edit aset, halaman scan QR (kamera HP), dan stock opname (bukti kondisi, AJAX).
 * Non-admin hanya untuk aset di gedungnya; aset yang sudah dihapus (disposal) tidak bisa diubah fotonya.
 */
class Photo extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'Auth');
        $this->load->model('Photo_model', 'Photos');
        $this->load->model('Assets_model', 'Assets');
        $this->load->library('encryption');
        $this->load->helper('url_crypto');
        is_logged_in();
    }

    public function upload()
    {
        $user     = $this->Auth->get_active_user($this->session->userdata('username'));
        $asset_id = (int) $this->input->post('asset_id');
        $asset    = $this->_editable_asset($asset_id, $user);
        $caption  = trim((string) $this->input->post('caption', true));
        $source   = (string) $this->input->post('source');
        $source   = array_key_exists($source, Photo_model::SOURCES) ? $source : 'manual';

        if (!$asset) {
            return $this->_respond('failed', 'Aset tidak ditemukan, sudah dihapus, atau di luar gedung Anda.');
        }

        // Foto bukti opname harus terkait sesi opname yang masih berjalan dan berisi aset ini
        $opname_id = null;
        if ($source === 'opname') {
            $opname_id = $this->_opname_for_photo((string) $this->input->post('opname_token'), $asset_id, $user);
            if (!$opname_id) {
                return $this->_respond('failed', 'Sesi opname tidak ditemukan, sudah selesai, atau aset tidak ada di daftar opname.');
            }
        }

        $files = $this->_uploaded_files('photos');
        if (empty($files)) {
            return $this->_respond('failed', 'Pilih minimal satu foto.');
        }

        $slots = Photo_model::MAX_PER_ASSET - $this->Photos->count_for_asset($asset_id);
        if ($slots <= 0) {
            return $this->_respond('failed', 'Aset ini sudah punya ' . Photo_model::MAX_PER_ASSET . ' foto. Hapus foto lama dulu.');
        }

        $this->load->library('asset_image');
        $saved  = [];
        $errors = [];
        foreach (array_slice($files, 0, $slots) as $file) {
            $name = mb_substr(basename((string) $file['name']), 0, 150);
            if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
                $errors[] = $name . ': gagal diupload.';
                continue;
            }

            $result = $this->asset_image->process($file['tmp_name'], Photo_model::dir($asset_id));
            if (isset($result['error'])) {
                $errors[] = $name . ': ' . $result['error'];
                continue;
            }

            $id = $this->Photos->create(array_merge($result, [
                'asset_id'        => $asset_id,
                'original_name'   => $name,
                'caption'         => $caption !== '' ? mb_substr($caption, 0, 150) : null,
                'source'          => $source,
                'stock_opname_id' => $opname_id,
                'uploaded_by'     => $user['id_user'],
            ]));
            $saved[] = [
                'id'    => $id,
                'thumb' => Photo_model::url($asset_id, $result['thumb_name']),
                'url'   => Photo_model::url($asset_id, $result['file_name']),
            ];
        }
        if (count($files) > $slots) {
            $errors[] = (count($files) - $slots) . ' foto dilewati karena batas ' . Photo_model::MAX_PER_ASSET . ' foto per aset.';
        }

        if ($saved) {
            $this->audit->log('asset.photo_upload', 'Upload ' . count($saved) . ' foto ' . $asset['asset_name']
                . ($source === 'opname' ? ' (bukti stock opname)' : ''), [
                'entity_type'  => 'asset',
                'entity_id'    => $asset_id,
                'entity_label' => $asset['serial_number'],
                'building_id'  => $asset['building_id'],
                'changes'      => ['foto' => ['old' => null, 'new' => implode(', ', array_column($saved, 'id'))], 'keterangan' => ['old' => null, 'new' => $caption ?: null]],
            ]);
        }

        $status  = $saved ? ($errors ? 'partial' : 'ok') : 'failed';
        $message = $saved ? count($saved) . ' foto tersimpan.' : 'Tidak ada foto yang tersimpan.';
        return $this->_respond($status, $message, $errors, ['photos' => $saved, 'asset' => $asset['serial_number']]);
    }

    public function delete()
    {
        $user  = $this->Auth->get_active_user($this->session->userdata('username'));
        $photo = $this->Photos->get((int) $this->input->post('photo_id'));
        $asset = $photo ? $this->_editable_asset($photo['asset_id'], $user) : null;

        if (!$photo || !$asset) {
            return $this->_respond('failed', 'Foto tidak ditemukan atau Anda tidak punya akses.');
        }

        $this->Photos->delete($photo);
        $this->audit->log('asset.photo_delete', 'Hapus foto ' . $asset['asset_name'] . ($photo['caption'] ? ' (' . $photo['caption'] . ')' : ''), [
            'entity_type'  => 'asset',
            'entity_id'    => $asset['id_asset'],
            'entity_label' => $asset['serial_number'],
            'building_id'  => $asset['building_id'],
            'changes'      => ['foto' => ['old' => $photo['original_name'] ?: $photo['file_name'], 'new' => null]],
        ]);
        return $this->_respond('ok', 'Foto dihapus.');
    }

    public function set_primary()
    {
        $user  = $this->Auth->get_active_user($this->session->userdata('username'));
        $photo = $this->Photos->get((int) $this->input->post('photo_id'));
        $asset = $photo ? $this->_editable_asset($photo['asset_id'], $user) : null;

        if (!$photo || !$asset) {
            return $this->_respond('failed', 'Foto tidak ditemukan atau Anda tidak punya akses.');
        }

        $this->Photos->set_primary($photo);
        $this->audit->log('asset.photo_primary', 'Ganti foto utama ' . $asset['asset_name'], [
            'entity_type'  => 'asset',
            'entity_id'    => $asset['id_asset'],
            'entity_label' => $asset['serial_number'],
            'building_id'  => $asset['building_id'],
        ]);
        return $this->_respond('ok', 'Foto utama diganti.');
    }

    // ------------------------------------------------------------------

    private function _editable_asset($asset_id, $user)
    {
        $asset = $asset_id ? $this->Assets->get_asset_with_building((int) $asset_id) : null;
        $ok = $asset && (int) $asset['is_disposed'] === 0
            && ($user['role_id'] === '1' || (string) $user['building_id'] === (string) $asset['building_id']);
        return $ok ? $asset : null;
    }

    private function _opname_for_photo($token, $asset_id, $user)
    {
        $id = $token !== '' ? $this->encryption->decrypt(urlsafe_b64decode($token)) : false;
        if (!$id) {
            return null;
        }
        $this->load->model('Opname_model', 'Opname');
        $opname = $this->Opname->get_header((int) $id);
        if (!$opname || (int) $opname['status'] !== Opname_model::STATUS_OPEN) {
            return null;
        }
        if ($user['role_id'] !== '1' && (string) $user['building_id'] !== (string) $opname['building_id']) {
            return null;
        }
        $in_list = $this->db->where(['stock_opname_id' => $opname['id'], 'asset_id' => $asset_id])->count_all_results('stock_opname_details');
        return $in_list ? (int) $opname['id'] : null;
    }

    /** Normalisasi $_FILES['photos'] (multiple) jadi daftar file. */
    private function _uploaded_files($field)
    {
        if (empty($_FILES[$field]) || !isset($_FILES[$field]['name'])) {
            return [];
        }
        $f = $_FILES[$field];
        if (!is_array($f['name'])) {
            return $f['error'] === UPLOAD_ERR_NO_FILE ? [] : [$f];
        }
        $files = [];
        foreach (array_keys($f['name']) as $i) {
            if ($f['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $files[] = [
                'name'     => $f['name'][$i],
                'tmp_name' => $f['tmp_name'][$i],
                'error'    => $f['error'][$i],
                'size'     => $f['size'][$i],
            ];
        }
        return $files;
    }

    /**
     * AJAX → JSON. Form biasa → flashdata `photo_result` + redirect ke `back` (path internal saja).
     */
    private function _respond($status, $message, array $errors = [], array $extra = [])
    {
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(array_merge(['status' => $status, 'message' => $message, 'errors' => $errors], $extra)));
            return;
        }

        $this->session->set_flashdata('photo_result', ['status' => $status, 'message' => $message, 'errors' => $errors]);
        $back = (string) $this->input->post('back');
        redirect(preg_match('#^(asset/update/\d+|scan/[A-Za-z0-9%_\-]+)$#', $back) ? $back : 'asset');
    }
}
