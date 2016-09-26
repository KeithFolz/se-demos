/*
Janrain initializations and settings for JUMP.

For more information about these settings, see the following documents:

    http://developers.janrain.com/documentation/widgets/social-sign-in-widget/social-sign-in-widget-api/settings/
    http://developers.janrain.com/documentation/widgets/user-registration-widget/capture-widget-api/settings/
    http://developers.janrain.com/documentation/widgets/social-sharing-widget/sharing-widget-js-api/settings/
*/

(function() {
    if (typeof window.janrain !== 'object') window.janrain = {};
    window.janrain.settings = {};
    window.janrain.settings.capture = {};
    janrain.settings.packages = ['login', 'capture'];

    // --- Engage Widget -------------------------------------------------------

    janrain.settings.language = 'en-US';
    janrain.settings.appUrl = 'https://janrain-se-demo.rpxnow.com';
    janrain.settings.tokenUrl = 'http://localhost/';
    //janrain.settings.tokenAction = 'hybrid';
    //janrain.settings.popup =  false;
    //janrain.settings.type =  'embed';
    //Mobile Testing:
    //janrain.settings.capture.redirectFlow = true;
    //janrain.settings.capture.redirectUri = location.href;
    //janrain.settings.popup = false;
    //janrain.settings.tokenAction = 'url';
    janrain.settings.tokenAction = 'event';
    janrain.settings.borderColor = '#ffffff';
    janrain.settings.fontFamily = 'Helvetica, Lucida Grande, Verdana, sans-serif';
    janrain.settings.width = 300;
    janrain.settings.actionText = ' ';

    // --- Capture Widget ------------------------------------------------------

    janrain.settings.capture.appId = 'hevwjvt8j7cym5hbbzdu8mv6aj';
    janrain.settings.capture.captureServer = 'https://janrain-se-demo.eval.janraincapture.com';
    janrain.settings.capture.redirectUri = 'http://localhost/';
    janrain.settings.capture.clientId = 'z4mptjsqd62ux4k4w3mz4s6cjjym4hdc';
    janrain.settings.capture.flowVersion = 'HEAD';
    janrain.settings.capture.registerFlow = 'socialRegistration';
    janrain.settings.capture.setProfileCookie = true;
    janrain.settings.capture.keepProfileCookieAfterLogout = true;
    janrain.settings.capture.modalCloseHtml = '<span class="janrain-icon-16 janrain-icon-ex2"></span>';
    janrain.settings.capture.noModalBorderInlineCss = true;
    janrain.settings.capture.responseType = 'token';
    janrain.settings.capture.returnExperienceUserData = ['displayName'];
    janrain.settings.capture.maxScreenHistory = 0;


    // --- Load URLs -----------------------------------------------------------

    var httpsLoadUrl = "https://rpxnow.com/load/janrain-se-demo";
    var httpLoadUrl = "http://widget-cdn.rpxnow.com/load/janrain-se-demo";

    // --- DO NOT EDIT BELOW THIS LINE -----------------------------------------

    function isReady() { janrain.ready = true; };
    if (document.addEventListener) {
        document.addEventListener("DOMContentLoaded", isReady, false);
    } else {
        window.attachEvent('onload', isReady);
    }

    var e = document.createElement('script');
    e.type = 'text/javascript';
    e.id = 'janrainAuthWidget';
    if (document.location.protocol === 'https:') {
        e.src = httpsLoadUrl;
    } else {
        e.src = httpLoadUrl;
    }
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(e, s);
})();



function janrainReturnExperience() {
    var span = document.getElementById('traditionalWelcomeName');
    var name = janrain.capture.ui.getReturnExperienceData("displayName");
    if (span && name) {
        span.innerHTML = "Welcome back, " + name + "!";
    }
}
