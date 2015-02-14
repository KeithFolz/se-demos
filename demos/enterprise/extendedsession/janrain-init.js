/*
Initializations and settings for the Capture Widget.

For more information about these settings, see the following documents:

    http://developers.janrain.com/documentation/widgets/social-sign-in-widget/social-sign-in-widget-api/settings/
    http://developers.janrain.com/documentation/widgets/user-registration-widget/capture-widget-api/settings/
*/

(function() {
    // Check for settings. If there are none, create them
    if (typeof window.janrain !== 'object') window.janrain = {};
    if (typeof window.janrain.settings !== 'object') window.janrain.settings = {};
    if (typeof window.janrain.settings.capture !== 'object') window.janrain.settings.capture = {};

    // Load Engage and Capture. 'login' is Engage, 'capture' is Capture.
    // Changing these values without guidance can result in unexpected behavior.
    janrain.settings.packages = ['login', 'capture'];

    /*--- Application Settings -----------------------------------------------*\

        When transitioning from a development to production, these are the
        settings that need to be changed. Others may also need to be changed if
        you have purchased optional products and features, such as Federate.
        Those settings are located below.

        janrain.settings.appUrl:
            The URL of your Engage application.
            Example: https://your-company.rpxnow.com

        janrain.settings.capture.captureServer:
            The URL of your Capture application.
            Example: https://your-company.janraincapture.com

        janrain.settings.capture.appId:
            The the application ID of your Capture application.

        janrain.settings.capture.clientId:
            The client ID of the Capture application.

        Example Dev Configuration:
            janrain.settings.appUrl                = 'https://your-company-dev.rpxnow.com';
            janrain.settings.capture.captureServer = 'https://your-company-dev.janraincapture.com';
            janrain.settings.capture.appId         = <DEV CAPTURE APP ID>;
            janrain.settings.capture.clientId      = <DEV CAPTURE CLIENT ID>;
            var httpLoadUrl                        = "https://rpxnow.com/load/your-company-dev";
            var httpsLoadUrl                       = "http://widgets-cdn.rpxnow.com/load/your-company-dev";

        Example Prod Configuration:
            janrain.settings.appUrl                = 'https://login.yourcompany.com';
            janrain.settings.capture.captureServer = 'https://your-company.janraincapture.com';
            janrain.settings.capture.appId         = <PROD CAPTURE APP ID>;
            janrain.settings.capture.clientId      = <PROD CAPTURE CLIENT ID>;
            var httpLoadUrl                        = "https://rpxnow.com/load/login.yourcompany.com";
            var httpsLoadUrl                       = "http://widgets-cdn.rpxnow.com/load/login.yourcompany.com";
    \*------------------------------------------------------------------------*/

    janrain.settings.appUrl = 'https://janrain-se-demo.rpxnow.com';

    janrain.settings.capture.captureServer = 'https://janrain-se-demo.eval.janraincapture.com';

    janrain.settings.capture.appId = 'hevwjvt8j7cym5hbbzdu8mv6aj';

    janrain.settings.capture.clientId = 't2vrzpx4fsyz9vxv7uv7he7xugusrex7';

    // These are the URLs for your Engage app's load.js file, which is necessary
    // to load the Capture Widget.

    var httpsLoadUrl = "https://rpxnow.com/load/janrain-se-demo";
    var httpLoadUrl = "http://widget-cdn.rpxnow.com/load/janrain-se-demo";

    // --- Engage Widget Settings ----------------------------------------------
    janrain.settings.language = 'en-US';
    janrain.settings.tokenUrl = 'http://localhost/';
    janrain.settings.tokenAction = 'event';
    janrain.settings.borderColor = '#ffffff';
    janrain.settings.fontFamily = 'Helvetica, Lucida Grande, Verdana, sans-serif';
    janrain.settings.width = 300;
    janrain.settings.actionText = ' ';

    // --- Capture Widget Settings ---------------------------------------------
    janrain.settings.capture.redirectUri = 'http://localhost/';
    janrain.settings.capture.flowName = 'standard_newer';
    janrain.settings.capture.flowVersion = 'HEAD';
    janrain.settings.capture.registerFlow = 'socialRegistration';
    janrain.settings.capture.setProfileCookie = true;
    janrain.settings.capture.keepProfileCookieAfterLogout = true;
    janrain.settings.capture.modalCloseHtml = 'X';
    janrain.settings.capture.noModalBorderInlineCss = true;
    janrain.settings.capture.responseType = 'token';
    janrain.settings.capture.returnExperienceUserData = ['displayName'];
    janrain.settings.capture.stylesheets = ['/JanrainDemoSites/default/styles/janrain.css'];
    janrain.settings.capture.mobileStylesheets = ['/JanrainDemoSites/default/styles/janrain-mobile.css'];

    // --- Mobile WebView ------------------------------------------------------
    //janrain.settings.capture.redirectFlow = true;
    //janrain.settings.popup = false;
    //janrain.settings.tokenAction = 'url';
    //janrain.settings.capture.registerFlow = 'socialMobileRegistration'

    // --- Federate ------------------------------------------------------------
    //janrain.settings.capture.federate = true;
    //janrain.settings.capture.federateServer = '';
    //janrain.settings.capture.federateXdReceiver = '';
    //janrain.settings.capture.federateLogoutUri = '';
    //janrain.settings.capture.federateLogoutCallback = function() {};
    //janrain.settings.capture.federateEnableSafari = false;

    // --- Backplane -----------------------------------------------------------
    janrain.settings.capture.backplane = true;
    janrain.settings.capture.backplaneBusName = 'se-demo';
    janrain.settings.capture.backplaneVersion = 1.2;
    janrain.settings.capture.backplaneBlock = 20;

    // --- BEGIN WIDGET INJECTION CODE -----------------------------------------
    /********* WARNING: *******************************************************\
    |      DO NOT EDIT THIS SECTION                                            |
    | This code injects the Capture Widget. Modifying this code can cause the  |
    | Widget to load incorrectly or not at all.                                |
    \**************************************************************************/

    function isReady() {
        janrain.ready = true;
    }
    if (document.addEventListener) {
        document.addEventListener("DOMContentLoaded", isReady, false);
    } else {
        window.attachEvent('onload', isReady);
    }

    var injector = document.createElement('script');
    injector.type = 'text/javascript';
    injector.id = 'janrainAuthWidget';
    if (document.location.protocol === 'https:') {
        injector.src = httpsLoadUrl;
    } else {
        injector.src = httpLoadUrl;
    }
    var firstScript = document.getElementsByTagName('script')[0];
    firstScript.parentNode.insertBefore(injector, firstScript);

    // --- END WIDGET INJECTION CODE -------------------------------------------

})();



