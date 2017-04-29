<?php
//localhost:8080/Retainer/api/fakeClient.php

//$data = array('task'=>1, 'useSandbox'=>'', 'accessKey'=>'', 'secretKey'=>'', 'mode'=>'retainer',
//    'requireUniqueWorkers'=>'', 'tutPageUrl'=>'', ''=>'', 'waitPageUrl'=>'', 'instrPageUrl'=>'');


$data = array(
    'foo' => 'bar',
    'baz' => 'boom',
    'cow' => 'milk',
    'test' => 'hypertext processor'
);

$queryString = http_build_query($data);
$api_request_url = 'http://localhost:8080/Retainer/api/fakeService.php?' . $queryString;
echo $api_request_url;

$ch = curl_init($api_request_url);
curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERPWD => 'sairohit:saisactualpassword'
));
$result = curl_exec($ch);

echo $result;
$api_response_info = curl_getinfo($ch);
echo $api_response_info['http_code'] . "\n";

curl_close($ch);
echo 'done';
