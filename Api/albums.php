<?php
$dir = explode('/',__DIR__);
define('LOCAL_DIR', str_replace(end($dir),'',__DIR__));
include(LOCAL_DIR.'config.php');
require('musicberooz.php');
require('class.php');
$bot = new TelegramBot(token);
$mb = new musicBerooz();
@$me = $bot->getMe();
error_reporting(E_ALL);
  $albums = ["https://musicberooz.ir/%D8%AF%D8%A7%D9%86%D9%84%D9%88%D8%AF-%D8%A2%D9%84%D8%A8%D9%88%D9%85-%D8%AC%D8%AF%DB%8C%D8%AF-%D8%A7%D8%AD%D8%B3%D8%A7%D9%86-%D8%AE%D9%88%D8%A7%D8%AC%D9%87-%D8%A7%D9%85%DB%8C%D8%B1%DB%8C-%D8%B4%D9%87/","https://musicberooz.ir/%D8%AF%D8%A7%D9%86%D9%84%D9%88%D8%AF-%D8%A2%D9%84%D8%A8%D9%88%D9%85-%D8%AC%D8%AF%DB%8C%D8%AF-%D8%B9%D8%B1%D9%81%D8%A7%D9%86-%D8%A2%DB%8C%D9%87-%D8%A7%D9%84%D8%AA%D8%B1%D9%BE/","https://musicberooz.ir/%D8%AF%D8%A7%D9%86%D9%84%D9%88%D8%AF-%D8%A2%D9%84%D8%A8%D9%88%D9%85-%D8%AC%D8%AF%DB%8C%D8%AF-%DA%AF%D8%B1%D9%88%D9%87-%D9%BE%D8%B1%D9%88%D8%A7%D8%B2-%D8%A2%D9%85%D8%A7%D8%AC/","https://musicberooz.ir/%D8%AF%D8%A7%D9%86%D9%84%D9%88%D8%AF-%D8%A2%D9%84%D8%A8%D9%88%D9%85-%D8%B1%D8%AD%DB%8C%D9%85-%D8%B4%D9%87%D8%B1%DB%8C%D8%A7%D8%B1%DB%8C-%D8%A2%D8%B0%D8%B1%D8%A8%D8%A7%DB%8C%D8%AC%D8%A7%D9%86/","https://musicberooz.ir/%D8%AF%D8%A7%D9%86%D9%84%D9%88%D8%AF-%D8%A2%D9%84%D8%A8%D9%88%D9%85-%D8%AC%D8%AF%DB%8C%D8%AF-%D8%AF%D9%86%DB%8C%D8%A7-%D8%A8%D9%87-%D9%86%D8%A7%D9%85-%D8%AF%D9%86%DB%8C%D8%A7/","https://musicberooz.ir/%D8%AF%D8%A7%D9%86%D9%84%D9%88%D8%AF-%D8%A2%D9%84%D8%A8%D9%88%D9%85-%D8%AC%D8%AF%DB%8C%D8%AF-%D9%BE%D8%AF%DB%8C-%D8%A2%DB%8C-%D9%BE%D8%B4%D8%AA-%D9%BE%D8%B1%D8%AF%D9%87/","https://musicberooz.ir/%D8%AF%D8%A7%D9%86%D9%84%D9%88%D8%AF-%D8%A2%D9%84%D8%A8%D9%88%D9%85-%D9%85%D8%AA%DB%8C%D9%86-%D8%AF%D9%88-%D8%AD%D9%86%D8%AC%D8%B1%D9%87-%D9%87%DB%8C%D8%B3-%D8%AE%D9%81%D9%87-%D8%A2%D8%B1%D9%88%D9%85/","https://musicberooz.ir/%D8%AF%D8%A7%D9%86%D9%84%D9%88%D8%AF-%D8%A2%D9%84%D8%A8%D9%88%D9%85-%D8%AC%D8%AF%DB%8C%D8%AF-%D8%AC%DB%8C-%D9%84%DB%8C-%D8%B3%DB%8C%D8%AC-%D8%B3%D8%AA%D8%A7%D8%B1%D9%87-%D8%A8%D8%A7%D8%B2%DB%8C/","https://musicberooz.ir/%D8%AF%D8%A7%D9%86%D9%84%D9%88%D8%AF-%D8%A2%D9%84%D8%A8%D9%88%D9%85-%D8%AC%D8%AF%DB%8C%D8%AF-%D8%B3%D8%A7%D9%85%DB%8C-%D8%A8%DB%8C%DA%AF%DB%8C-%D9%BE%D8%A7%D8%AF%D8%B4%D8%A7%D9%87/","https://musicberooz.ir/%D8%AF%D8%A7%D9%86%D9%84%D9%88%D8%AF-%D8%A2%D9%84%D8%A8%D9%88%D9%85-%D8%B1%D9%88%D8%B2%D8%A8%D9%87-%D8%A8%D9%85%D8%A7%D9%86%DB%8C-%DA%A9%D8%AC%D8%A7-%D8%A8%D8%A7%DB%8C%D8%AF-%D8%A8%D8%B1%D9%85/","https://musicberooz.ir/%D8%AF%D8%A7%D9%86%D9%84%D9%88%D8%AF-%D8%A2%D9%84%D8%A8%D9%88%D9%85-%D9%87%D9%85%D8%A7%DB%8C%D9%88%D9%86-%D8%B4%D8%AC%D8%B1%DB%8C%D8%A7%D9%86-%D8%A7%DB%8C%D8%B1%D8%A7%D9%86-%D9%85%D9%86/","https://musicberooz.ir/%D8%AF%D8%A7%D9%86%D9%84%D9%88%D8%AF-%D8%A2%D9%84%D8%A8%D9%88%D9%85-%D8%B9%D9%84%DB%8C-%D8%B9%D8%A8%D8%AF%D8%A7%D9%84%D9%85%D8%A7%D9%84%DA%A9%DB%8C-%D8%AE%D8%AF%D8%A7-%D9%86%D8%B4%D9%86%D8%A7%D8%B3/","https://musicberooz.ir/%D8%AF%D8%A7%D9%86%D9%84%D9%88%D8%AF-%D8%A2%D9%84%D8%A8%D9%88%D9%85-%D8%B3%DB%8C%D9%86%D8%A7-%D8%B3%D8%B1%D9%84%DA%A9-%D9%85%D8%AC%D9%86%D9%88%D9%86-%D8%B2%D9%85%D8%A7%D9%86%D9%87/","https://musicberooz.ir/%D8%AF%D8%A7%D9%86%D9%84%D9%88%D8%AF-%D8%A2%D9%84%D8%A8%D9%88%D9%85-%D8%AC%D8%AF%DB%8C%D8%AF-%D8%A7%D8%AF%DB%8C-%D8%B9%D8%B7%D8%A7%D8%B1-%D9%87%D9%85%D9%87-%D9%85%DB%8C%D8%AF%D9%88%D9%86%D9%86/","https://musicberooz.ir/%D8%A2%D9%84%D8%A8%D9%88%D9%85-%D8%AF%D8%A7%D9%84-%D8%A8%D9%86%D8%AF-%D9%BE%DB%8C%D8%A7%D8%AF%D9%87-%D8%B1%D9%88%DB%8C-%D8%A7%D8%B1%D8%AF%DB%8C%D8%A8%D9%87%D8%B4%D8%AA/","https://musicberooz.ir/%D8%AF%D8%A7%D9%86%D9%84%D9%88%D8%AF-%D8%A2%D9%84%D8%A8%D9%88%D9%85-%D8%AC%D8%AF%DB%8C%D8%AF-%D8%A2%D8%B1%D9%85%DB%8C%D9%86-%D9%86%D8%B5%D8%B1%D8%AA%DB%8C-%D8%B3%D8%B1%DA%A9%D8%A7%D8%B1%DB%8C/","https://musicberooz.ir/%D8%AF%D8%A7%D9%86%D9%84%D9%88%D8%AF-%D8%A2%D9%84%D8%A8%D9%88%D9%85-%D8%AC%D8%AF%DB%8C%D8%AF-%D8%B3%D8%A7%D9%84%D8%A7%D8%B1-%D8%B9%D9%82%DB%8C%D9%84%DB%8C-%D8%B5%D9%88%D8%B1%D8%AA%DA%AF%D8%B1/","https://musicberooz.ir/%D8%A2%D9%84%D8%A8%D9%88%D9%85-%D8%AC%D8%AF%DB%8C%D8%AF-%D8%A7%D9%85%DB%8C%D9%86-%DB%8C%D8%B2%D8%AF%D8%A7%D9%86%DB%8C-%D8%A2%D8%B1%D8%B2%D9%88%D9%87%D8%A7%DB%8C-%D8%B4%D8%B9%D8%A7%D8%B1%DB%8C/","https://musicberooz.ir/%D8%AF%D8%A7%D9%86%D9%84%D9%88%D8%AF-%D8%A2%D9%84%D8%A8%D9%88%D9%85-%D8%AC%D8%AF%DB%8C%D8%AF-%DA%A9%DB%8C%D8%A7%D9%86-%D9%BE%D9%88%D8%B1%D8%AA%D8%B1%D8%A7%D8%A8-%D8%A7%DB%8C%D9%86-%D8%B3%D9%85%D8%AA/"]; 
