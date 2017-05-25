<?php

// Needs logserver.py to be running

// Can be used like this:
// curl -i -H "Content-Type: aplication/json" -X POST -d '{"message":"This is an interesting debug message"}' http://localhost:5000/message/
// logDebug('This is an interesting debug message');

function logDebug($actualMessage)
{

    $data = array("message" => $actualMessage);
    $data_string = json_encode($data);

    $ch = curl_init('http://localhost:5000/message/');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
    );

    $result = curl_exec($ch);
}