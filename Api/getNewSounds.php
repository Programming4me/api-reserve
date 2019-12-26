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
  $new = reset($mb->getNewsounds());

  $settings = $db->select('settings')->fetch_assoc();
  if(!empty($settings['channel']) && $new['id'] != $settings['last_id'])
  {

      $db->update('settings',['last_id'=> $new['id']],"id='1'");
      $music = $mb->getSoundInfo($new['href']);
      if($music['category'] == 'آلبوم')
      {
        $title = $music['title'];
        $artist = $music['artist'];
        $_title = $music['_title'];
        $_artist = $music['_artist'];
        $hash = create_hash(8);
        $caption = '🗣 '.$_artist.PHP_EOL.'📀 '.$_title.PHP_EOL.'💢 #'.str_replace(' ','',$_title).' #Album'.PHP_EOL.PHP_EOL.'👉 t.me/'.$me['username'].'/?start='.$hash;
        $src = $music['src'];
        $localSrc = $_title.'.jpg';
        $bot->download_to_file($localSrc,$src);
        $bot->sendPhoto(['chat_id'=> $settings['channel'],'photo'=> new CURLFile($localSrc),'caption'=> $caption]);
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
          $res = $bot->sendAudio(['chat_id'=> $settings['channel'],'audio'=> new CURLFile('out_put.mp3'),'title'=>  $single['_title'],'performer'=> $_artist,'caption'=> $caption,'parse_mode'=>'MarkDown']);
          $audio = $res['result']['audio'];
          $t = $single['title'].' '.$single['_title'];
          $file_id = $audio['file_id'];
          $title = $audio['title'];
          $duration = $audio['duration'];
          $file_size = $audio['file_size'];
          $db->insert('sounds',['hash'=> $_hash,'file_id'=> $file_id,'title'=> $single['_title'],'query'=>$t,'duration'=> $duration,'file_size'=> $file_size,'likes'=> 0,'dislikes'=> 0,'downloads'=> 0,'edit'=>0,'album_id'=> $hash]);
          $db->insert('latest',['hash'=> $_hash]);
          unlink($localAudio);
          unlink('output.mp3');
          unlink('out_put.mp3');
        }
        
        unlink($localAudio);
        unlink('output.mp3');
        unlink('out_put.mp3');
        unlink($localSrc);
      }
      elseif($music['category'] == 'آهنگ')
      {
        $title = $music['title'];
        $artist = $music['artist'];
        $_title = $music['_title'];
        $_title = explode('(',$_title.'(')[0];
        $_artist = $music['_artist'];
        $hash = create_hash();
        $caption = '🗣 '.$_artist.PHP_EOL.'📀 '.$_title.PHP_EOL.'💢 #'.str_replace(' ','',$_title).' #Music'.PHP_EOL.PHP_EOL.'👉 t.me/'.$me['username'].'/?start='.$hash;
        $src = $music['src'];
        $localSrc = $_title.'.jpg';
        $local128 = '128.mp3';
        $local320 = '320.mp3';
        $bot->download_to_file($localSrc,$src);
        $bot->sendPhoto(['chat_id'=>$settings['channel'],'photo'=> new CURLFile($localSrc),'caption'=> $caption]);
        $bot->download_to_file($local128,$music['128']);
        $meta_data = value_merge($meta_data,['artist'=> $artist,'album_artist'=> $artist,'album'=> $title,'title'=> $title]);
        editMusic($local128,'output.mp3',$meta_data);
        cutMp3('output.mp3','cut.mp3',0,40);
        mp3ToOgg('cut.mp3','output.ogg');
        $caption = '[🎵 جستجوی موزیک](https://t.me/'.$me['username'].'?start=search)';
        setCover('output.mp3',$localSrc,'out_put.mp3');
        $res = $bot->sendAudio(['chat_id'=> $settings['channel'],'audio'=> new CURLFile('output.mp3'),'title'=> $_title,'performer'=> $_artist,'caption'=> $caption,'parse_mode'=>'MarkDown']);
        $audio = $res['result']['audio'];
        $t = $title.' '.$_title.' '.$artist.' '.$_artist;
        $file_id = $audio['file_id'];
        $title = $audio['title'];
        $duration = $audio['duration'];
        $file_size = $audio['file_size'];
        $db->insert('sounds',['hash'=> $hash,'file_id'=> $file_id,'title'=> $_title,'query'=>$t,'duration'=> $duration,'file_size'=> $file_size,'likes'=> 0,'dislikes'=> 0,'downloads'=> 0,'edit'=>0,'album_id'=> 'none']);
        $db->insert('latest',['hash'=> $hash]);
        $bot->sendVoice(['chat_id'=> $settings['channel'],'voice'=> new CURLFile('output.ogg'),'duration'=> 40]);
        $hash = create_hash();
        unlink($localSrc);
        unlink($local128);
        unlink('out_put.mp3');
        unlink('output.mp3');
        unlink('output.ogg');
        unlink('cut.mp3');
      }
      elseif($music['category'] == 'ریمیکس')
      {
        $title = $music['title'];
        $artist = $music['artist'];
        $_title = $music['_title'];
        $_title = explode('(',$_title.'(')[0];
        $_artist = $music['_artist'];
        $hash = create_hash();
        $caption = '🗣 '.$_artist.PHP_EOL.'📀 '.$_title.PHP_EOL.'💢 #'.str_replace(' ','',$_title).' #Music'.PHP_EOL.PHP_EOL.'👉 t.me/'.$me['username'].'/?start='.$hash;
        $src = $music['src'];
        $localSrc = $_title.'.jpg';
        $local128 = '128.mp3';
        $local320 = '320.mp3';
        $bot->download_to_file($localSrc,$src);
        $bot->sendPhoto(['chat_id'=> $settings['channel'],'photo'=> new CURLFile($localSrc),'caption'=> $caption]);
        $bot->download_to_file($local128,$music['128']);
        $meta_data = value_merge($meta_data,['artist'=> $artist,'album_artist'=> $artist,'album'=> $title,'title'=> $title]);
        editMusic($local128,'output.mp3',$meta_data);
        cutMp3('output.mp3','cut.mp3',0,40);
        mp3ToOgg('cut.mp3','output.ogg');
        $caption = '[🎵 جستجوی موزیک](https://t.me/'.$me['username'].'?start=search)';
        setCover('output.mp3',$localSrc,'out_put.mp3');
        $res = $bot->sendAudio(['chat_id'=> $settings['channel'],'audio'=> new CURLFile('out_put.mp3'),'title'=> $_title,'performer'=> $_artist,'caption'=> $caption,'parse_mode'=>'MarkDown']);
        $audio = $res['result']['audio'];
        $t = $title.' '.$_title.' '.$artist.' '.$_artist;
        $file_id = $audio['file_id'];
        $title = $audio['title'];
        $duration = $audio['duration'];
        $file_size = $audio['file_size'];
        $db->insert('sounds',['hash'=> $hash,'file_id'=> $file_id,'title'=> $_title,'query'=>$t,'duration'=> $duration,'file_size'=> $file_size,'likes'=> 0,'dislikes'=> 0,'downloads'=> 0,'edit'=>0,'album_id'=> 'none']);
        $db->insert('latest',['hash'=> $hash]);
        $bot->sendVoice(['chat_id'=> $settings['channel'],'voice'=> new CURLFile('output.ogg'),'duration'=> 40]);
        $hash = create_hash();
        unlink($localSrc);
        unlink($local128);
        unlink('out_put.mp3');
        unlink('output.mp3');
        unlink('output.ogg');
        unlink('cut.mp3');
      }
      unset($music,$new);
  }
