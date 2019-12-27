<?php
require_once('../funcs.php');
dd($_GET['username']);
$db->insertUserByUsername('username', [
    'username' => "09361722174",
    'access_token' => bin2hex(openssl_random_pseudo_bytes(64)),
    'first_name' => "mehdi",
    'last_name' => "shahpoury",
    'password'=> password_hash('123',PASSWORD_DEFAULT)

]);
$arr = [
    'status' => true,
    'data' => [$db->select('users')]
];
echo json_encode($arr);