// This function is called by the Capture Widget when it has completred loading
// itself and all other dependencies. This function is required, and must call
// janrain.capture.ui.start() for the Widget to initialize correctly.
function janrainCaptureWidgetOnLoad() {
    var implFuncs = janrainExampleImplementationFunctions(); // Located below.

    /*==== CUSTOM ONLOAD CODE START ==========================================*\
    ||  Any javascript that needs to be run before screens are rendered but   ||
    ||  after the Widget is loaded should go between this comment and "CUSTOM ||
    ||  ONLOAD CODE END" below.                                               ||
    \*                                                                        */

    /*--
        SCREEN TO RENDER:
        This setting defines which screen to render. We've set it to the result
        of implFuncs.getParameterByName() so that if you pass in a parameter
        in your URL called 'screenToRender' and provide a valid screen name,
        that screen will be shown when the Widget loads.
                                                                            --*/
    janrain.settings.capture.screenToRender = implFuncs.getParameterByName('screenToRender');

    /*--
        EVENT HANDLING:

        Event Documentation:
        http://developers.janrain.com/reference/javascript-api/registration-js-api/events/
    --*/
    janrain.events.onCaptureScreenShow.addHandler(implFuncs.enhanceReturnExperience);
    janrain.events.onCaptureSaveSuccess.addHandler(implFuncs.hideResendLink);

    /*--
        NAVIGATION EVENTS:
        These event handlers are used for navigating the example implementation
        that exists on our servers for testing/demo/sample purposes. It is not
        required for your implementation, but can be modified to suit your
        needs. These event handlers are provided as an example.
                                                                            --*/
    janrain.events.onCaptureLoginSuccess.addHandler(implFuncs.setNavigationForLoggedInUser);
    janrain.events.onCaptureSessionFound.addHandler(implFuncs.setNavigationForLoggedInUser);
    janrain.events.onCaptureRegistrationSuccess.addHandler(implFuncs.setNavigationForLoggedInUser);
    janrain.events.onCaptureSessionEnded.addHandler(implFuncs.setNavigationForLoggedOutUser);
    janrain.events.onCaptureLoginFailed.addHandler(implFuncs.handleDeactivatedAccountLogin);
    janrain.events.onCaptureAccountDeactivateSuccess.addHandler(implFuncs.handleAccountDeactivation);

    janrain.events.onCaptureSessionNotFound.addHandler(implFuncs.handleInvalidToken);
    janrain.events.onCaptureSaveFailed.addHandler(implFuncs.handleInvalidToken);


    /*--
        SHOW EVENTS:
        Uncomment this line to show events in your browser's console. You must
        include janrain-utils.js to run this function.
                                                                            --*/
    janrainUtilityFunctions().showEvents();

    // helper function for displaying backplane events
    function writeTo(theId, content, overwrite) {
        var theElement = document.getElementById(theId);
        if (overwrite == true) {
            theElement.innerHTML = content;
            return true;
        }
        theElement.innerHTML += content;
        return true;
    }

        janrain.events.onCaptureBackplaneReady.addHandler(function(result) {

            /*
            * An important thing to note in this demo: while the Enterprise solution makes *some* of the Backplane calls
            * for you, it in no way prevents you from doing others yourself.
            *
            * For example, simply by including the Backplane settings in the overall Enterprise settings:
            *
            * janrain.settings.capture.backplane = true;
            * janrain.settings.capture.backplaneBusName = 'se-demo';
            * janrain.settings.capture.backplaneVersion = 1.2;
            * janrain.settings.capture.backplaneBlock = 20;
            *
            * , Enterprise does the work of initializing Backplane, including getting the correct Backplane javascript
            * file loaded in. So for example, you won't see calls to "Backplane.init()" or "Backplane.resetCookieChannel()"
            * in this page.
            *
            * On the other hand, you do see calls to "Backplane.getChannelID()", "Backplane.expectMessages()"
            * and "Backplane.subscribe()" here; again, you are in no way prevented from doing direct calls like this
            * by the Enterprise solution.
            *
            * What's important to remember is that if you are simply instructing Janrain Enterprise to load Backplane and
            * feed authentication events into the bus for other BP-enabled widgets to consume, all you have to do is
            * enter the settings as above, which your Janrain Technical Lead will give you during deployment. No need for
            * custom coding.
            *
            * Note that adding the BP settings can be done in our central settings file (see scripts/janrain-init.js),
            * but you can also manage these settings directly on the page - see bottom of this page where we do this.
            * */


            bpChannel = Backplane.getChannelID();

            writeTo('event-list', '<li>Channel created: <a target="_blank" href="'
                    + bpChannel + '">' + bpChannel + '</a>.<br>(Try viewing; will contain ' +
                    'channel data after you authenticate.)</li>');

            Backplane.expectMessages('identity/login');
            writeTo('event-list', '<li>Expecting messages.</li>');

            window.bpSubscription = Backplane.subscribe(function(backplaneMessage) {

                writeTo('event-list', '<li>New '+backplaneMessage.type+' message received. (Try viewing in the JS console.)</li>');
                console.log ("RAW BACKPLANE MESSAGE; TYPE == '"+backplaneMessage.type + "':");
                console.log (backplaneMessage);

                if (backplaneMessage.type == 'identity/login') {

                    var avatarUrl = '';
                    try {
                        avatarUrl = backplaneMessage.payload.identities.entry.accounts[0].photos[0].value;
                    } catch(err) {
                        console.log ("error retrieving avatarUrl: ");
                        console.log (err);
                    }
                    var avatar = '';
                    if ( avatarUrl != '' ) {
                        avatar = '<img src="'+avatarUrl+'" style="float:left; width:50px; padding:2px;">';
                    }
                    writeTo('welcome-msg', avatar+'Welcome, '+backplaneMessage.payload.identities.entry.displayName + '!', true);

                }

                //If we had no further interest in backplane events we could stop listening.
                //Backplane.unsubscribe(window.bpSubscription);

            });

        });

    /*                                                                        *\
    || *** CUSTOM ONLOAD CODE END ***                                         ||
    \*========================================================================*/

    // This should be the last line in janrainCaptureWidgetOnLoad()
    //janrain.capture.ui.start();

    // When the end-user logs in, send the access token to the server-side PHP
    // script to start the server-side session.
    janrain.events.onCaptureLoginSuccess.addHandler(function(result) {
        $.post("start_session.php", {'access_token': result.accessToken});
    });

    // When the end-user ends the client-side session, send a request to the
    // server-side PHP script to end the server-side session.
    janrain.events.onCaptureSessionEnded.addHandler(function(result) {
        $.post("end_session.php");
    });

    // If the access token stored in local storage expires (or is deleted), get
    // a new access token from the server-side PHP script and start a new
    // client-side session.
    if (localStorage.getItem("janrainCaptureToken")) {
        janrain.capture.ui.start();
    } else {
        alert("Getting new token");
        $.getJSON('new_token.php', function(result) {
            if (result.stat == "ok") {
                janrain.capture.ui.createCaptureSession(result.accessToken);
            } else {
                console.log(result.error_description);
            }
            janrain.capture.ui.start();
        });
    }
}

