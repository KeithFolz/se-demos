<?php

require_once("config.php");

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function janrain_update_profile() {
    
}

/**
 *  Save information about the authenticated Janrain user in the PHP session.
 *
 *  @param string $tokens   an array of Janrain tokens as returned from calling
 *                          janrain_exchange_authorization_code()
 *  @param array  $profile  an array representing a Janrain user profile object
 */
function janrain_set_session_data($tokens, $profile) {
    // The Janrain UUID is the unique identifier of the user profile in Janrain
    $_SESSION['janrain_uuid'] = $profile['uuid'];

    // Saving the access token allows for additional API calls to be made to the
    // Janrain API. This access token is scoped for this user and is typically
    // used with calls to /entity and /entity.update.
    $_SESSION['janrain_access_token'] = $tokens['access_token'];

    // Saving the refresh token allows the access token to be refreshed if the
    // PHP session lasts longer than the access token which is stored in the
    // 'expires' variable (default 1 hour).
    $expires = strtotime("+{$tokens['expires_in']} seconds");
    $_SESSION['janrain_refresh_token'] = $tokens['refresh_token'];
    $_SESSION['janrain_token_expires'] = $expires;

    // Any data in the Janrain user profile that is needed by the application
    // can also be saved in the PHP session. In this case, saving the display
    // name will allow client-side code to present a personalized experience.
    $_SESSION['janrain_displayName'] = $profile['displayName'];
}

/**
 *  Get data about the Janrain user which can be exposed client-side. Secure
 *  information (such as the refresh token) should not be exposed client-side.
 *
 *  @return array associative array containing key => value pairs
 */
function janrain_get_client_side_session_data() {
    if (!empty($_SESSION['janrain_uuid'])) {
        return array(
            'uuid' => $_SESSION['janrain_uuid'],
            'token' => $_SESSION['janrain_access_token'],
            'displayName' => $_SESSION['janrain_displayName']
        );
    } else {
        return array(
            'uuid' => null,
            'token' => null,
            'displayName' => null
        );
    }
}

/**
 *  Retrieve an authenticated user's profile data using the access
 *
 *  @param string @token Janrain access token to use when retrieving profile data
 * 
 *  @return array associative array representation of the JSON response from
 *                the Janrain API
 */
function janrain_get_profile_data($token) {
    $params = array(
        'access_token' => $token,
        'type_name' => 'user'
    );

    return janrain_api('/entity', $params);
}

/**
 *  Save the user's profile data 
 *
 *  @param string $accessToken      user's access token
 *  @param string $email            user's email address
 *  @param string $firstName        user's first name
 *  @param string $lastName         user's last name
 *  @param string $displayName      user's display name
 *  @param string $gender           user's gender
 * 
 *  @return array associative array representation of the JSON response from
 *                the Janrain API
 */
function janrain_save_profile_data($attribute, $value) {
    $updateValue = '{"' . $attribute . '":"' . $value . '"}';
    $params = array(
            'client_id' => JANRAIN_LOGIN_CLIENT_ID,
            'client_secret' => JANRAIN_LOGIN_CLIENT_SECRET,
            'type_name' => 'user',
            'uuid' => $_SESSION['janrain_uuid'],
            'value' => $updateValue
        );

    trigger_error(json_encode($params));

    return janrain_api('/entity.update', $params);
}

function janrain_api($call, $params) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, JANRAIN_CAPTURE_API_URL.$call);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));

    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response, true);
}


