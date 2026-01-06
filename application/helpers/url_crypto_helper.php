<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('urlsafe_b64encode')) {
    function urlsafe_b64encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

if (!function_exists('urlsafe_b64decode')) {
    function urlsafe_b64decode($data)
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
