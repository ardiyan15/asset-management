<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Pemroses foto aset.
 *
 * Setiap file diperiksa isinya (bukan dari ekstensi/MIME kiriman browser), lalu di-decode
 * dan di-encode ulang ke JPEG dengan GD. Hasilnya:
 *  - file yang bukan gambar asli (mis. skrip yang diberi ekstensi .jpg) tertolak
 *  - metadata EXIF (termasuk lokasi GPS dari HP) terbuang
 *  - orientasi foto HP dibetulkan, ukuran diperkecil, plus thumbnail
 */
class Asset_image
{
    const MAX_BYTES     = 10485760;   // 10 MB per file
    const MAX_PIXELS    = 40000000;   // 40 MP, mencegah "image bomb"
    const MAX_DIMENSION = 1600;
    const THUMB_SIZE    = 400;
    const QUALITY       = 82;

    const ALLOWED_TYPES = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP];

    /**
     * @param string $tmp_path  file sementara hasil upload
     * @param string $dest_dir  folder tujuan (dibuat kalau belum ada)
     * @return array ['file_name', 'thumb_name', 'width', 'height', 'size_bytes'] atau ['error' => pesan]
     */
    public function process($tmp_path, $dest_dir)
    {
        if (!is_file($tmp_path) || filesize($tmp_path) === 0) {
            return ['error' => 'File kosong atau gagal diupload.'];
        }
        if (filesize($tmp_path) > self::MAX_BYTES) {
            return ['error' => 'Ukuran file melebihi 10 MB.'];
        }

        $info = @getimagesize($tmp_path);
        if (!$info || !in_array($info[2], self::ALLOWED_TYPES, true)) {
            return ['error' => 'File bukan gambar JPG, PNG, atau WEBP.'];
        }
        if ($info[0] * $info[1] > self::MAX_PIXELS) {
            return ['error' => 'Resolusi gambar terlalu besar (maks. 40 MP).'];
        }

        $old_limit = ini_get('memory_limit');
        @ini_set('memory_limit', '512M');

        try {
            $src = $this->load($tmp_path, $info[2]);
            if (!$src) {
                return ['error' => 'Gambar rusak atau tidak bisa dibaca.'];
            }

            if ($info[2] === IMAGETYPE_JPEG) {
                $src = $this->fix_orientation($src, $tmp_path);
            }

            if (!is_dir($dest_dir) && !@mkdir($dest_dir, 0755, true)) {
                imagedestroy($src);
                return ['error' => 'Folder penyimpanan foto tidak bisa dibuat.'];
            }
            $this->protect_dir($dest_dir);

            $base  = bin2hex(random_bytes(12));
            $file  = $base . '.jpg';
            $thumb = $base . '_t.jpg';

            $main = $this->resize($src, self::MAX_DIMENSION);
            $ok   = imagejpeg($main, $dest_dir . DIRECTORY_SEPARATOR . $file, self::QUALITY);
            $w    = imagesx($main);
            $h    = imagesy($main);

            $small = $this->resize($src, self::THUMB_SIZE);
            $ok    = $ok && imagejpeg($small, $dest_dir . DIRECTORY_SEPARATOR . $thumb, 78);

            imagedestroy($src);
            if ($main !== $src) {
                imagedestroy($main);
            }
            if ($small !== $src) {
                imagedestroy($small);
            }

            if (!$ok) {
                @unlink($dest_dir . DIRECTORY_SEPARATOR . $file);
                @unlink($dest_dir . DIRECTORY_SEPARATOR . $thumb);
                return ['error' => 'Gagal menyimpan foto.'];
            }

            return [
                'file_name'  => $file,
                'thumb_name' => $thumb,
                'width'      => $w,
                'height'     => $h,
                'size_bytes' => filesize($dest_dir . DIRECTORY_SEPARATOR . $file),
            ];
        } finally {
            @ini_set('memory_limit', $old_limit);
        }
    }

    private function load($path, $type)
    {
        switch ($type) {
            case IMAGETYPE_JPEG:
                return @imagecreatefromjpeg($path);
            case IMAGETYPE_PNG:
                $img = @imagecreatefrompng($path);
                return $img ? $this->flatten($img) : false;
            case IMAGETYPE_WEBP:
                $img = @imagecreatefromwebp($path);
                return $img ? $this->flatten($img) : false;
        }
        return false;
    }

    /** Gambar transparan diberi latar putih (JPEG tidak punya alpha). */
    private function flatten($img)
    {
        $w   = imagesx($img);
        $h   = imagesy($img);
        $out = imagecreatetruecolor($w, $h);
        imagefill($out, 0, 0, imagecolorallocate($out, 255, 255, 255));
        imagecopy($out, $img, 0, 0, 0, 0, $w, $h);
        imagedestroy($img);
        return $out;
    }

    /** Putar sesuai tag EXIF Orientation (foto HP sering tersimpan miring). */
    private function fix_orientation($img, $path)
    {
        if (!function_exists('exif_read_data')) {
            return $img;
        }
        $exif = @exif_read_data($path);
        $orientation = (int) ($exif['Orientation'] ?? 1);

        $rotations = [3 => 180, 6 => -90, 8 => 90];
        if (isset($rotations[$orientation])) {
            $rotated = imagerotate($img, $rotations[$orientation], 0);
            if ($rotated) {
                imagedestroy($img);
                return $rotated;
            }
        }
        return $img;
    }

    /** Perkecil supaya sisi terpanjang <= $max (tidak diperbesar). */
    private function resize($img, $max)
    {
        $w = imagesx($img);
        $h = imagesy($img);
        $scale = min(1, $max / max($w, $h));
        if ($scale >= 1) {
            return $img;
        }
        $nw  = max(1, (int) round($w * $scale));
        $nh  = max(1, (int) round($h * $scale));
        $out = imagecreatetruecolor($nw, $nh);
        imagecopyresampled($out, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
        return $out;
    }

    private function protect_dir($dir)
    {
        if (!is_file($dir . DIRECTORY_SEPARATOR . 'index.html')) {
            @file_put_contents($dir . DIRECTORY_SEPARATOR . 'index.html', '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body></body></html>');
        }
    }
}
