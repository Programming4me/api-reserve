<?php
require_once('../funcs.php');

$result = $db->insertUserByUsername('username', [
    'username' => $_GET['username'],
    'access_token' => bin2hex(openssl_random_pseudo_bytes(64)),
    'first_name' => $_GET['first_name'],
    'last_name' => $_GET['last_name'],
    'password' => password_hash($_GET['password'], PASSWORD_DEFAULT)

]);

$arr = [
    'status' => true,
    'data' => [$db->select('users')->fetch_all()],
];

if ($result) $arr['status'] = true;

else {
    $arr['status'] = false;
    $arr['message'] = getMessage('unique');
}

echo json_encode($arr);
