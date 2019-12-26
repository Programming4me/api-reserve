<?php
/*
???? ??? ?? ????? ??? ?? 
@Win_Tab
????.????? ?????
@Mr_MoRdaB

*/
$dir = explode('/',__DIR__);
define('LOCAL_DIR', str_replace(end($dir),'',__DIR__));
include(LOCAL_DIR.'config.php');
require('class.php');
$bot = new TelegramBot(token);
@$me = $bot->getMe();
$offset = -1;
while(true)
{
  $updates = $bot->get_updates(['offset'=> $offset, 'limit'=> 50 , 'timeout'=> 0]);
  if(isset($updates['ok']) && $updates['ok'] == false) continue;
  $updates = $updates['result'];
  if(isset(end($updates)['update_id'])) $offset = end($updates)['update_id']+1;
  foreach($updates as $update)
  {

    include('source.php');
  }
}
