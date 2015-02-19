<?php

require_once('config.php');

//-----------------

$path_to_api_key_file = "/Janrain/apiKeyCapture.txt";

if (file_exists($path_to_api_key_file)) {}
else {
    echo '{
        "stat": "error",
        "error_description": "Sorry, could not find the api key file $path_to_api_key_file"
    }';
    //echo "<p>Sorry, could not find the api key file $path_to_api_key_file</p>";
    exit;
}

$api_key = trim(file_get_contents($path_to_api_key_file));

//------------

header('Content-Type: application/json');

session_start();

if (!empty($_SESSION['uuid'])) {

    // An API client with "access_issuer" credentials must be used to obtain a
    // new access token scoped for the user specified by 'uuid' and based on the
    // permissions and access schema for the 'for_client_id' parameter.
    $params = array(
        'client_id' => JANRAIN_ISSUER_CLIENT_ID,
        'client_secret' => $api_key,
        'for_client_id' => JANRAIN_LOGIN_CLIENT_ID,
        'type_name' => 'user',
        'uuid' => $_SESSION['uuid']
    );

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, JANRAIN_CAPTURE_SERVER."/access/getAccessToken");
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));

    $response = curl_exec($curl);
    curl_close($curl);

    echo $response;

  } else {
      echo '{
          "stat": "error",
          "error_description": "User is not authenticated. Cannot get a new token."
      }';
  }

?>
