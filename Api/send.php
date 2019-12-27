<?php
require_once('../funcs.php');
$db->insert('users', [
    'access_token' => bin2hex(openssl_random_pseudo_bytes(64)),
    'first_name' => "mehdi",
    'last_name' => "shahpoury",
    'username' => "09361722175",
]);
$arr = [
    'status' => true,
    'data' => [$db]
];
echo json_encode($arr);
