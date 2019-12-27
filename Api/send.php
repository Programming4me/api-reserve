<?php
require_once ('../funcs.php');

$arr = [
    'status' => true,
    'data' => [$db]
];
echo  json_encode($arr);
