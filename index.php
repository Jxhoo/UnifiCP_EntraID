<?php
session_start();

// Load Unifi API
require_once 'Client.php';
// Load configuration
require_once 'config.php';

use UniFi_API\Client;

$controller_url = UNIFI_URL;
$controller_user = UNIFI_USER;
$controller_pass = UNIFI_PASSWORD;
$controller_site = UNIFI_SITE;

// If Authorization code not in request, initiate Entra ID authentication flow
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['code'])) {

    $id = $_GET['id'];
    $ap_mac = $_GET['ap'];
    $ssid = $_GET['ssid'];
    $validity = VALIDITY;

    $_SESSION['id'] = $id;
    $_SESSION['ap_mac'] = $ap_mac;
    $_SESSION['ssid'] = $ssid;
    $_SESSION['validity'] = $validity;

    $state = bin2hex(random_bytes(16));

    $params = [
        'client_id' => CLIENT_ID,
        'response_type' => 'code',
        'redirect_uri' => REDIRECT_URI,
        'response_mode' => 'query',
        'scope' => 'openid profile User.Read',
        'state' => $state,
    ];

    $auth_url = AUTHORIZATION_URL . '?' . http_build_query($params);
    // Initialize authencation flow
    header("Location: $auth_url");
    exit();
}
// If Authorization code in request, initiate validation of login
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code'])) {
    // Handle callback from Azure AD
    $code = $_GET['code'];

    // Exchange code for token
    $token_response = getAccessToken($code);
    if (isset($token_response['access_token'])) {
        $access_token = $token_response['access_token'];

        // Fetch user information from Azure AD
        $user_info = getUserInfo($access_token);

        // Now that the user is authenticated, authorize them in UniFi
        $id = $_SESSION['id'];
        $ap_mac = $_SESSION['ap_mac'];
        $ssid = $_SESSION['ssid'];
        $validity = $_SESSION['validity'];

        $unifi = new Client($controller_user, $controller_pass, $controller_url, $controller_site);
        $login = $unifi->login();
        if ($login) {
            // Authorize client
            $authorize = $unifi->authorize_guest($id, $validity); 
            if ($authorize) {
                echo 'You are successfully authorized to use the network!';
            } else {
                echo 'Failed to authorize with UniFi controller.';
            }
        } else {
            echo 'Failed to login to UniFi controller.';
        }
    } else {
        echo 'Failed to obtain access token from Azure AD.';
    }
}

function getAccessToken($code)
{
    $params = [
        'client_id' => CLIENT_ID,
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => REDIRECT_URI,
        'client_secret' => CLIENT_SECRET,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, TOKEN_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

function getUserInfo($access_token)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, GRAPH_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $access_token"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}
?>