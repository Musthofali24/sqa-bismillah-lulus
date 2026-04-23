<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$apiBaseUrl = getenv('API_BASE_URL') ?: 'http://localhost:8000';
define('API_BASE_URL', $apiBaseUrl);

function callAPI($method, $url, $data = false)
{
    $curl = curl_init();
    $full_url = API_BASE_URL . $url;

    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];

    if (isset($_SESSION['access_token'])) {
        $headers[] = 'Authorization: Bearer ' . $_SESSION['access_token'];
    }

    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case "DELETE":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            break;
        default:
            if ($data) $full_url = sprintf("%s?%s", $full_url, http_build_query($data));
    }

    curl_setopt($curl, CURLOPT_URL, $full_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return ['code' => $httpcode, 'response' => json_decode($result, true)];
}

function isLoggedIn()
{
    return isset($_SESSION['access_token']);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}
