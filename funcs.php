<?php
/*
اوپن شده در کانال وین تب 
@Win_Tab
اوپن.کننده مزداب
@Mr_MoRdaB

*/
function create_hash($length=7)
{
  $_hash = array_merge(range('A','Z'),range('a','z'));
  $x = 1;
  $hash = '';
  while($x<=$length)
  {
    $z = $_hash[array_rand($_hash)];
    $hash .= $z;
    $x++;
  }
  return $hash;
}
function step(string $step='')
{
  global $db,$fromid;
  $db->updateStep($fromid,$step);
}
function getMethod()
{
  global $update;
  $array = ['photo','text','video','audio','voice','document','video_note','sticker'];
  $obj = array_values(array_intersect($array,array_keys($update['message'])));
  if(empty($array)) return false;
  $method = false;
  switch(end($obj))
  {
    case 'photo':
      $method = 'sendPhoto';
      break;
    case 'text':
      $method = 'sendMessage';
      break;
    case 'video':
      $method = 'sendVideo';
      break;
    case 'audio':
      $method ='sendAudio';
      break;
    case 'voice':
      $method ='sendVoice';
      break;
    case 'document';
      $method = 'sendDocument';
      break;
    case 'video_note':
      $method = 'sendVideoNote';
      break;
    case 'sticker';
      $method = 'sendSticker';
      break;

  }
  return ['method'=>$method,'type'=>end($obj)];
}
function config()
{
  global $db;
  $res = $db->select('settings',"id='1'");
  if($db->row_exists($res)) return true;
  return $db->insert('settings',['channel'=> '','auto_ads'=> 0,'send'=>0,'sent_offset'=> 0]);
}
function checkChannel($channel)
{
  global $me,$bot;
  @$status = $bot->getChatMember(['chat_id'=> $channel,'user_id'=> $me['id']])['result']['status'];
  return $status;

}
function menukey()
{
  global $db;
  $settings = $db->select('settings')->fetch_assoc();
  $join_ch =  str_replace([1,0],['✅','❌'],(int )$settings['join_ch']);
  $auto_ads = str_replace([1,0],['✅','❌'],(int) $settings['auto_ads']);
  return makeKeyboard([
    [['مشاهده آمار و وضعیت ربات']],[['تنظیم کانال'],['عضویت در کانال ' . $join_ch]],
    [['ارسال تبلیغات'],['وضعیت تبلیغ خودکار  ' .$auto_ads],['ویرایش تبلیغات']],
    [['ارسال پیام به کاربران'],['فروارد پیام به کاربران']],
    [['منوی اصلی']]
  ]);
}
function value_merge(array $array,array $_array)
{

    foreach($_array as $key => $obj) if(array_key_exists($key,$array)) $array[$key] .= $obj;
    return $array;
}
function p2f($string)
{
    $ch = curl_init('http://syavash.com/portal/modules/pinglish2farsi/convertor.php?lang=fa');
    curl_setopt_array
    ($ch ,
        [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => ['action'=> 'convert', 'pinglish'=>$string],
            CURLOPT_RETURNTRANSFER => true
        ]
    );
    return curl_exec($ch);
}