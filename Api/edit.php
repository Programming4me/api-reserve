<?php
$dir = explode('/',__DIR__);
define('LOCAL_DIR', str_replace(end($dir),'',__DIR__));
include(LOCAL_DIR.'config.php');
require('class.php');
error_reporting(E_ALL);
require('uploader.php');
$bot = new TelegramBot(token);
$up = new uupload();
$me = $bot->getMe();
$username = isset($me['username'])?'@'. $me['username']:'';
$performer = '🆔 '.$username;
while(true)
{
  $db->delete('sounds',"file_id=''");
  $rows =$db->select('sounds',"edit='0'");
  if(!empty($rows))
  {
    foreach($rows as $row)
    {
      $id = $row['id'];
      $fileid = $row['file_id'];
      $url = $bot->getFileID($fileid);
      $title = $row['title'];
      $localFile = time().'.mp3';
      $duration = $row['duration'];
      if($bot->download_to_file($localFile , $url))
      {

        getAlbumArt($localFile,'cover.jpg');
        jpgToPng('cover.jpg','cover.png');
        if(file_exists('cover.png'))
        {
          $localCover = 'cover.png';
          $up->upload(['file'=>'cover.png']);
          $up->getLinks();
          $direct_link = $up->links['direct_link'];
          $res = $bot->sendPhoto(['chat_id'=> logs,'photo'=> new CURLFile('cover.png'),'caption'=> $direct_link]);
          $src = end($res['result']['photo'])['file_id'];
        }else {
          $localCover = '../default.png';
          $direct_link = 'http://uupload.ir/files/s850_photo_2019-01-25_16-13-55.jpg';
          $res = $bot->sendPhoto(['chat_id'=> logs,'photo'=> new CURLFile($localCover),'caption'=> $direct_link]);
          $src = end($res['result']['photo'])['file_id'];
        }
        $localMp3 = microtime(1).'.mp3';
        setCover($localFile,$localCover,$localMp3);
        $res = $bot->sendAudio(['chat_id'=> logs,'audio'=> new CURLFile($localMp3),'performer'=> $performer,'title'=> $title,'duration'=> $duration]);
        $audio_fileid = $res['result']['audio']['file_id'];
        get320Quality($localMp3,'output320.mp3');
        $res = $bot->sendAudio(['chat_id'=> logs,'audio'=> new CURLFile('output320.mp3'),'performer'=> $performer,'title'=> $title,'duration'=> $duration]);
        $audio_fileid320 = $res['result']['audio']['file_id'];
        cutMp3($localMp3,'cutted.mp3');
        mp3ToOgg('cutted.mp3','cover.ogg');
        $res = $bot->sendVoice(['chat_id'=> logs,'voice'=> new CURLFile('cover.ogg')]);
        $voice_fileid = $res['result']['voice']['file_id'];
  
      $db->update('sounds',['file_id'=> $audio_fileid,'cover'=>$voice_fileid,'src_url'=> $direct_link,'file_id320'=>$audio_fileid320,'src'=> $src ,'edit'=> 1],"id='$id'");
      }else{
        $db->delete('sounds',"id='$id'");
      }
      if(file_exists($localFile)) unlink($localFile);
      @$files = array_merge(glob('*.mp3'),glob('*.ogg'));
      @$files = array_merge($files,glob('*.jpg'));
      @$files = array_merge($files,glob('*.png'));
      foreach(@$files as $file)
      {
        unlink($file);
      }
    }
  }
  sleep(8);
}
