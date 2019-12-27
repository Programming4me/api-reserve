<?php

require_once('../funcs.php');

$requestToken = getallheaders()['token'] ?? $_GET['token'];
$user = $db->getUserByToken($requestToken);
dd($_GET['start_time']);
$result = $db->insert('reports', [
    'user_id' => $user['id'],
    'start_time' => $_GET['start_time'],
    'end_time' => $_GET['end_time'],
]);

$arr = [
    'status' => true,
    'data' => [
        'reserves' => $db->fetch_all($db->select('reports'))
    ],
    'message' => null
];

if ($result) $arr['status'] = true;

else {
    $arr['status'] = false;
    $arr['message'] = getMessage('required');
}

echo json_encode($arr);
