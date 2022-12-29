<?php 

if ( ! function_exists('isBrokenLink')) {
    function isBrokenLink(string $url): bool
    {
        $statusCode = getRemoteStatus($url);

        if (str_starts_with($statusCode, '4') || str_starts_with($statusCode, '5')) {
            return true;
        }

        return false;
    }
}

if ( ! function_exists('getRemoteStatus')) {
    function getRemoteStatus(string $url): int
    {
        $ch = curl_init($url);

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION
        ];

        curl_setopt_array($ch, $options);
        curl_exec($ch);

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return $statusCode;
    }
}

if ( ! function_exists('getRemoteFileSize')) {
    function getRemoteFileSize(string $url): int|false
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //not necessary unless the file redirects (like the PHP example we're using here)
        $data = curl_exec($ch);
        curl_close($ch);

        if ($data === false) {
            return 0;
        }

        if (preg_match('/Content-Length: (\d+)/', $data, $matches) || 
            preg_match('/content-length: (\d+)/', $data, $matches)
        ) {
            $contentLength = (int) $matches[1];
        }        

        return $contentLength ?? false;
    }
}

