<?php

require_once('../funcs.php');

$req = $_REQUEST;
$user = $db->getUserByToken(getallheaders()['token']);

$result = $db->insert('reports', [
    'user_id' => $user['id'],
    'start_time' => $_GET['start_time'],
    'end_time' => $_GET['end_time'],
]);

if ($result)
    $arr = [
        'status' => true,
        'data' => [$db->select('reports')->fetch_all()]
    ];
else
    $arr = [
        'status' => false,
        'data' => [$db->select('reports')->fetch_all()]
    ];

echo json_encode($arr);
