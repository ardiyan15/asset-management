<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('generate_transaction_id')) {

    function generate_transaction_id($module = 'AST') {

        $CI =& get_instance();
        $CI->load->database();

        $year = date('Y');
        $month = date('m');

        $CI->db->select('transaction_id');
        $CI->db->from('transaction');
        $CI->db->like('transaction_id', "TRX/{$module}/{$year}/{$month}", "after");
        $CI->db->order_by('transaction_id', "DESC");
        $CI->db->limit(1);

        $query = $CI->db->get();

        if($query->num_rows() > 0) {
            $lastCode = $query->row()->transaction_id;
            $lastNumber = (int) substr($lastCode, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $runningNumber = str_pad($newNumber, 6, '0', STR_PAD_LEFT);

        return "TRX/{$module}/{$year}/{$month}/{$runningNumber}";
    }
}