// Reference implementation navigation.
function janrainExampleImplementationFunctions() {


    function handleInvalidToken(result) {
        //janrain.capture.ui.modal.close();
        alert("Invalid Token!");

        $.getJSON('new_token.php', function(result) {
            if (result.stat == "ok") {
                janrain.capture.ui.createCaptureSession(result.accessToken);
            } else {
                console.log(result.error_description);
            }

        });
    }

    function setNavigationForLoggedInUser(result) {
        janrain.capture.ui.modal.close();
        document.getElementById("captureSignInLink").style.display  = 'none';
        document.getElementById("captureSignOutLink").style.display = '';
        document.getElementById("captureProfileLink").style.display = '';
    }
    function setNavigationForLoggedOutUser(result) {
        document.getElementById("captureSignInLink").style.display  = '';
        document.getElementById("captureSignOutLink").style.display = 'none';
        document.getElementById("captureProfileLink").style.display = 'none';
        document.getElementById("editProfile").style.display = 'none';
    }
    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }
    function enhanceReturnExperience(result) {
        if (result.screen == "returnTraditional") {
            var span = document.getElementById('traditionalWelcomeName');
            var name = janrain.capture.ui.getReturnExperienceData("displayName");
            if (span && name) {
                span.innerHTML = "Welcome back, " + name + "!";
            }
        }
    }
    function hideResendLink(result) {
        // Hide the 'Resend confirmation email' link if it's been clicked
        // from the edit profile page. Link will reappear if the user
        // refreshes their profile page.
        if(result.controlName == "resendVerificationEmail" &&
           result.screen == "editProfile") {
            document.getElementById("capture_editProfile_resendLink").style.display = 'none';
        }
    }
    function handleDeactivatedAccountLogin(result) {
        if (result.statusMessage == "accountDeactivated") {
            janrain.capture.ui.renderScreen('accountDeactivated');
        }
    }
    function handleAccountDeactivation(result) {
        if(result.status == "success") {
            document.getElementById("editProfile").style.display = 'none';
            janrain.capture.ui.modal.close();
            janrain.capture.ui.endCaptureSession();
            janrain.capture.ui.renderScreen('accountDeactivated');
        }
    }
    return {
        setNavigationForLoggedInUser: setNavigationForLoggedInUser,
        setNavigationForLoggedOutUser: setNavigationForLoggedOutUser,
        getParameterByName: getParameterByName,
        enhanceReturnExperience: enhanceReturnExperience,
        hideResendLink: hideResendLink,
        handleDeactivatedAccountLogin: handleDeactivatedAccountLogin,
        handleAccountDeactivation: handleAccountDeactivation,
        handleInvalidToken: handleInvalidToken
    };
}
