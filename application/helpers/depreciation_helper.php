<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Penyusutan metode garis lurus (straight-line), dihitung per bulan penuh.
 *
 *   nilai sisa          = harga perolehan × residual%
 *   penyusutan / bulan  = (harga perolehan − nilai sisa) / (masa manfaat × 12)
 *   akumulasi           = penyusutan / bulan × bulan berjalan   (maks. harga − nilai sisa)
 *   nilai buku          = harga perolehan − akumulasi
 *
 * Bulan berjalan dihitung dari tanggal perolehan sampai tanggal acuan (bulan penuh).
 */

if (!function_exists('asset_depreciation')) {
    /**
     * @param float|null  $price           harga perolehan
     * @param string|null $purchase_date   Y-m-d
     * @param int|null    $life_years      masa manfaat (tahun), null = belum diatur
     * @param float       $residual_percent
     * @param string|null $as_of           Y-m-d, default hari ini
     * @return array|null null kalau harga/tanggal perolehan belum diisi
     */
    function asset_depreciation($price, $purchase_date, $life_years, $residual_percent = 0, $as_of = null)
    {
        if ($price === null || $price === '' || empty($purchase_date)) {
            return null;
        }

        $price    = (float) $price;
        $residual = round($price * max(0, min(100, (float) $residual_percent)) / 100, 2);
        $start    = new DateTime($purchase_date);
        $end      = new DateTime($as_of ?: date('Y-m-d'));

        $months = 0;
        if ($end > $start) {
            $diff   = $start->diff($end);
            $months = $diff->y * 12 + $diff->m;
        }

        $life_months = $life_years ? (int) $life_years * 12 : 0;

        if ($life_months <= 0) {
            return [
                'configured'          => false,
                'price'               => $price,
                'residual'            => $residual,
                'age_months'          => $months,
                'life_months'         => 0,
                'monthly'             => 0.0,
                'yearly'              => 0.0,
                'accumulated'         => 0.0,
                'book_value'          => $price,
                'percent_depreciated' => 0,
                'fully_depreciated'   => false,
            ];
        }

        $depreciable = max(0, $price - $residual);
        $monthly     = $depreciable / $life_months;
        $accumulated = round(min($depreciable, $monthly * $months), 2);

        return [
            'configured'          => true,
            'price'               => $price,
            'residual'            => $residual,
            'age_months'          => $months,
            'life_months'         => $life_months,
            'monthly'             => round($monthly, 2),
            'yearly'              => round($monthly * 12, 2),
            'accumulated'         => $accumulated,
            'book_value'          => round($price - $accumulated, 2),
            'percent_depreciated' => $depreciable > 0 ? (int) round($accumulated / $depreciable * 100) : 100,
            'fully_depreciated'   => $months >= $life_months,
        ];
    }
}

if (!function_exists('rupiah')) {
    function rupiah($amount, $with_prefix = true)
    {
        if ($amount === null || $amount === '') {
            return '-';
        }
        return ($with_prefix ? 'Rp ' : '') . number_format((float) $amount, 0, ',', '.');
    }
}

if (!function_exists('asset_age_label')) {
    /** 27 → "2 th 3 bln" */
    function asset_age_label($months)
    {
        $months = (int) $months;
        $years  = intdiv($months, 12);
        $rest   = $months % 12;
        $parts  = [];
        if ($years > 0) {
            $parts[] = $years . ' th';
        }
        if ($rest > 0 || $years === 0) {
            $parts[] = $rest . ' bln';
        }
        return implode(' ', $parts);
    }
}

if (!function_exists('warranty_status')) {
    /**
     * @return array|null ['active' => bool, 'label' => string, 'color' => string]
     */
    function warranty_status($warranty_until, $as_of = null)
    {
        if (empty($warranty_until)) {
            return null;
        }
        $today = new DateTime($as_of ?: date('Y-m-d'));
        $until = new DateTime($warranty_until);
        $date  = $until->format('d M Y');

        if ($until < $today) {
            return ['active' => false, 'label' => 'Habis ' . $date, 'color' => 'secondary'];
        }
        $days = (int) $today->diff($until)->days;
        if ($days <= 30) {
            return ['active' => true, 'label' => 'Sampai ' . $date . ' (' . $days . ' hari lagi)', 'color' => 'warning'];
        }
        return ['active' => true, 'label' => 'Sampai ' . $date, 'color' => 'success'];
    }
}
