<?php

namespace App\functions;

if (!function_exists('recur_ksort')) {
    function recur_ksort(&$array, $sort_flags = null)
    {
        foreach ($array as &$value) {
            if (is_array($value)) recur_ksort($value);
        }
        return ksort($array, $sort_flags);
    }
}


?>