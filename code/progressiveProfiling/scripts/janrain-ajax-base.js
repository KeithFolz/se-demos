
    /* ---------------------------------
     * 
     * Handle Ajax calls
     * 
     --------------------------------- */
    function ajaxIsSuccess(mode,result) {
        switch(mode) {
            case 'load-profile':
                console.log('[DEBUG] User profile loaded');
                break;
            case 'event-data-capture':
                console.log('[DEBUG] HANDLING EVENT DATA CAPTURE');
                // *** STEP 4: has user already answered the question?
                console.log('FIELD: ' + fieldName + " | VALUE: " + eval("result.result.extendedProgressiveProfile." + fieldName));
                userValue = eval("result.result.extendedProgressiveProfile." + fieldName);
                if ((userValue == '')||(userValue == null)||(userValue == undefined)) {
                    console.log('[DEBUG] No value set, so display data form');
                    document.getElementById('progressiveProfilingPopUp').style.display = '';
                } else {
                    console.log('[DEBUG] ALL GOOD. Field '+fieldName+' already set to: ' + userValue);
                    document.getElementById('progressiveProfilingPopUp').style.display = 'none';
                }
                break;
            case 'add-user-data':
                if (result.stat == 'ok') {
                    console.log('[DEBUG] User data saved!');
                    document.getElementById('progressiveProfilingPopUp').style.display = 'none';
                    alert('Thanks for your reply ' +janrain.capture.ui.getReturnExperienceData("displayName")+ '!');
/*                  
                    document.getElementById('msgStatus').style.backgroundColor='#0f820f';
                    document.getElementById('msgStatus').style.display = '';
                    document.getElementById('msgStatus').innerHTML = 'Thanks, ' + janrainMsgStatus + ' has been added to your subscriptions.';
                    loadSubscriptions();
*/
                    }
                break;
            default:
                console.log("SUCCESS in mode "+mode+"\nStatus returned: " + result.stat + "\nResults found: " + result.result_count + "\nCheck the console log for more info");
                break;
        }
        console.log(result);
    }

    function ajaxIsError(mode,result) {
        console.log("[DEBUG] ERROR in mode "+mode+"! Check the console log for more info.");
        console.log(result);
    }

    function ajaxCall(callParams) {
        var callType = callParams["type"] != '' ? callParams["type"] : 'POST';
        var callUrl = callParams['url'];
        var callData = callParams['data'];
        var callMode = callParams['mode'];

        $.ajax({
                type: callType,
                url: callUrl,
                data: callData,
                success: function(res) {
                    console.log('data type returned is: ' + typeof(res));
                    // *** If callback returns an object (as opposed to a string) then "stringify"! *** //
                    if (typeof(res) === 'object') {
                        res = JSON.stringify(res);
                    }
                    var resData = JSON.parse(res);
                    // console.log(resData);
                    ajaxIsSuccess(callMode,resData);
                },
                error: function(callMode,err) {
                    console.log('data type returned is: ' + typeof(res));
                    ajaxIsError(err);
                }
        });
        console.log(callParams);
    }