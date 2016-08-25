<?php

?><!DOCTYPE html>
<html>
<head>
    <script>
        // *** GLOBAL VARIABLES DEFINITION *** //        
        janrainClientId = ''; // Your client id here
        janrainClientSecret = ''; // Your client secret here
        janrainCallBackUrl = 'http://' + <your domain> + '/demos/eval-instances/progressive-profiling-demo/janrain-callback.php'; // replace with your url
        janrainCaptureServerDomain = 'xxx.janraincapture.com'; // your server domain
        janrainAppUrl = 'https://xxx.rpxnow.com'; // your app url
        JanrainAppId = ''; // your app id

        janrainHttpLoadUrl  = "http://widget-cdn.rpxnow.com/load/partners2-demo-eu";
        janrainHttpsLoadUrl = "https://rpxnow.com/load/partners2-demo-eu";

        /* Define the time period during which the progressive profiling is enabled */ 
        startDate = new Date("2016/01/03");
        endDate = new Date("2017/03/23");

        /* Name of the "progressive" data field. It MUST already exist within the 'extendedProgressiveProfile' object */
        fieldName = 'totalCars';
        
        userId = '';
        displayMode = 'embedded'; // embedded/modal/basic
        debugMode = 'console'; // alert,console,off
        eventTested = 0;
    </script>
    <script src="scripts/janrain-ajax-base.js"></script>
    <script type="text/javascript">


        // *** HANDLE THE CAPTURE OF EVENT DATA *** //
        function checkEventDataCapture() {
            todayDate = new Date();
            
            // STEP 1: Are we in the right period for the event?
            if ((todayDate >= startDate) && (todayDate <= endDate)) {
                logDebugEvent("Event is ON");
                // STEP 2: Is the user registered and logged in?
                userId = getUuid();
                if (userId == '') {
                    logDebugEvent("User IS NOT logged in");
                } else {
                    logDebugEvent("User IS logged in - " + userId);
                    // STEP 3: Handle event data capture for user
                    handleEventDataCapture(userId);
                    eventTested = 1;
                }
            } else {
                logDebugEvent("Event is OFF");
            }
        }
        
        // *** Return the user Janrain UUID *** //
        function getUuid() {
            uuid = janrain.capture.ui.getProfileCookieData("uuid");
            if ( (uuid == '') || (uuid == undefined)) {
                return '';
            } else {
                return uuid;
            }
            
        }
        // *** RETURN the user profile *** //
        function handleEventDataCapture(curId) {
            var ajaxParams = new Array();
            ajaxParams['mode'] = 'event-data-capture';
            ajaxParams['type'] = 'POST';
            ajaxParams['url'] = janrainCallBackUrl;
            ajaxParams['data'] = 'apiUrl=https://'+janrainCaptureServerDomain+'/entity&type_name=user&uuid='+curId+'&client_id='+janrainClientId+'&client_secret='+janrainClientSecret;
            ajaxCall(ajaxParams);
        }

        function handleUserData(myVal) {
            mode = 'add-user-data';

                // alert("Handling user data post!");
                var ajaxParams = new Array();
                ajaxParams['mode'] = mode;
                ajaxParams['type'] = 'POST';
                ajaxParams['url'] = janrainCallBackUrl;
                ajaxParams['data'] = '';
                switch(mode) {
                    case 'add-user-data':
                        console.log('[DEBUG] Adding ' + myVal + ' to user profile for '+fieldName+'...');
                        ajaxParams['data'] = 'apiUrl=https://'+janrainCaptureServerDomain+'/entity.update&type_name=user&uuid='+userId+'&attribute_name=extendedProgressiveProfile&value={"'+fieldName+'":"'+myVal+'"}&client_id='+janrainClientId+'&client_secret='+janrainClientSecret;
                        break;
                }
                // console.log(ajaxParams);
                if (ajaxParams['data'] != '') {
                    ajaxCall(ajaxParams);
                } else {
                    console.log('[DEBUG] Ajax call ignored in mode ' + mode);
                }
        }


        function logDebugEvent(myEvent) {
            switch(debugMode) {
                case 'console':
                    console.log('DEBUG: ' + myEvent);
                    break;
                case 'alert':
                    alert('DEBUG: ' + myEvent);
                    break;
                default:
                    break;
            }            
        }
    </script>
    <script src="scripts/janrain-init.js"></script>
    <script src="scripts/janrain-utils.js"></script>
