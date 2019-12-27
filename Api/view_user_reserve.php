<?php

require_once('../funcs.php');

$requestToken = getallheaders()['token'] ?? $_GET['token'];
$result = $db->getUserReportsByToken($requestToken);


$arr = [
    'status' => true,
    'data' => [
        'reserves' => $result
    ],
    'message' => null
];

if ($result) $arr['status'] = true;

else {
    $arr['status'] = false;
    $arr['message'] = getMessage('no_result');
}

echo json_encode($arr);
