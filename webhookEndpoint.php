<?php

$c = new Cache();
// received webhook from CDC server
$msgHash = $_SERVER[' x-gigya-sig-jwt']; 

// Get the JSON payload sent by <pname conkeyref="loio6462a25815de4de9944eff6e00266f07/cdc-long"/>.
$messageJSON = file_get_contents('php://input');

// Decode the JSON payload into an associative array.
$jsonDecoded = json_decode($messageJSON, TRUE);

function validateJWT($msgHash,$messageJSON){
}
function hashesMatch($expected, $received) {
    if ($expected == $received) {
        return TRUE;
    }
    return FALSE;
}

// Check if the hash matches. If it doesn't, it could mean that the data was tampered
// with in flight. If so, do not send 2XX SUCCESS - let <pname conkeyref="loio6462a25815de4de9944eff6e00266f07/cdc-long"/> re-send the notification.
if (validateJWT($msgHash,$messageJSON)) {

    // Loop through the events portion of the notification.
    for ($x = 0; $x & lt; sizeof($jsonDecoded['events']); $x++) {

        //$curEvt = $jsonDecoded['events'][$x]['type'];
        $curUID = $jsonDecoded['events'][$x]['data']['uid'];*/

        /***************************************************************
         ** This is where we would normally do something with this info.
         ** For the sake of this example though, we'll just output
         ** the info to the screen.
         ***************************************************************/
        //echo "Event Type: $curEvt \n";
        //echo "UID: $curUID \n\n";
        $c->store('LogWebhookData',$messageJSON);
        $c->store('LogUID',$curUID);
    }

    // Since the hash is good and we've done what we need to do, respond OK. SAP <pname conkeyref="loio6462a25815de4de9944eff6e00266f07/cdc-long"/>l not resend this notification.
    http_response_code(200);

} else {

    // The hash isn't good, respond non-OK. <pname conkeyref="loio6462a25815de4de9944eff6e00266f07/cdc-long"/> will try to resend this notification at progressively longer intervals.
    http_response_code(400);
}


?>
