<?php
require_once('../funcs.php');

$db->insertUserByUsername('username', [
    'username' => "09361722174",
    'access_token' => bin2hex(openssl_random_pseudo_bytes(64)),
    'first_name' => "mehdi",
    'last_name' => "shahpoury",

]);
$arr = [
    'status' => true,
    'data' => [$db->select('users')]
];
echo json_encode($arr);
