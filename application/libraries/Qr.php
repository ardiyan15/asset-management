<?php defined('BASEPATH') or exit('No direct script access allowed');

use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/**
 * Wrapper QR code (chillerlan/php-qrcode) untuk label aset.
 *
 * Output berupa PNG data URI supaya bisa langsung dipakai di <img> halaman web
 * maupun di view PDF Dompdf tanpa menulis file sementara.
 */
class Qr
{
    /**
     * URL yang di-encode ke dalam QR untuk satu aset.
     * Pakai config `qr_base_url` kalau di-set (mis. IP LAN supaya bisa di-scan dari HP),
     * kalau kosong fallback ke base_url().
     */
    public function asset_url($serial_number)
    {
        $ci   = get_instance();
        $base = $ci->config->item('qr_base_url') ?: base_url();

        return rtrim($base, '/') . '/scan/' . rawurlencode($serial_number);
    }

    /**
     * Render teks menjadi PNG data URI (data:image/png;base64,...).
     *
     * @param string $data  isi QR
     * @param int    $scale ukuran pixel per modul
     */
    public function png_data_uri($data, $scale = 6)
    {
        $options = new QROptions([
            'outputType'       => QROutputInterface::GDIMAGE_PNG,
            'eccLevel'         => EccLevel::M,
            'scale'            => (int) $scale,
            'quietzoneSize'    => 1,
            'imageTransparent' => false,
            'outputBase64'     => true,
        ]);

        return (new QRCode($options))->render((string) $data);
    }
}
