<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('loan_status')) {
    /**
     * Status peminjaman untuk badge.
     * @return array ['key' => returned|overdue|due_today|due_soon|active, 'label' => .., 'color' => .., 'days' => int|null]
     */
    function loan_status(array $loan, $today = null)
    {
        if ((int) $loan['status'] === 1) {
            $late = !empty($loan['returned_at']) && substr($loan['returned_at'], 0, 10) > $loan['due_date'];
            return [
                'key'   => 'returned',
                'label' => $late ? 'Dikembalikan (telat)' : 'Dikembalikan',
                'color' => $late ? 'secondary' : 'success',
                'days'  => null,
            ];
        }

        $today = new DateTime($today ?: date('Y-m-d'));
        $due   = new DateTime($loan['due_date']);
        $diff  = (int) $today->diff($due)->format('%r%a');

        if ($diff < 0) {
            return ['key' => 'overdue', 'label' => 'Terlambat ' . abs($diff) . ' hari', 'color' => 'danger', 'days' => $diff];
        }
        if ($diff === 0) {
            return ['key' => 'due_today', 'label' => 'Kembali hari ini', 'color' => 'warning', 'days' => 0];
        }
        if ($diff <= 2) {
            return ['key' => 'due_soon', 'label' => $diff . ' hari lagi', 'color' => 'warning', 'days' => $diff];
        }
        return ['key' => 'active', 'label' => $diff . ' hari lagi', 'color' => 'info', 'days' => $diff];
    }
}
