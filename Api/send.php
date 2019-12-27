<?php
require_once ('../Database.php');
$args = ['localhost'=> 'localhost', 'username'=> 'hooshman_user', 'password'=> 'mahdi@0912', 'database'=> 'hooshman_test']; // اطلاعات مربوط به دیتابیس
$db = new Database($args);
$arr = [
    'status' => true,
    'data' => [$db]
];
echo  json_encode($arr);
