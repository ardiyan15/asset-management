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
        redirect('auth/blocked');
    }
}