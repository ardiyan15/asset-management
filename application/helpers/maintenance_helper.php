<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Helper jadwal maintenance: satuan interval, hitung jatuh tempo berikutnya, dan status jatuh tempo.
 */

if (!function_exists('maintenance_interval_units')) {
    function maintenance_interval_units()
    {
        return ['day' => 'hari', 'week' => 'minggu', 'month' => 'bulan', 'year' => 'tahun'];
    }
}

if (!function_exists('maintenance_interval_label')) {
    /** (3, 'month') → "tiap 3 bulan" */
    function maintenance_interval_label($value, $unit)
    {
        $units = maintenance_interval_units();
        return 'tiap ' . (int) $value . ' ' . ($units[$unit] ?? $unit);
    }
}

if (!function_exists('maintenance_next_due')) {
    /**
     * Tanggal + interval. Untuk bulan/tahun, tanggal dijepit ke akhir bulan
     * (31 Jan + 1 bulan = 28/29 Feb, bukan 3 Mar).
     */
    function maintenance_next_due($from_date, $value, $unit)
    {
        $date  = new DateTime($from_date);
        $value = max(1, (int) $value);

        if ($unit === 'day' || $unit === 'week') {
            $days = $unit === 'week' ? $value * 7 : $value;
            return $date->modify('+' . $days . ' days')->format('Y-m-d');
        }

        $months = $unit === 'year' ? $value * 12 : $value;
        $day    = (int) $date->format('j');
        $target = (clone $date)->modify('first day of this month')->modify('+' . $months . ' months');
        $last   = (int) $target->format('t');
        return $target->setDate((int) $target->format('Y'), (int) $target->format('n'), min($day, $last))->format('Y-m-d');
    }
}

if (!function_exists('maintenance_due_status')) {
    /**
     * @return array ['key' => overdue|today|soon|upcoming|inactive, 'label' => .., 'color' => .., 'days' => int]
     */
    function maintenance_due_status($next_due_date, $remind_days_before, $is_active = 1, $today = null)
    {
        if (!(int) $is_active) {
            return ['key' => 'inactive', 'label' => 'Nonaktif', 'color' => 'secondary', 'days' => null];
        }

        $today = new DateTime($today ?: date('Y-m-d'));
        $due   = new DateTime($next_due_date);
        $diff  = (int) $today->diff($due)->format('%r%a');

        if ($diff < 0) {
            return ['key' => 'overdue', 'label' => 'Terlambat ' . abs($diff) . ' hari', 'color' => 'danger', 'days' => $diff];
        }
        if ($diff === 0) {
            return ['key' => 'today', 'label' => 'Jatuh tempo hari ini', 'color' => 'danger', 'days' => 0];
        }
        if ($diff <= (int) $remind_days_before) {
            return ['key' => 'soon', 'label' => $diff . ' hari lagi', 'color' => 'warning', 'days' => $diff];
        }
        return ['key' => 'upcoming', 'label' => $diff . ' hari lagi', 'color' => 'success', 'days' => $diff];
    }
}
