<?php

if (!function_exists('generateCode')) {
    function generateCode($str)
    {
        $date = now()->format('YmdHis');
        $randomNumber = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return strtoupper($str) . $date . $randomNumber;
    }
}
?>