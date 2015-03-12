<?php

require_once('config.php');

//-----------------

$path_to_api_key_file = "/Janrain/bpPassword.txt";

if (file_exists($path_to_api_key_file)) {}
else {
    echo '{
        "stat": "error",
        "error_description": "Sorry, could not find the BP PW File"
    }';
    //echo "<p>Sorry, could not find the api key file $path_to_api_key_file</p>";
    exit;
}

$bp_password = trim(file_get_contents($path_to_api_key_file));
$bp_user = "se-demo";


//------------

header('Content-Type: application/json');

session_start();

if (!empty($_POST['access_token']) && !empty($_POST['bp_channel_id'])) {

    //error_log("Got POST variables: {$_POST['access_token']} {$_POST['bp_channel_id']}");
    error_log("Test");

    // get entity
    $params = array(
        'access_token' => $_POST['access_token'],
        'type_name' => 'user'
    );

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, JANRAIN_CAPTURE_SERVER."/entity");
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));

    $entity_response = curl_exec($curl);
    curl_close($curl);
    $entity = json_decode($entity_response, true)['result'];

    // construct backplane payload
    $json_payload = "
    [
       {
           \"source\": \"http://localhost/\",
           \"type\": \"identity/login\",
           \"sticky\": true,
           \"payload\": {
               \"context\": \"http://localhost/\",
               \"identities\": {
                   \"startIndex\": 0,
                   \"itemsPerPage\": 1,
                   \"totalResults\": 1,
                   \"entry\": {
                       \"id\": \"https://".JANRAIN_CAPTURE_SERVER."/oauth/public_profile?uuid={$entity['uuid']}\",
                       \"displayName\": \"" . $entity['displayName'] . "\",
                       \"accounts\": [
                            {
                               \"username\": \"{$entity['displayName']}\",
                               \"identityUrl\": \"https://".JANRAIN_CAPTURE_SERVER."/oauth/public_profile?uuid={$entity['uuid']}\"
                            }";

    foreach ($entity['profiles'] as $profile) {
        $json_payload .= ",{\"identityUrl\": \"{$profile['identifier']}\"}";
    }

    $json_payload .= "
                       ]
                   }
               }
           }
       }
    ]";

    // send backplane identity message
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "$bp_user:$bp_password");
    curl_setopt($curl, CURLOPT_URL, $_POST['bp_channel_id']);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json_payload);

    $response = curl_exec($curl);
    curl_close($curl);

    echo $response;


  } else {
      echo '{
          "stat": "error",
          "error_description": "Missing required parameters."
      }';
  }

?>
