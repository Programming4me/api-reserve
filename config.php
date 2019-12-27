<?php
/*
اوپن شده در کانال وین تب 
@Win_Tab
اوپن.کننده مزداب
@Mr_MoRdaB

*/
set_time_limit(0);
ini_set('max_execuation_time',9999999);
error_reporting(0);
require('Database.php');
$args = ['localhost'=> 'localhost', 'username'=> 'hooshman_user', 'password'=> 'mahdi@0913', 'database'=> 'hooshman_test']; // اطلاعات مربوط به دیتابیس
$db = new database($args);
return $db;
$sudo = [300459605,387036499,252174441,342929908];
define('token', '721048021:AAHm9WwAYeykhBI7fcNV-fzCTYa3JaJjpqY'); // محل قرار گیری توکن
define('logs',-1001275244057); // محل قرار گیری آیدی گروه
$db->create('sounds',['id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY','hash varchar(7)','file_id text(35)','cover text(35)','file_id320 text(35)','src_url text(255)','src text(255)','title text(255)','query text(255)','duration int(10)','file_size int(10)','likes int(5)','dislikes int(5)','edit int(1)','downloads int(5)','album_id varchar(8)']);
$db->create('users',['id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY','user_id int(10)','step varchar(15)','reg_date int(12)','last_ads_id int(10)','last_ads_time int(15)']);
$db->create('likes',['id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY','user_id int(10)','hash varchar(7)']);
$db->create('dislikes',['id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY','user_id int(10)','hash varchar(7)']);
$db->create('downloads',['id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY','user_id int(10)','hash varchar(7)']);
$db->create('favorites',['id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY','user_id int(10)','hash varchar(7)']);
$db->create('latest',['id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY','hash varchar(7)']);
$db->create('albums',['id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY','hash varchar(8)','title text(255)','_title text(255)','artist text(255)','_artist text(255)']);
$db->create('albums',['id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY','hash varchar(8)','title text(255)','_title text(255)','artist text(255)','_artist text(255)']);
$db->create('ads',['id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY','hash varchar(5)','sent_count int(10)','message text(4096)','file_id varchar(100)','method varchar(20)','users_count int(10)','time int(15)']);
$db->create('settings',['id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY','channel text(50)','join_ch int(1)','auto_ads int(1)','send int(1)','sent_offset int(15)','last_id int(10)']);
$db->create('sendmsg',['id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY','hash varchar(5)','message text(4096)','file_id varchar(100)','method varchar(20)','message_id int(12)', 'chat_id int(12)']);
$advertiser = 'aniestekhdam_tabriz97';
$submit_musicer = 'Skke_ir_ad';
$meta_data = ['comment'=> 'کامنت','artist'=> 'خواننده ','album_artist'=> 'نام آلبوم','album'=> 'آلبوم','title'=> 'نام آهنگ','date'=> date('Y'),'genre'=>'persian'];
// اطلاعات آهنگ ارسال شونده در کانال


include('funcs.php');
config();
