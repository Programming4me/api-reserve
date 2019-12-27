<?php

require_once('../funcs.php');

  $req = $_REQUEST['headers'];
  dd($req);
$result = $db->insert('reports', [
    'username' => $_GET['username'],
    'username' => $_GET['username'],
    'username' => $_GET['username'],
    'username' => $_GET['username'],


]);

if ($result)
    $arr = [
        'status' => true,
        'data' => [$db->select('users')]
    ];
else
    $arr = [
        'status' => false,
        'data' => [$db->select('users', null, '*')]
    ];

echo json_encode($arr);
