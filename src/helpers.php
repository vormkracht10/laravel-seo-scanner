<?php

if (! function_exists('checkIfLinkIsBroken')) {
    function checkIfLinkIsBroken(string $url): bool
    {
        $ch = curl_init($url);

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION,
        ];

        curl_setopt_array($ch, $options);
        curl_exec($ch);

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if (str_starts_with($statusCode, '4') || str_starts_with($statusCode, '5')) {
            return true;
        }

        return false;
    }
}
