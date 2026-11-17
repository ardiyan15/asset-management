<?php

function is_logged_in() {
    $ci = get_instance();
    if (!$ci->session->userdata('username')) {
        redirect('auth');
    }
}

function is_allowed() {
    $ci = get_instance();
    $role_id = $ci->session->userdata('role_id');
    if($role_id !== "1"){
        // Percobaan akses fitur admin oleh non-admin ikut dicatat
        if ($ci->session->userdata('username') && isset($ci->audit)) {
            $ci->audit->log('auth.forbidden', 'Akses ditolak ke fitur admin: ' . $ci->uri->uri_string());
        }
        redirect('auth/blocked');
    }
}