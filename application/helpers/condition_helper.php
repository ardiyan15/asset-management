<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Kondisi fisik aset (kolom asset.asset_condition).
 * 1 = Baik, 2 = Rusak Ringan, 3 = Rusak Berat
 */

if (!function_exists('asset_condition_options')) {
    function asset_condition_options()
    {
        return [
            1 => 'Baik',
            2 => 'Rusak Ringan',
            3 => 'Rusak Berat',
        ];
    }
}

if (!function_exists('is_valid_asset_condition')) {
    function is_valid_asset_condition($condition)
    {
        return array_key_exists((int) $condition, asset_condition_options());
    }
}

if (!function_exists('asset_condition_label')) {
    function asset_condition_label($condition)
    {
        $options = asset_condition_options();
        return $options[(int) $condition] ?? '-';
    }
}

if (!function_exists('asset_condition_color')) {
    /** Warna Bootstrap (success / warning / danger) untuk badge. */
    function asset_condition_color($condition)
    {
        $colors = [1 => 'success', 2 => 'warning', 3 => 'danger'];
        return $colors[(int) $condition] ?? 'secondary';
    }
}

if (!function_exists('asset_condition_badge')) {
    function asset_condition_badge($condition)
    {
        return '<span class="badge badge-' . asset_condition_color($condition) . '">'
            . html_escape(asset_condition_label($condition)) . '</span>';
    }
}
