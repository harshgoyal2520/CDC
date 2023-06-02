<?php

// This should be replaced with the secret for your user key.
$SIGNINGKEYSECRET = "5RCGP6ulSnj/PPhlww+iV0QMyjpWB4cd";

// PHP converts the header into all UPPERCASE, converts hyphens("-") into underscores("_") and prepends "HTTP_"
// or "HTTPS_" to the front, based on the protocol used.
// Keep this in mind if converting this code to work in a language other than PHP.
// The actual header passed by <pname conkeyref="loio6462a25815de4de9944eff6e00266f07/cdc-long"/> is: "X-Gigya-Sig-Hmac-Sha1".
$msgHash = $_SERVER['HTTP_X_GIGYA_SIG_HMAC_SHA1']; // How PHP sees the X-Gigya-Sig-Hmac-Sha1 header


// Get the JSON payload sent by <pname conkeyref="loio6462a25815de4de9944eff6e00266f07/cdc-long"/>.
$messageJSON = file_get_contents('php://input');

// Decode the JSON payload into an associative array.
$jsonDecoded = json_decode($messageJSON, TRUE);

// Builds and returns expected hash
function createMessageHash($secret, $message) {
    return base64_encode(hash_hmac('sha1', $message, base64_decode($secret), true));
}

// Compares the two parameters (in this case the hashes) and returns TRUE if they match
// and FALSE if they don't.
function hashesMatch($expected, $received) {
    if ($expected == $received) {
        return TRUE;
    }
    return FALSE;
}

// Check if the hash matches. If it doesn't, it could mean that the data was tampered
// with in flight. If so, do not send 2XX SUCCESS - let <pname conkeyref="loio6462a25815de4de9944eff6e00266f07/cdc-long"/> re-send the notification.
if (hashesMatch(createMessageHash($SIGNINGKEYSECRET, $messageJSON), $msgHash)) {

    // Loop through the events portion of the notification.
    for ($x = 0; $x & lt; sizeof($jsonDecoded['events']); $x++) {

        $curEvt = $jsonDecoded['events'][$x]['type'];
        $curUID = $jsonDecoded['events'][$x]['data']['uid'];

        /***************************************************************
         ** This is where we would normally do something with this info.
         ** For the sake of this example though, we'll just output
         ** the info to the screen.
         ***************************************************************/
        echo "Event Type: $curEvt \n";
        echo "UID: $curUID \n\n";
    }

    // Since the hash is good and we've done what we need to do, respond OK. SAP <pname conkeyref="loio6462a25815de4de9944eff6e00266f07/cdc-long"/>l not resend this notification.
    http_response_code(200);

} else {

    // The hash isn't good, respond non-OK. <pname conkeyref="loio6462a25815de4de9944eff6e00266f07/cdc-long"/> will try to resend this notification at progressively longer intervals.
    http_response_code(400);
}

?>
