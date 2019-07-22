<?php

namespace App\functions;

if (!function_exists('recur_ksort')) {
    function recur_ksort(&$array)
    {
        foreach ($array as &$value) {
            if (is_array($value)) recur_ksort($value);
        }
        return ksort($array);
    }
}

?>