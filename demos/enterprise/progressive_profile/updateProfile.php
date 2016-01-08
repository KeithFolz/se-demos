<?php

require_once("config.php");
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$uuid = htmlspecialchars($_POST["uuid"]);
$attribute = htmlspecialchars($_POST["attribute"]);
$value = htmlspecialchars($_POST["value"]);

$result = janrain_save_profile_data($uuid, $attribute, $value);

echo json_encode($result);

function janrain_save_profile_data($uuid, $attribute, $value) {
    
    $updateValue = json_encode(array( $attribute => $value ));
    
    $params = array(
            'client_id' => JANRAIN_LOGIN_CLIENT_ID,
            'client_secret' => JANRAIN_LOGIN_CLIENT_SECRET,
            'type_name' => 'user',
            'uuid' => $uuid,
            'value' => $updateValue
        );

    trigger_error(json_encode($params));

    $returnVal = janrain_api('/entity.update', $params);
    return $returnVal;
}

function janrain_api($call, $params) {
    // return JANRAIN_CAPTURE_API_URL.$call;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, JANRAIN_CAPTURE_API_URL.$call);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));

    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response, true);
}


