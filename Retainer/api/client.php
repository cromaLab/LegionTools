<?php


<?php

// Fix URL
// Introduce way to

//$api_request_url = 'https://legionpowered.net/FP/LegionTools/Retainer/api/api.php/retainer/preparation';
//$api_request_url = 'https://legionpowered.net/FP/LegionTools/Retainer/api/api.php/retainer/prep2';
//$api_request_url = 'https://legionpowered.net/FP/LegionTools/Retainer/api/api.php/retainer/call';
$api_request_url = 'https://legionpowered.net/FP/LegionTools/Retainer/api/api.php/retainer/call2';
//$api_request_url = 'https://legionpowered.net/FP/LegionTools/Retainer/api/api.php/retainer/query';

//Retainer/php/setFire.php and Retainer/php/updateReleased.php

//'http://localhost:4041/api.php/retainer/call';

// Method to use - GET, POST, PUT or DELETE
$method_name = 'POST';

// Authenticate user
//$api_request_parameters = array(
//    'numFire' => 1337,
//    'task' => "Test",
//    'fireToURL' => 'Test2'
//);

$api_request_parameters = array(
    'numFire' => 1,
        'task' => 'Experiment',
            'useSandbox' => true,
                'requireUniqueWorkers' => false,
                    'mode' => 'retainer',
                        'instrPageUrl' => 'https://legionpowered.net/instructions/robocrowd/task.html',
                            'tutPageUrl' => 'https://legionpowered.net/FP/FakeTutorial.html',
                                'waitPageUrl' => 'https://legionpowered.net/instructions/robocrowd/waiting.html',
                                    'link' => 'https://legionpowered.net/FP/Test/testHIT.html', // fireToURL
                                        'accessKey' => 'ACCESSKEY',
                                            'secretKey' => 'SECRETKEY'
                                            );

                                            $ch = curl_init();
                                            curl_setopt_array($ch, array(
                                            CURLOPT_RETURNTRANSFER => true,
                                            CURLOPT_USERPWD => 'ACCESSKEY:SECRETKEY'
                                            ));

                                            if ($method_name == 'GET')
                                            {
                                                // NOP
                                                }

                                                if ($method_name == 'POST')
                                                {
                                                    curl_setopt($ch, CURLOPT_POST, TRUE);
                                                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_request_parameters));
                                                        }

                                                        if ($method_name == 'PUT')
                                                        {
                                                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                                                                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_request_parameters));
                                                                }

                                                                if ($method_name == 'DELETE')
                                                                {
                                                                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                                                                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_request_parameters));
                                                                        }

                                                                        // Preferred response content type (application/json, application/xml, text/html, text/plain)
                                                                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));

                                                                        curl_setopt($ch, CURLOPT_URL, $api_request_url);

                                                                        curl_setopt($ch, CURLOPT_HEADER, TRUE);

                                                                        // If using HTTPS
                                                                        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                                                                        // Execute
                                                                        $api_response = curl_exec($ch);

                                                                        $api_response_info = curl_getinfo($ch);

                                                                        curl_close($ch);

                                                                        // Extract response header and body
                                                                        $api_response_header = trim(substr($api_response, 0, $api_response_info['header_size']));
                                                                        $api_response_body = substr($api_response, $api_response_info['header_size']);

                                                                        // Response HTTP Status Code
                                                                        echo $api_response_info['http_code'] . "\n";

                                                                        // Response Header
                                                                        echo $api_response_header  .  "\n";

                                                                        // Response Body
                                                                        echo $api_response_body;
