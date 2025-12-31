<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('is_active')) {
    /**
     * is_active('asset') -> aktif jika /asset
     * is_active('admin', 'list_user') -> aktif jika /admin/list_user
     * is_active('admin') -> aktif hanya jika /admin atau /admin/index (bukan /admin/*)
     */
    function is_active($segment1, $segment2 = null)
    {
        $CI =& get_instance();

        $s1 = $CI->uri->segment(1);
        $s2 = $CI->uri->segment(2);

        // segment1 harus match dulu
        if ($s1 !== $segment1) return '';

        // Kalau segment2 dikirim, wajib match juga
        if ($segment2 !== null) {
            return ($s2 === $segment2) ? 'active' : '';
        }

        // Kalau segment2 TIDAK dikirim:
        // Anggap ini menu "root" controller, jadi aktif hanya saat segment2 kosong / index
        return ($s2 === null || $s2 === '' || $s2 === 'index') ? 'active' : '';
    }
}
