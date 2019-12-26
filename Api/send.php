<?php
$dir = explode('/',__DIR__);
define('LOCAL_DIR', str_replace(end($dir),'',__DIR__));
include(LOCAL_DIR.'config.php');
require('class.php');
$bot = new TelegramBot(token);
while(true)
{
  $msg = $db->getFirstMessage();
  if($msg && !empty($msg))
  {
    $args = [];
    @ $args['sticker'] = $args['video'] = $args['voice'] = $args['photo'] = $args['audio'] = $args['document'] = $args['video_note'] = $msg['file_id'];
    @ $args['caption'] = $args['text'] = $msg['message'];
    @ $args['from_chat_id'] = $msg['chat_id'];
    @ $args['message_id'] = $msg['message_id'];
    $id = $msg['id'];
    $users = array_chunk($db->select('users','','user_id')->fetch_all(),100);
    foreach($users as $members)
    {
      foreach($members as $user)
      {
        $args['chat_id'] = $user[0];
        $bot->makeRequest($msg['method'],$args);
        unset($args['chat_id']);
      }
      sleep(30);
    }
    $db->delete('sendmsg',"id='$id'");
  }else{
    sleep(100);
  }
}
