<?php
/*
???? ??? ?? ????? ??? ?? 
@Win_Tab
????.????? ?????
@Mr_MoRdaB

*/
class TelegramBot
{
  public $token,$chatid,$fromid,$messageid,$api_url ='https://api.telegram.org/';
  public function __construct($token)
  {
    $this->token = $token;
  }
  public function makeRequest(string $method='',array $args=[])
  {
    $url = $this->api_url.'bot'.$this->token.'/'. $method;
    $ch = curl_init($url);
    curl_setopt_array($ch ,[CURLOPT_POST => true,CURLOPT_POSTFIELDS => $args,CURLOPT_RETURNTRANSFER=> true]);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result , true);
  }
  public function get_updates(array $args)
  {
    return $this->makeRequest('getUpdates', $args);
  }
  public function sendMessage(array $args)
  {
    return $this->makeRequest('sendMessage', $args);
  }
  public function editMessageText(array $args=[])
  {
    return $this->makeRequest('editMessageText', $args);
  }
  public function sendAudio(array $args)
  {
    return $this->makeRequest('sendAudio', $args);
  }
  public function sendVoice(array $args)
  {
    return $this->makeRequest('sendVoice', $args);
  }
  public function sendPhoto(array $args)
  {
    return $this->makeRequest('sendPhoto', $args);
  }
  public function sendDocument(array $args)
  {
    return $this->makeRequest('sendDocument', $args);
  }
  public function answerCallbackQuery(array $args)
  {
    return $this->makeRequest('answerCallbackQuery', $args);
  }
  public function editMessageReplyMarkup(array $args=[])
  {
    return $this->makeRequest('editMessageReplyMarkup',$args);
  }
  public function answerInlineQuery(array $args=[])
  {
    return $this->makeRequest('answerInlineQuery',$args);
  }
  public function getMe()
  {
    return $this->makeRequest('getMe')['result'];
  }
  public function getFile($fileid)
  {
    return  $this->makeRequest('getFile',['file_id'=> $fileid]);

  }
  public function getChatMember(array $args=[])
  {
    return $this->makeRequest('getChatMember',$args);
  }
  public function getChannelLink(string $channel='')
  {
    $getChat = $this->makeRequest('getChat',['chat_id'=> $channel]);
    if($getChat['ok'] == false) return;
    @ $invite_link = $getChat['result']['invite_link'];
    if(empty($invite_link))
    {
      $getChat = @$this->makeRequest('exportChatInviteLink',['chat_id'=> $channel]);
      if(empty($getChat['result'])) return;
      $invite_link = $getChat['result'];
    }
    return $invite_link;
  }
  public function getFileID(string $fileid)
  {
    $path = $this->getFile($fileid)['result']['file_path'];
    if(empty($path)) return;
    return $this->api_url .'file/bot' . $this->token .'/'. $path;
  }
  public function download_to_file(string $path='',$url='')
  {
    $ch = curl_init();
    curl_setopt_array($ch , [CURLOPT_URL => $url,CURLOPT_VERBOSE=>true,CURLOPT_RETURNTRANSFER=>true,CURLOPT_AUTOREFERER=>false,CURLOPT_REFERER=> $this->api_url,CURLOPT_HTTP_VERSION=>CURL_HTTP_VERSION_1_1,CURLOPT_HEADER=> 0]);
    $data = curl_exec($ch);
    curl_close($ch);
    $fp = fopen($path,'w');
    $fw = fwrite($fp , $data);
    fclose($fp);
    return $fw;
  }
}
function makeKeyboard(array $array=[],$resize_keyboard= true,$one_time_keyboard=false)
{
  if(empty($array)) return null;
  $keyboard = [];
  foreach($array as $obj)
  {
    $key = [];
    foreach($obj as $row)
    {
      $type = isset($row[1]) ? $row[1]:1;
      if($type ==1) $key[] = ['text'=> $row[0]];
      if($type ==2) $key[] = ['text'=> $row[0],'request_contact'=> true];
      if($type ==3) $key[] = ['text'=> $row[0],'request_location'=> true];
    }
    $keyboard[] = $key;
  }
  return json_encode(['keyboard'=> $keyboard,'one_time_keyboard'=> $one_time_keyboard,'resize_keyboard'=> $resize_keyboard]);
}
function makeInlineKeyboard(array $array=[],$resize_keyboard= true)
{
  if(empty($array)) return null;
  $inline_keyboard = [];
  foreach($array as $obj)
  {
    $key = [];
    foreach($obj as $row)
    {
      $type = isset($row[2]) ? $row[2]:1;
      if($type ==1) $key[] = ['text'=> $row[0],'callback_data'=> $row[1]];
      if($type ==2) $key[] = ['text'=> $row[0],'url'=> $row[1]];
      if($type ==3) $key[] = ['text'=> $row[0],'switch_inline_query'=> $row[1]];
      if($type ==4) $key[] = ['text'=> $row[0],'switch_inline_query_current_chat'=> $row[1]];
    }
    $inline_keyboard[] = $key;
  }
  return json_encode(['inline_keyboard'=> $inline_keyboard,'resize_keyboard'=> $resize_keyboard]);
}
function editMusic(string $file_path='',string $output='',array $args=[])
{
  if(!file_exists($file_path)) return;
  $cmd = 'ffmpeg -i '.$file_path.' -c:a copy ';
   foreach($args as $key => $value)
   {
     $cmd .= "-metadata '$key=$value' ";
   }
   $cmd .= '-y '.$output;
  exec($cmd);

}
function setCover(string $file_name='',string $cover='',string $output='')
{
  if(!file_exists($file_name) || !file_exists($cover)) return;
  $cmd = "ffmpeg -i '$file_name' -i '$cover'  -map 0:0 -map 1:0 -codec copy -id3v2_version 3 -metadata:s:v title='Album cover' -metadata:s:v comment='Cover (front)' '$output'";
  exec($cmd);
}
function mp3ToOgg(string $mp3_file='',string $output='')
{
  if(!file_exists($mp3_file)) return;
  $cmd = "ffmpeg -i '$mp3_file' -acodec libopus -b:a 64k -vbr off -compression_level 10  '$output'";
  exec($cmd);
}
function cutMp3(string $mp3_file='',string $output='',int $start=0, int $length=30)
{
  if(!file_exists($mp3_file)) return;
  $cmd = "ffmpeg -i '$mp3_file'  -vn -ar 44100 -ac 2 -ab 192k -f mp3 -ss $start -t $length '$output'";
  exec($cmd);
}
function is_member()
{
  global $fromid,$settings,$is_sudo,$bot;
  if($is_sudo) return;
  if(empty($settings['channel'])) return;
    if($settings['join_ch'] == 0) return;
  $status = $bot->makeRequest('getChatMember',['chat_id'=> $settings['channel'],'user_id'=> $fromid])['result']['status'];
  return $status=='left';
}
function removeKeyboard()
{
  return json_encode(['remove_keyboard'=> true]);
}
function get320Quality(string $filename,string $output320)
{
  if(file_exists($filename)){
  $cmd = "ffmpeg -i '$filename' -ab 320k -f mp3 '$output320'";
  exec($cmd);
  }
}
function getAlbumArt($filename,$coverpath)
{
  if(file_exists($filename)){
  $cmd = "ffmpeg -i '$filename' -an -vcodec copy '$coverpath'";
  exec($cmd);
  }
}
function jpgToPng($sourceImage,$png_path)
{
  if(file_exists($sourceImage)){
      $cmd = "ffmpeg -i '$sourceImage' -qscale:v 2 '$png_path'";
      exec($cmd);
  }
}
