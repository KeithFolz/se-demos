<?php

require_once('config.php');

header('Content-Type: application/json');

session_start();

if (!empty($_POST['access_token'])) {

    // The access token from the login client is scoped to a particular UUID.
    // Use the /entity API call to obatain that UUID so that it can be stored
    // in the user's session and used to get a new access token later.

    $params = array(
        'access_token' => $_POST['access_token'],
        'type_name' => 'user',
        'attributes' => '["uuid"]'
    );

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, JANRAIN_CAPTURE_SERVER."/entity");
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));

    $response = curl_exec($curl);
    curl_close($curl);

    $_SESSION['access_token'] = $_POST['access_token'];
    $_SESSION['uuid'] = json_decode($response, true)['result']['uuid'];

    $uuid = $_SESSION['uuid'];

    echo $response;


} else {
    echo '{
        "stat": "error",
        "error_description": "Missing required parameter: access_token."
    }';
}

?>