$settings = $db->select('settings')->fetch_assoc();
  if(logs)
  {
      foreach($albums as $album){

          $db->update('settings',['last_id'=> $new['id']],"id='1'");
          $music = $mb->getSoundInfo($album);
          if($music['category'] == 'آلبوم')
          {
            $title = explode('(',$music['title'])[0];
            $artist = explode('(',$music['artist'])[0];
            $_title = explode('(',$music['_title'])[0];
            $_artist = explode('(',$music['_artist'])[0];
            $hash = create_hash(8);
            $caption = '🗣 '.$_artist.PHP_EOL.'📀 '.$_title.PHP_EOL.'💢 #'.str_replace(' ','',$_title).' #Album'.PHP_EOL.PHP_EOL.'👉 t.me/'.$me['username'].'/?start='.$hash;
            $src = $music['src'];
            $localSrc = $_title.'.jpg';
            $bot->download_to_file($localSrc,$src);
            $bot->sendPhoto(['chat_id'=> logs,'photo'=> new CURLFile($localSrc),'caption'=> $caption]);
            $db->insert('albums',['title'=> $title,'artist'=> $artist,'_title'=> $_artist,'_artist'=> $_artist,'hash'=> $hash]);
            foreach(@$music['single128'] as $single)
            {
              $_hash = create_hash();
              $localAudio = time().'.mp3';
              $localMP3 = $single['_title'].'.mp3';
              $bot->download_to_file($localAudio,$single['url']);
              $m_data = value_merge($meta_data,['artist'=> $artist,'album_artist'=> $artist,'album'=> $title,'title'=> $single['_title']]);
              editMusic($localAudio,'output.mp3',$m_data);
              setCover('output.mp3',$localSrc,'out_put.mp3');
              $caption = '[🎵 جستجوی موزیک](https://t.me/'.$me['username'].'?start=search)';
              $res = $bot->sendAudio(['chat_id'=> logs,'audio'=> new CURLFile('out_put.mp3'),'title'=>  explode('(',$single['_title'])[0],'performer'=> $_artist,'caption'=> $caption,'parse_mode'=>'MarkDown']);
              $audio = $res['result']['audio'];
              $t = $single['title'].' '.explode('(',$single['_title'])[0].' '.$_artist;
              $file_id = $audio['file_id'];
              $title = $audio['title'];
              $duration = $audio['duration'];
              $file_size = $audio['file_size'];
              $db->insert('sounds',['hash'=> $_hash,'file_id'=> $file_id,'title'=> explode('(',$single['_title'])[0],'query'=>$t,'duration'=> $duration,'file_size'=> $file_size,'likes'=> 0,'dislikes'=> 0,'downloads'=> 0,'edit'=>0,'album_id'=> $hash]);
              $db->insert('latest',['hash'=> $_hash]);
              unlink($localAudio);
              unlink('output.mp3');
              unlink('out_put.mp3');
                sleep(3);
            }

            unlink($localAudio);
            unlink('output.mp3');
            unlink('out_put.mp3');
            unlink($localSrc);
              sleep(10);
          }
      }
     
  }