</head>
<body>

    <!--
    ============================================================================
        A NOTE ON NAVIGATION:
        These links are not needed for integration into your site. These links
        are only used for navigating the reference implementation that exists on
        our servers for testing/demo/sample purposes. It is not required for
        your implementation.

        Here you can see an example of using janrain.capture.ui.renderScreen in
        conjunction with HTML elements to simplify user navigation.
    ============================================================================
    -->
    <h1>Janrain - progressive profiling demo</h1>
    <pre>
        BEHAVIOUR: 

            Upon successful login, the user will be presented with a "popup question". Answering this question will enrich the user 
            profile with the selected value.

        CONDITIONS:

            This "popup question" will appear IF:
                1. Today's date falls within the defined startDate/endDate period (see Javascript global variables in the page header)
                2. The user is logged in and has NOT already answered the question (aka value for the field is null)

        FILES LIBRARY CONTENT:

        /index.php                  The root file (based on the default index.html)
        /janrain-callback.php       The basi PHP callback script
        /Progressive-Profiling-Demo.postman_collection The Postman collection
        /scripts                    The Javascript libraries
            /janrain-ajax-base.js   A simple Ajax requests handler
            /janrain-init.js        A modified version of the default library: I've removed the various harcoded 
                                    declarations (i.e. appId, key/secret etc...) and moved them in the index.php global 
                                    variables declarations instead.
        /css/*                      The default css library

        SETUP GUIDE:

            1. Deploy the files library in a localhost folder
            2. Run the provided "progressive profiling demo" postman collection.
            3. Configure the Javascript global variables in the header of index.php
            4. OPTIONAL: configure the HTML preceded by "PPD" HTML comments within the body of index.php

        TIPS/COMMENTS:

            - Enable the console to see what's happening along the way
            - You can easily change the "popup question" trigger, based on any Janrain Javascript event
            - In this demo I've added the custom field (totalCars) at the bottom of the Edit Profile 
              screen (so you can track the behaviour). You can use the "Reset to null" value to revert back 
              to null (meaning the popup question will reappear if you reload the page)

    </pre>
    <div style='font-family: verdana; font-size: 0.8em;'>
        <a href="#" id="captureSignInLink" onclick="janrain.capture.ui.renderScreen('signIn')">Sign In/Register</a>
        <a href="#" id="captureProfileLink" style="display:none" onclick="janrain.capture.ui.renderScreen('editProfile')" >Edit Profile</a>
        <a href="#" id="captureSignOutLink" style="display:none" onclick="janrain.capture.ui.endCaptureSession()" >Sign Out</a>
    </div>

    <div>
        <!-- PPD: This is the progressive profiling custom form. -->
        <div id='progressiveProfilingPopUp' style="border-bottom-left-radius:50%; border-bottom-right-radius:50%; background-color: orange; font-family: verdana; font-size: 1.1em; text-align: center; display: none;">
            <form>
                <p><strong><br />Quick question: how many cars do you own?</strong></p>
                <select name='progressiveField' id='progressiveField' style='font-size: 1.2em;'>
                    <option value=''>- Choose here -</option>
                    <option value='1'>Just one</option>
                    <option value='2'>Two</option>
                    <option value='3'>Actually 3</option>
                    <option value='999'>Prefer not to say</option>
                </select>
                <input type='button' name='submitData' value='Submit' style='font-size: 1.2em;'
                       onclick='handleUserData(document.getElementById("progressiveField").value)'/>
            </form>
        </div>
        
    </div>

    <!--
    ============================================================================
        SIGNIN SCREENS:
        The following screens are part of the sign in user workflow. For a
        complete out-of-the-box sign in experience, these screens must be
        included on the page where you are implementing sign in and registration.
    ============================================================================
    -->

    <!-- signIn:
    This is the starting point for sign in and registration. This screen is
    rendered by default. In order to change this behavior, the Flow must be
    edited.
    -->
    <div style="display:none;" id="signIn">
        <div class="capture_header">
            <h1>Sign Up / Sign In</h1>
        </div>
        <div class="capture_signin">
            <h2>With your existing account from...</h2>
            {* loginWidget *} <br />
        </div>
        <div class="capture_backgroundColor">
            <div class="capture_signin">
                <h2>With a traditional account...</h2>
                {* #signInForm *}
                    {* signInEmailAddress *}
                    {* currentPassword *}
                    <div class="capture_form_item">
                        <a href="#" data-capturescreen="forgotPassword">Forgot your password?</a>
                    </div>
                    <div class="capture_rightText">
                        <button class="capture_secondary capture_btn capture_primary" type="submit"><span class="janrain-icon-16 janrain-icon-key"></span> Sign In</button>
                        <a href="#" id="capture_signIn_createAccountButton" data-capturescreen="traditionalRegistration" class="capture_secondary capture_createAccountButton capture_btn capture_primary">Create Account</a>
                    </div>
                {* /signInForm *}
            </div>
        </div>
    </div>

    <!-- returnSocial:
    This is the screen the user sees in place of the signIn screen if they've
    already signed in with a social account on this site. Rendering of this
    screen is defined in the Flow only when the 'janrainLastAuthMethod' cookie
    is set to'socialSignin'.
    -->
    <div style="display:none; background-color:#fff;" id="returnSocial">
        <div class="capture_header">
            <h1>Sign In</h1>
        </div>
        <div class="capture_signin">
            <h2>Welcome back, {* welcomeName *}!</h2>
            {* loginWidget *}
            <div class="capture_centerText switchLink"><a href="#" data-cancelcapturereturnexperience="true">Use another account</a></div>
        </div>
    </div>

    <!-- returnTraditional:
    This is the screen the user sees in place of the signIn screen if they've
    already signed in with a traditional account on this site. Rendering of this
    screen is defined in the Flow only when the 'janrainLastAuthMethod' cookie
    is set to'traditionalSignin'.
    -->
    <div style="display:none; background-color:#fff;" id="returnTraditional">
        <div class="capture_header">
            <h1>Sign In</h1>
        </div>
        <h2 class="capture_centerText"><span id="traditionalWelcomeName">Welcome back!</span></h2>
        <div class="capture_backgroundColor">
            {* #signInForm *}
                {* signInEmailAddress *}
                {* currentPassword *}
                <div class="capture_form_item capture_rightText">
                    <button class="capture_secondary capture_btn capture_primary" type="submit"><span class="janrain-icon-16 janrain-icon-key"></span> Sign In</button>
                </div>
            {* /signInForm *}
            <div class="capture_centerText switchLink"><a href="#" data-cancelcapturereturnexperience="true">Use another account</a></div>
        </div>
    </div>

    <!-- accountDeactivated:
        This screen is rendered if the user's account is deactivated. Screen
        rendering is handled in janrain-init.js.
    -->
    <div style="display:none; background-color:#fff;" id="accountDeactivated">
        <div class="capture_header">
            <h1>Deactivated Account </h1>
        </div>
        <div class="content_wrapper">
            <p>Your account has been deactivated.</p>
        </div>
    </div>



    <!--
    ============================================================================
        REGISTRATION SCREENS:
        The following screens are part of the registration user workflow. For a
        complete out-of-the-box registration experience, these screens must be
        included on the page where you are implementing sign in and
        registration.
    ============================================================================
    -->

    <!-- socialRegistration:
        When a user clicks an IDP and does not already have an account in your
        capture application, this screen is rendered. This behavior is defined
        in the Flow.
    -->
    <div style="display:none; background-color:#fff;" id="socialRegistration">
        <div class="capture_header">
        <h1>Register with us</h1>
        </div>
        <h2>Please confirm the information below before signing in.</h2>
        {* #socialRegistrationForm *}
            {* firstName *}
            {* lastName *}
            {* emailAddress *}
            {* displayName *}
            <!-- PPD: Add your progressive field here ONLY IF you want it to be part of the social registration form -->

            By clicking "Sign in", you confirm that you accept our <a href="#">terms of service</a> and have read and understand <a href="#">privacy policy</a>.
            <div class="capture_footer">
                <div class="capture_left">
                    {* backButton *}
                </div>
                <div class="capture_right">
                    <input value="Create Account" type="submit" class="capture_btn capture_primary">
                </div>
            </div>
        {* /socialRegistrationForm *}
    </div>

    <!-- traditionalRegistration:
        When a user clicks the 'Create Account' button this screen is rendered.
    -->
    <div style="display:none; background-color:#fff;" id="traditionalRegistration">
        <div class="capture_header">
        <h1>Register with us</h1>
        </div>
        <p>Please confirm the information below before signing in. Already have an account? <a id="capture_traditionalRegistration_navSignIn" href="#" data-capturescreen="signIn">Sign In.</a></p>
        {* #registrationForm *}
            {* firstName *}
            {* lastName *}
            {* emailAddress *}
            {* displayName *}
            {* newPassword *}
            {* newPasswordConfirm *}
            <!-- PPD: Add your progressive field here ONLY IF you want it to be part of the traditional registration form -->
            By clicking "Create Account", you confirm that you accept our <a href="#">terms of service</a> and have read and understand <a href="#">privacy policy</a>.
            <div class="capture_footer">
                <div class="capture_left">
                    {* backButton *}
                </div>
                <div class="capture_right">
                    <input value="Create Account" type="submit" class="capture_btn capture_primary">
                </div>
            </div>
        {* /registrationForm *}
    </div>

    <!-- emailVerificationNotification:
        This screen is rendered after a user has registered. In the case of
        traditional registration, this screen is always rendered after the user
        completes registration on the traditionalRegistration screen. In the
        case of social registration, this screen is only rendered if the data
        returned from the IDP does not contain a verified email address.
        Twitter is an example of an IDP that does not return a verified email.
    -->
    <div style="display:none; background-color:#fff;" id="emailVerificationNotification">
        <div class="capture_header">
            <h1>Thank you for registering!</h1>
        </div>
        <p>We have sent a confirmation email to {* emailAddressData *}. Please check your email and click on the link to activate your account.</p>
        <div class="capture_footer">
            <a href="#" onclick="janrain.capture.ui.modal.close()" class="capture_btn capture_primary">Close</a>
        </div>
    </div>




    <!--
    ============================================================================
        FORGOT PASSWORD SCREENS:
        The following screens are part of the forgot password user workflow. For
        a complete out-of-the-box registration experience, these screens must be
        included on the page where you are implementing forgot password
        functionality.
    ============================================================================
    -->

    <!-- forgotPassword:
        Entry point into the forgot password user workflow. This screen is
        rendered when the user clicks on the 'Forgot your password?' link on the
        signIn screen.
    -->
    <div style="display:none; background-color:#fff;" id="forgotPassword">
        <div class="capture_header">
            <h1>Create a new password</h1>
        </div>
        <h2>We'll send you a link to create a new password.</h2>
        {* #forgotPasswordForm *}
            {* signInEmailAddress *}
            <div class="capture_footer">
                <div class="capture_left">
                    {* backButton *}
                </div>
                <div class="capture_right">
                    <input value="Send" type="submit" class="capture_btn capture_primary">
                </div>
            </div>
        {* /forgotPasswordForm *}
    </div>

    <!-- forgotPasswordSuccess:
        When the user submits an email address on the forgotPassword screen,
        this screen is rendered.
    -->
    <div style="display:none; background-color:#fff;" id="forgotPasswordSuccess">
        <div class="capture_header">
            <h1>Create a new password</h1>
        </div>
            <p>We've sent an email with instructions to create a new password. Your existing password has not been changed.</p>
        <div class="capture_footer">
            <a href="#" onclick="janrain.capture.ui.modal.close()" class="capture_btn capture_primary">Close</a>
        </div>
    </div>




    <!--
    ============================================================================
        MERGE ACCOUNT SCREENS:
        The following screens are part of the account merging user workflow. For
        a complete out-of-the-box account merging experience, these screens must
        be included on the page where you are implementing account merging
        functionality.
    ============================================================================
    -->

    <!-- mergeAccounts:
        This screen is rendered if the user created their account through
        traditional registration and then tries to sign in with an IDP that
        shares the same email address that exists in their user record.

        NOTE! You will notice special tags you see on this screen. These tags,
        such as '{| current_displayName |}' are rendered by the Janrain Capture
        Widget in a way similar to JTL tags, but are more limited. We currently
        only support modifying the text in this screen through the Flow. You
        can, however, add your own markup and text throughout this screen as you
        see fit.
    -->
    <div style="display:none; background-color:#fff;" id="mergeAccounts">
        {* mergeAccounts {"custom": true} *}
        <div id="capture_mergeAccounts_mergeAccounts_mergeOptionsContainer" class="capture_mergeAccounts_mergeOptionsContainer">
            <div class="capture_header">
                <div class="capture_icon_col">
                    {| rendered_current_photo |}
                </div>
                <div class="capture_displayName_col">
                    {| current_displayName |}<br />
                    {| current_emailAddress |}
                </div>
                <span class="capture_mergeProvider janrain-provider-icon-24 janrain-provider-icon-{| current_provider_lowerCase |}"></span>
            </div>
            <div class="capture_dashed">
                <div class="capture_mergeCol capture_centerText capture_left">
                    <p class="capture_bigText">{| foundExistingAccountText |} <b>{| current_emailAddress |}</b>.</p>
                    <div class="capture_hover">
                        <div class="capture_popup_container">
                            <span class="capture_popup-arrow"></span>{| moreInfoHoverText |}<br />
                            {| existing_displayName |} - {| existing_provider |} : {| existing_siteName |} {| existing_createdDate |}
                        </div>
                        {| moreInfoText |}
                    </div>
                </div>
                <div class="capture_mergeCol capture_mergeExisting_col capture_right">
                    <div class="capture_shadow capture_backgroundColor capture_border">
                        {| rendered_existing_provider_photo |}
                        <div class="capture_displayName_col">
                            {| existing_displayName |}<br />
                            {| existing_provider_emailAddress |}
                        </div>
                        <span class="capture_mergeProvider janrain-provider-icon-16 janrain-provider-icon-{| existing_provider_lowerCase |} "></span>
                        <div class="capture_centerText capture_smallText">Created {| existing_createdDate |} at {| existing_siteName |}</div>
                    </div>
                </div>
            </div>
            <div id="capture_mergeAccounts_form_collection_mergeAccounts_mergeRadio" class="capture_form_collection_merge_radioButtonCollection capture_form_collection capture_elementCollection capture_form_collection_mergeAccounts_mergeRadio" data-capturefield="undefined">
                <div id="capture_mergeAccounts_form_item_mergeAccounts_mergeRadio_1_0" class="capture_form_item capture_form_item_mergeAccounts_mergeRadio capture_form_item_mergeAccounts_mergeRadio_1_0 capture_toggled" data-capturefield="undefined">
                    <label for="capture_mergeAccounts_mergeAccounts_mergeRadio_1_0">
                        <input id="capture_mergeAccounts_mergeAccounts_mergeRadio_1_0" data-capturefield="undefined" data-capturecollection="true" value="1" type="radio" class="capture_mergeAccounts_mergeRadio_1_0 capture_input_radio" checked="checked" name="mergeAccounts_mergeRadio">
                            {| connectLegacyRadioText |}
                    </label>
                </div>
                <div id="capture_mergeAccounts_form_item_mergeAccounts_mergeRadio_2_1" class="capture_form_item capture_form_item_mergeAccounts_mergeRadio capture_form_item_mergeAccounts_mergeRadio_2_1" data-capturefield="undefined">
                    <label for="capture_mergeAccounts_mergeAccounts_mergeRadio_2_1">
                        <input id="capture_mergeAccounts_mergeAccounts_mergeRadio_2_1" data-capturefield="undefined" data-capturecollection="true" value="2" type="radio" class="capture_mergeAccounts_mergeRadio_2_1 capture_input_radio" name="mergeAccounts_mergeRadio">
                            {| createRadioText |} {| current_provider |}
                    </label>
                </div>
                <div class="capture_tip" style="display:none;">
            </div>
                <div class="capture_tip_validating" data-elementname="mergeAccounts_mergeRadio">Validating</div>
                <div class="capture_tip_error" data-elementname="mergeAccounts_mergeRadio"></div>
            </div>
            <div class="capture_footer">
                {| connect_button |}
                {| create_button |}
            </div>
        </div>
    </div>

    <!-- traditionalAuthenticateMerge:
        When the user elects to merge their traditional and social account, the
        user will see this screen. They will then enter their current sign in
        credentials and, upon successful authorization, the accounts will be
        merged.
    -->
    <div style="display:none; background-color:#fff;" id="traditionalAuthenticateMerge">
        <div class="capture_header">
            <h1>Sign in to complete account merge</h1>
        </div>
        <div class="capture_signin">
            {* #signInForm *}
                {* signInEmailAddress *}
                {* currentPassword *}
                <div class="capture_footer">
                    <div class="capture_left">
                        {* backButton *}
                    </div>
                    <div class="capture_right">
                        <button class="capture_secondary capture_btn capture_primary" type="submit"><span class="janrain-icon-16 janrain-icon-key"></span> Sign In</button>
                    </div>
                </div>
             {* /signInForm *}
        </div>
    </div>




    <!--
    ============================================================================
        EMAIL VERIFICATION SCREENS:
        The following screens are part of the email verification user workflow.
        For a complete out-of-the-box email verification experience, these
        screens must be included on page where you are implementing email
        verification.
    ============================================================================
    -->

    <!-- verifyEmail:
        This is the landing screen after a user clicks on the link in the
        verification email sent to the user when they've registered with a
        non-verified email address.

        HOW IT WORKS: The code that is generated by Capture and included in the
        link sent in the verification email is sent to the server and, if valid,
        the user's email will be marked as valid and the verifyEmailSuccess
        screen will be rendered. If the code is not accepted for any reason,
        the verifyEmail screen is shown and the user has another opportunity
        to have the verification email sent to them.

        NOTE: The links generated in the emails sent to users are based on
        Capture settings found in Janrain's Capture Dashboard. In addition to
        entering the URL of your email verification page, you will need to add
        'screenToRender' as a parameter in the URL with a value of 'verifyEmail'
        which is this screen.
    -->
    <div style="display:none; background-color:#fff;" id="verifyEmail">
        <div class="capture_header">
            <h1>Resend Email Verification</h1>
        </div>
        <p>Sorry we could not verify that email address. Enter your email below and we'll send you another email.</p>
        {* #resendVerificationForm *}
            {* signInEmailAddress *}
            <div class="capture_footer">
                <input value="Submit" type="submit" class="capture_btn capture_primary">
            </div>
         {* /resendVerificationForm *}
    </div>

    <!-- resendVerificationSuccess:
        This screen is rendered when a user enters an email address from the
        verifyEmail screen.
    -->
    <div style="display:none; background-color:#fff;" id="resendVerificationSuccess">
        <div class="capture_header">
            <h1>Your Verification Email Has Been Sent</h1>
        </div>
        <div class="hr"></div>
        <p>Check your email for a link to reset your password.</p>
        <div class="capture_footer">
            <a href="index.html" class="capture_btn capture_primary">Sign in</a>
        </div>
    </div>

    <!-- verifyEmailSuccess:
        This screen is rendered if the verification code provided in the link
        sent to the user in the verification email is accepted and the user's
        email address has been verified.
    -->
    <div style="display:none; background-color:#fff;" id="verifyEmailSuccess">
        <div class="capture_header">
            <h1>You did it!</h1>
        </div>
        <p>Thank you for verifiying your email address.
        <div class="capture_footer">
            <a href="index.html" class="capture_btn capture_primary">Sign in</a>
        </div>
    </div>




    <!--
    ============================================================================
        RESET PASSWORD SCREENS:
        The following screens are part of the password reset user workflow.
        For a complete out-of-the-box password reset experience, these screens
        must be included on the page where you are implementing password reset
        functionality.

        NOTE: The order in which these screens are rendered is as follows:
        resetPasswordRequestCode
        resetPasswordRequestCodeSuccess
        resetPassword
        resetPasswordSuccess
    ============================================================================
    -->

    <!-- resetPassword:
        This screen is rendered when the user clicks the link in provided in the
        password reset email and the code in the link is valid.
    -->
    <div style="display:none; background-color:#fff;" id="resetPassword">
        <div class="capture_header">
            <h1>Change password</h1>
        </div>
        {* #changePasswordFormNoAuth *}
            {* newPassword *}
            {* newPasswordConfirm *}
            <div class="capture_footer">
                <input value="Submit" type="submit" class="capture_btn capture_primary">
            </div>
        {* /changePasswordFormNoAuth *}
    </div>
    <!-- resetPasswordSuccess:
        This screen is rendered when the user successfully changes their
        password from the resetPassword screen.
    -->
    <div style="display:none; background-color:#fff;" id="resetPasswordSuccess">
        <div class="capture_header">
            <h1>Your password has been changed</h1>
        </div>
        <p>Password has been successfully updated.</p>
        <div class="capture_footer">
            <a href="index.html" class="capture_btn capture_primary">Sign in</a>
        </div>
    </div>
    <!-- resetPasswordRequestCode:
        This is the landing screen for the password reset workflow. When the
        user clicks the link provided in the reset password email, a code is
        supplied and is passed to Capture for verification. If the code is valid
        the resetPassword screen is rendered immediately and the content of
        this screen is not presented. If the code is not accepted for any reason
        this screen is then presented, allowing the user to re-enter their
        email address.
    -->
    <div style="display:none; background-color:#fff;" id="resetPasswordRequestCode">
        <div class="capture_header">
            <h1>Create a new password</h1>
        </div>
        <p>We didn't recognize that password reset code. Enter your email address to get a new one.</p>
        {* #resetPasswordForm *}
            {* signInEmailAddress *}
            <div class="capture_footer">
                <input value="Send" type="submit" class="capture_btn capture_primary">
            </div>
        {* /resetPasswordForm *}
    </div>

    <!-- resetPasswordRequestCodeSuccess:
        This screen is rendered if the user submitted an email address on the
        resetPasswordRequestCode screen.
    -->
    <div style="display:none; background-color:#fff;" id="resetPasswordRequestCodeSuccess">
        <div class="capture_header">
            <h1>Create a new password</h1>
        </div>
            <p>We've sent an email with instructions to create a new password. Your existing password has not been changed.</p>
        <div class="capture_footer">
            <a href="#" onclick="janrain.capture.ui.modal.close()" class="capture_btn capture_primary">Close</a>
        </div>
    </div>




    <!--
    ============================================================================
        EDIT PROFILE SCREENS:
        The following screens are part of the profile editing user workflow.
        For a complete out-of-the-box profile editing experience, these screens
        must be included on the page where you are implementing profile editing
        functionality.
    ============================================================================
    -->

    <!-- editProfile
        This screen is where the user can edit their profile data. It can be
        rendered in whatever way works best for your implementation, be it
        using the data-capturescreen attribute, janrain.capture.ui.renderScreen
        or passing in 'screenToRender' in the URL linking to the page where
        you have implemented edit profile.
    -->
    <div style="display:none; background-color:#fff; z-index:999;" id="editProfile">
        <h1>Edit Your Account</h1>
        <div class="capture_grid_block" style="background-color:#fff; z-index:999;">
            <div class="capture_col_4" style="background-color:#fff; z-index:999;">
                <h3>Profile Photo</h3>
                <div class="contentBoxWhiteShadow">
                    {* photoManager *}
                </div>
                <h3>Linked Accounts</h3>
                <div class="contentBoxWhiteShadow">
                    {* linkedAccounts *}
                    {* #linkAccountContainer *}
                        <div class="capture_header">
                            <h1>Link your accounts</h1>
                        </div>
                        <h2>Allows you to sign in to your account using that provider in the future.</h2>
                        <div class="capture_signin">
                            {* loginWidget *}
                        </div>
                    {* /linkAccountContainer *}
                </div>
                <!-- Only show this if it was from a traditional login !-->
                <h3 class="janrain_traditional_account_only">Password</h3>
                <div class="janrain_traditional_account_only contentBoxWhiteShadow">
                    <a href="#" data-capturescreen="changePassword">Change Password</a>
                </div>
                <h3>Deactivate Account</h3>
                <div class="capture_deactivate_section contentBoxWhiteShadow clearfix">
                    <a href="#" data-capturescreen="confirmAccountDeactivation">Deactivate Account</a>
                </div>
            </div>
            <div class="capture_col_8" style="background-color:#fff; z-index:999;">
                <h3>Account Info</h3>
                <div class="contentBoxWhiteShadow">
                    <div class="capture_grid_block">
                        <div class="capture_center_col capture_col_8">
                            <div class="capture_editCol">
                                {* #editProfileForm *}
                                    {* firstName *}
                                    {* lastName *}
                                    {* gender *}
                                    {* birthdate *}
                                    {* displayName *}
                                    {* emailAddress *}
                                    {* resendLink *}
                                    {* phone *}
                                    {* addressStreetAddress1 *}
                                    {* addressStreetAddress2 *}
                                    {* addressCity *}
                                    {* addressPostalCode *}
                                    {* addressState *}
                                    {* addressCountry *}
                                    <!-- PPD: Add your progressive field here ONLY IF you want it to be part of the edit profile form -->
                                    {* totalCars *}
                                    <div class="capture_form_item">
                                        <input value="Save" type="submit" class="capture_btn capture_primary">
                                        {* savedProfileMessage *}
                                    </div>
                                {* /editProfileForm *}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- changePassword:
        This screen is rendered when the user clicks the 'Change Password' link
        on the edit profile page. After the user enters their new password,
        the edit profile screen is refreshed and displayed.
    -->
    <div style="display:none; background-color:#fff;" id="changePassword">
        <div class="capture_header">
            <h1>Change password</h1>
        </div>
        {* #changePasswordForm *}
            {* currentPassword *}
            {* newPassword *}
            {* newPasswordConfirm *}
            <div class="capture_footer">
                <input value="Save" type="submit" class="capture_btn capture_primary">
            </div>
        {* /changePasswordForm *}
    </div>

    <!-- confirmAccountDeactivation:
        If the user clicks the 'Deactivate Account' link on the edit profile
        page, this screen is rendered. From here, the user can deactivate their
        account.
    -->
    <div style="display:none; background-color:#fff;" id="confirmAccountDeactivation">
        <div class="capture_header">
            <h1>Deactivate your Account</h1>
        </div>
        <div class="content_wrapper">
            <p>Are you sure you want to deactivate your account? You will no longer have access to your profile.</p>
            {* deactivateAccountForm *}
                    <div class="capture_footer">
                        <input value="Yes" type="submit" class="capture_btn capture_primary">
                        <a href="#" id="capture_confirmAccountDeactivation_noButton" onclick="janrain.capture.ui.modal.close()" class="capture_btn capture_primary">No</a>
                    </div>
                </div>
            {* /deactivateAccountForm *}
    </div>

    <pre><?php // print_r($_COOKIE); phpinfo(); ?></pre>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

</body>
</html>