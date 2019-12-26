<?php
/*
اوپن شده در کانال وین تب 
@Win_Tab
اوپن.کننده مزداب
@Mr_MoRdaB

*/
//error_reporting(E_ALL);
if(isset($update['callback_query']))
{
  $callback = $update['callback_query'];
  @$message = $callback['message'];
  $from = $callback['from'];
  $data = $callback['data'];
  $callback_id = $callback['id'];
  $chat = $message['chat'];
  $messageid = $message['message_id'];
  $fromid = $from['id'];
  $firstname = $from['first_name'];
  $lastname = isset($from['last_name']) ? $from['last_name']:'';
  $username = isset($from['username']) ? '@'. $from['username']:'';
  $chatid = $chat['id'];
  unset($text);
}
if(isset($update['inline_query']))
{
  $inlinequery = $update['inline_query'];
  $query = $inlinequery['query'];
  $inlinequery_id = $inlinequery['id'];
  $fromid = $inlinequery['from']['id'];
  unset($text);
  unset($chatid);
}
if(isset($update['message']))
{
  $message = $update['message'];
  $from = $message['from'];
  $text = $message['text'];
  $chat = $message['chat'];
  $messageid = $message['message_id'];
  $fromid = $from['id'];
  $firstname = $from['first_name'];
  $lastname = isset($from['last_name']) ? $from['last_name']:'';
  $username = isset($from['username']) ? '@'. $from['username']:'';
  $chatid = $chat['id'];
  unset($data);
}
$menu = menukey();
$main_keyboard = makeKeyboard([
  [['📱 منوی اصلی'],['جستجوی موزیک🎶']],

]);

$sample_key = makeKeyboard([
  [['جستجوی موزیک🎶'],['🌟 پر بازدیدترین']],
  [['🔈 آلبوم های جدید'],['🎼آهنگ های جدید']],
  [['📥دانلودهای من'],['💌 لایک های من']],
  [['💜 لیست مورد علاقه من']],
 [[ '✖️خروج از منو']]
]);
$is_sudo = in_array($fromid,$sudo);
if($is_sudo) $main_keyboard = makeKeyboard([
  [['📱 منوی اصلی'],['جستجوی موزیک🎶']],
  [[ 'منوی مدیریت']]
]);
if($is_sudo)
{

  $sample_key = makeKeyboard([
      [['جستجوی موزیک🎶'],['🌟 پر بازدیدترین']],
      [['🔈 آلبوم های جدید'],['🎼آهنگ های جدید']],
      [['📥دانلودهای من'],['💌 لایک های من']],
      [['💜 لیست مورد علاقه من']],
      [[ '✖️خروج از منو']],
      [['منوی مدیریت']]
  ]);

}
$settings = $db->select('settings')->fetch_assoc();
$udb = $db->user($fromid);
if(isset($update['message']['audio']))
{
  $audio = $update['message']['audio'];
  if(!empty($audio)){
    $hash = create_hash();
    $file_id = $audio['file_id'];
    $title = preg_replace('/@(.*)/','music',$audio['title']);
    $duration = $audio['duration'];
    $file_size = $audio['file_size'];
    $performer = preg_replace('/@(.*)/','',$audio['performer']);
    $query = $title.' '.$performer . strip_tags(p2f($title.' '.$performer));
    $res = $db->select('sounds',"title='$title' AND duration='$duration' AND file_size='$file_size'");
    if(!$db->row_exists($res)){
      $db->insert('sounds',['hash'=> $hash,'file_id'=> $file_id,'title'=> $title,'duration'=> $duration,'file_size'=> $file_size,'query'=> $query,'likes'=> 0,'dislikes'=> 0,'downloads'=> 0,'edit'=>0,'album_id'=> 'none']);
      if($chatid != logs && $chat['type'] == 'private')
      {
        $answer = '🙏 با تشکر، آهنگ شما دریافت شد.'.PHP_EOL.'فایل آهنگ بر اساس کیفیت اطلاعات آن امتیاز بندی شده و به نتایج جستجو اضافه خواهد شد.';
        $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid, 'text'=> $answer,'reply_markup'=> $main_keyboard]);
      }
    }
  }

}
if(@$chatid == logs) return;
else  if(preg_match('/^\/start\s?(inline|search|new)?$/i',$text) || $text == 'منوی اصلی' || $text == '🔙 برگشت')
  {
    step();
    $keyboard = $main_keyboard;
    $db->insertUser($fromid);
    $answer = 'سلام '.$firstname.PHP_EOL.'به بزرگ‌ترین آرشیو 🎵موزیک در تلگرام خوش آمدی;)'.PHP_EOL.PHP_EOL.'🔎 چطوری موزیک دانلود کنم؟:'.PHP_EOL.'برای پیدا کردن موزیک مورد نظرت می تونی از روش‌های زیر استفاده کنی:'.PHP_EOL.PHP_EOL.'`⌨️ جستجوی متنی:`'.PHP_EOL.'اسم موزیک، خواننده، یا قسمتی از متن ترانه را برام بفرست.'.PHP_EOL.PHP_EOL.'`🔊 جستجو بصورت اینلاین`'.PHP_EOL.'ایدی ربات رو توی گروه‌هات یا پی‌وی دوستات تایپ کن و سپس با یه فاصله اسم اون آهنگی رو که می‌خوای رو بنویس.';
    $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid, 'text'=> $answer,'parse_mode'=> 'markdown','reply_markup'=> $keyboard]);
  }
  elseif(preg_match('/^\/start (.*)$/i',$text,$hash))
  {
    $hash = $hash[1];
    if(strlen($hash) == 7)
    {
      $res = $db->getMp3Info($hash);
      if($res)
      {
        $file_id = $res['file_id'];
        $likes = $res['likes'];
        $dislikes = $res['dislikes'];
        $hash = $res['hash'];
        $favorites = '➕ افزودن به لیست مورد علاقه';
        if($db->isMp3ExistsFavorites($hash,$fromid)) $favorites = '➖ حذف از لیست مورد علاقه';
        $keyboard = makeInlineKeyboard([
          [['❤ ('.$likes.')','like_'.$hash],['💔 ('.$dislikes.')','dislike_'.$hash]],
          [[$favorites,'favorites_'.$hash]],
          [['🔗 اشتراک گذاری', '#dl_'.$hash,3]],
        ]);
        $row = $db->getFirstAds();
        if($row && @$settings['auto_ads'] == true && @$udb['last_ads_id'] != $row['id'] && time() > @$udb['last_ads_time'])
        {
          $args =[];
          @$args['photo'] = $args['video']  = $args['voice'] = $args['audio']  = $args['document']  = $args['sticker'] = $args['video_note'] = $row['file_id'];
          @$args['text'] = $row['message'];
          @$args['caption'] = $row['message'];
          @$args['chat_id'] = $chatid;
          @$method = $row['method'];
          $bot->makeRequest($method,$args);
          $id = $row['id'];
          $db->update('ads',['users_count'=> $row['users_count'] +1],"id='$id'");
          $db->update('users',['last_ads_id'=> $id,'last_ads_time'=> time()],"user_id='$fromid'");
          $row = $db->getFirstAds();

          if($row['users_count'] >= $row['sent_count'])
          {
            $args['chat_id'] = logs;
            unset($args['reply_to_message_id']);
            $msgid = $bot->makeRequest($method,$args)['result']['message_id'];
            $answer = 'تبلیغ مورد نظر برای '.$row['users_count'].' کاربر ارسال شد.';
            $bot->sendMessage(['chat_id'=> logs, 'reply_to_message_id'=> $msgid,'text'=> $answer]);
            $db->delete('ads',"id='$id'");
          }
        }
        $caption = '[🎵 جستجوی موزیک](https://t.me/'.$me['username'].'?start=search)';
        $bot->sendAudio(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid, 'audio'=> $file_id,'reply_markup'=> $keyboard,'caption'=> $caption,'parse_mode'=>'MarkDown']);
        $db->addToDownloadList($hash , $fromid);
        $db->addMp3DownloadCount($hash);
      }else{
        $answer = 'موزیک موردنظر یافت نشد';
        $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid,'text'=> $answer]);
      }
    }
    elseif(strlen($hash) == 8)
    {
      $musics = $db->getAlbumMusics($hash);
      if($musics)
      {
        $album = $db->getAlbumInfo($hash);
        $answer = '✅ شما آلبوم "'.$album['title'].'" با صدای هنرمند "'.$album['artist'].'" را انتخاب نمودید.'.PHP_EOL.' 🔸 این آلبوم '.count($musics).' ترک دارد،شما می‌توانید آهنگ ‌های این آلبوم را در لیست زیر مشاهده کنید.'.PHP_EOL.PHP_EOL.'🔅 #'.str_replace(' ','',$album['_title']).' #'.explode('(',str_replace(' ','',$album['_artist']))[0];
        $key = [];
        $key[] = [['🔙 برگشت']];
        foreach($musics as $row)
        {
          $key[] = [['🎶 '.$album['_artist'].' -'.$row['title']]];
        }
        $keyboard = makeKeyboard($key);
        $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid,'text'=> $answer,'reply_markup'=> $keyboard]);
      }else {
        $answer = 'آلبوم مورد نظر یافت نشد';
        $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid,'text'=> $answer]);
      }

    }
  }
  elseif(is_member())
  {
    $answer = 'کاربر گرامی '.$firstname.' 🎶'.PHP_EOL.'استفاده از تمامی امکانات ربات کاملاً رایگان می‌باشد،امـّا برای آگاه شدن از اطلاعیه‌ها و آپدیت‌های ربات و همچنین دریافت جدیدترین موزیک ها می‌بایست در کانال پشتیبانی بات عضو شوید. '.PHP_EOL.'🔅 جهت عضویت در کانال برروی لینک عضویت و یا نام کاربری کانال کلیک کنید.'.PHP_EOL.PHP_EOL.'🆔 '.@$settings['channel'].PHP_EOL.PHP_EOL;
    if($invitelink = $bot->getChannelLink($settings['channel'])) $answer .= '🌐 '.$invitelink.PHP_EOL.PHP_EOL;
    $answer .= '📌 پس از عضویت <a href="https://t.me/'.$me['username'].'?start=new">"📥 اینجـــا"</a> کلیک کنید.';
    $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid, 'text'=> $answer,'parse_mode'=> 'markdown','reply_markup'=> removeKeyboard(),'disable_web_page_preview'=> true,'parse_mode'=> 'html']);
  }
  elseif($text == '📱 منوی اصلی' || preg_match('/^\/home/i',$text))
  {
    $keyboard = makeInlineKeyboard([
      [['📥 بیشترین دانلود','#md',4],['⌛️تاریخچه دانلود','#history',4]],
      [['🎶 جدیدترین آلبوم','#albums',4],['🎵جدیدترین اهنگ','#latest',4]],
      [['💜 لیست مورد علاقه','#favorites',4],['❤️ لایک های من','#mylikes',4]],
      [['🏚 منوی ساده','sm']],
      [['💰تبلیغات','#advertise',4],['➕ ثبت موزیک','#submit_music',4]]
    ]);
    $answer ='📱 منوی اصلی'.PHP_EOL.'ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ'.PHP_EOL.PHP_EOL.'🔎 چطوری آهنگی که می‌خوامو پیدا کنم؟'.PHP_EOL.'برای جستجوی موزیک،هرجای ربات که هستید عبارت موردنظرتون رو وارد کنید،نیازی به مراجعه به بخش خاصی نیست.'.PHP_EOL.PHP_EOL.'❤️ چطور می‌تونم موزیک‌هایی که لایک کردمو ببینم؟'.PHP_EOL.'برای استفاده از این بخش می‌تونید به بخش "❣️ مورد پسندهای من" بروید و یا هر جای تلگرام که هستید؛توی گروه‌ها،کانال‌ها و یا پی‌وی ها می‌تونید با تایپ کردن نام کاربری ربات و دستور <pre>#mylikes</pre> موسیقی‌هایی که اخیراً لایک کرده‌اید رو مشاهده و به اشتراک بگذارید.مثال:'.PHP_EOL.'<pre>@'.$me['username'].' #mylikes</pre>'.PHP_EOL.PHP_EOL;
    $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid, 'text'=> $answer,'parse_mode'=> 'html','reply_markup'=> $keyboard]);
  }
  elseif($text == '✖️خروج از منو')
  {
    $answer = '❌ منوی ساده غیرفعال شد.';
    $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid, 'text'=> $answer,'reply_markup'=> $main_keyboard]);
  }
  elseif($text == 'جستجوی موزیک🎶')
  {
    $answer = '🎶 برای جستجو توی ربات ،چند تا روش وجود داره که دوتا از روش‌هاشو بهت می‌گم :'.PHP_EOL.PHP_EOL.'🔅 روش جستجو بصورت متنی :'.PHP_EOL.'  ✍️ در این روش،شما یک متن صد کاراکتری؛شامل:متن‌آهنگ،اسم خواننده و یا اسم آهنگ ارسال می‌کنی و یه لیست مرتبط با عبارتی که جستجو کردی واست ارسال می‌شه.'.PHP_EOL.PHP_EOL.'🔅 روش جستجو در حالت اینلاین(درون خطی): '.PHP_EOL.'  ✍️ در این روش شما هرجا که هستی می‌تونی از طریق نوشتن آیدی ربات در  چت‌ها،گروه‌ها،کانال‌ها و یا پی‌وی دوستات و عبارتی که می‌خوای جستجو کنی لیستی از آهنگ های مرتبط با عبارت مورد نظرت داشته باشی.'.PHP_EOL.PHP_EOL.'🎵 پس منتظر چی هستی؟همین حالا هرچیزی که می‌خوای رو بفرست تا جستجو کنم و واست ارسال کنم.';
    $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid, 'text'=> mb_convert_encoding($answer,'UTF-8','auto'),'reply_markup'=> $main_keyboard]);
  }
  elseif($text == '🌟 پر بازدیدترین')
  {
    $downloads = $db->getMostDownloads();
    $answer = 'درحال حاضر هیچ موزیکی در بخش "'.$text.'" وجود نداره:(';
    $keyboard= $sample_key;
    if($downloads)
    {
      $rows = array_chunk($downloads,20);
      $answer = '🔅 در اینجا شما آهنگ‌هایی که بیشترین تعداد دانلود را داشته‌اند،به‌ترتیب مشاهده می‌کنید: '.PHP_EOL.PHP_EOL;
      if(!empty($rows[1]))
      {
        $keyboard = makeInlineKeyboard([
          [['➡️ صفحه‌ی بعدی','md-1']]
        ]);
      }
      foreach($rows[0] as $row)
      {
        $answer .= '🎵 نام : '.substr($row['title'],0,50).PHP_EOL.'🕒 زمان : '.date('i:s',$row['duration']).' دقیقه '.PHP_EOL.'📀حجم : '.round(($row['file_size']/1024)/1024,2).' مگابایت'.PHP_EOL.'📥 دانلود آهنگ : /dl_'.$row['hash'].PHP_EOL.'➖➖➖➖➖➖'.PHP_EOL;
      }
    }
    $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid, 'text'=> mb_convert_encoding($answer,'UTF-8','auto'),'reply_markup'=> $keyboard]);
  }
  elseif($text == '📥دانلودهای من')
  {
    $recently = $db->getRecentlyDownloads($fromid);
    $answer = 'درحال حاضر هیچ موزیکی در بخش "'.$text.'" وجود نداره:(';
    $keyboard=$sample_key;
    if($recently)
    {

      $rows = array_chunk($recently,20);
      $answer = '🔅 در اینجا شما آهنگ‌هایی که اخیراً دانلود کرده اید،به‌ترتیب مشاهده می‌کنید: '.PHP_EOL.PHP_EOL;
      if(!empty($rows[1]))
      {
        $keyboard = makeInlineKeyboard([
          [['➡️ صفحه‌ی بعدی','rd-1']]
        ]);
      }
      foreach($rows[0] as $row)
      {
        $answer .= '🎵 نام : '.substr($row['title'],0,50).PHP_EOL.'🕒 زمان : '.date('i:s',$row['duration']).' دقیقه '.PHP_EOL.'📀حجم : '.round(($row['file_size']/1024)/1024,2).' مگابایت'.PHP_EOL.'📥 دانلود آهنگ : /dl_'.$row['hash'].PHP_EOL.'➖➖➖➖➖➖'.PHP_EOL;
      }
    }
    $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid, 'text'=> mb_convert_encoding($answer,'UTF-8','auto'),'reply_markup'=> $keyboard]);
  }
  elseif($text == 'بیشترین ❤️ شده‌ها')
  {
    $likes = $db->getMostLiked();
    $answer = 'درحال حاضر هیچ موزیکی در بخش "'.$text.'" وجود نداره:(';
    $keyboard=$sample_key;
    if($likes)
    {

      $rows = array_chunk($likes,20);
      $answer = '🔅 در اینجا شما آهنگ‌هایی که بیشترین تعداد ❤️ داشته‌اند،به‌ترتیب مشاهده می‌کنید: '.PHP_EOL.PHP_EOL;
      if(!empty($rows[1]))
      {
        $keyboard = makeInlineKeyboard([
          [['➡️ صفحه‌ی بعدی','ml-1']]
        ]);
      }
      foreach($rows[0] as $row)
      {
        $answer .= '🎵 نام : '.substr($row['title'],0,50).PHP_EOL.'🕒 زمان : '.date('i:s',$row['duration']).' دقیقه '.PHP_EOL.'📀حجم : '.round(($row['file_size']/1024)/1024,2).' مگابایت'.PHP_EOL.'📥 دانلود آهنگ : /dl_'.$row['hash'].PHP_EOL.'➖➖➖➖➖➖'.PHP_EOL;
      }
    }
    $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid, 'text'=> mb_convert_encoding($answer,'UTF-8','auto'),'reply_markup'=> $keyboard]);
  }
  elseif($text == '💜 لیست مورد علاقه من')
  {
    $favorites = $db->getUserFavorites($fromid);
    $answer = 'درحال حاضر هیچ موزیکی در بخش "'.$text.'" وجود نداره:(';
    $keyboard=$sample_key;
    if($favorites)
    {

      $rows = array_chunk($favorites,20);
      $answer = '🔅 در اینجا شما آهنگ‌هایی که به لیست علاقه مندی اضافه کرده‌اید،به‌ترتیب مشاهده می‌کنید: '.PHP_EOL.PHP_EOL;
      if(!empty($rows[1]))
      {
        $keyboard = makeInlineKeyboard([
          [['➡️ صفحه‌ی بعدی','f-1']]
        ]);
      }
      foreach($rows[0] as $row)
      {
        $answer .= '🎵 نام : '.substr($row['title'],0,50).PHP_EOL.'🕒 زمان : '.date('i:s',$row['duration']).' دقیقه '.PHP_EOL.'📀حجم : '.round(($row['file_size']/1024)/1024,2).' مگابایت'.PHP_EOL.'📥 دانلود آهنگ : /dl_'.$row['hash'].PHP_EOL.'➖➖➖➖➖➖'.PHP_EOL;
      }
    }
    $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid, 'text'=> mb_convert_encoding($answer,'UTF-8','auto'),'reply_markup'=> $keyboard]);
  }
  elseif($text == '💌 لایک های من')
  {
    $liked = $db->getUserLiked($fromid);
    $answer = 'درحال حاضر هیچ موزیکی در بخش "'.$text.'" وجود نداره:(';
    $keyboard=$sample_key;
    if($liked)
    {

      $rows = array_chunk($liked,20);
      $answer = '🔅 در اینجا شما آهنگ‌هایی که اخیراً  پسندیده‌اید،به‌ترتیب مشاهده می‌کنید: '.PHP_EOL.PHP_EOL;
      if(!empty($rows[1]))
      {
        $keyboard = makeInlineKeyboard([
          [['➡️ صفحه‌ی بعدی','l-1']]
        ]);
      }
      foreach($rows[0] as $row)
      {
        $answer .= '🎵 نام : '.substr($row['title'],0,50).PHP_EOL.'🕒 زمان : '.date('i:s',$row['duration']).' دقیقه '.PHP_EOL.'📀حجم : '.round(($row['file_size']/1024)/1024,2).' مگابایت'.PHP_EOL.'📥 دانلود آهنگ : /dl_'.$row['hash'].PHP_EOL.'➖➖➖➖➖➖'.PHP_EOL;
      }
    }
    $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid, 'text'=> mb_convert_encoding($answer,'UTF-8','auto'),'reply_markup'=> $keyboard]);
  }
  elseif($text == '🎼آهنگ های جدید')
  {
    $latest = $db->getLatestMusics();
    $answer = 'درحال حاضر هیچ موزیکی در بخش "'.$text.'" وجود نداره:(';
    $keyboard=$sample_key;
    if($latest)
    {

      $rows = array_chunk($latest,20);
      $answer = '🔅 در اینجا شما لیستی از جدیدترین آهنگهارو،به‌ترتیب مشاهده می‌کنید: '.PHP_EOL.PHP_EOL;
      if(!empty($rows[1]))
      {
        $keyboard = makeInlineKeyboard([
          [['➡️ صفحه‌ی بعدی','n-1']]
        ]);
      }
      foreach($rows[0] as $row)
      {
        $answer .= '🎵 نام : '.substr($row['title'],0,50).PHP_EOL.'🕒 زمان : '.date('i:s',$row['duration']).' دقیقه '.PHP_EOL.'📀حجم : '.round(($row['file_size']/1024)/1024,2).' مگابایت'.PHP_EOL.'📥 دانلود آهنگ : /dl_'.$row['hash'].PHP_EOL.'➖➖➖➖➖➖'.PHP_EOL;
      }
    }
    $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid, 'text'=> mb_convert_encoding($answer,'UTF-8','auto'),'reply_markup'=> $keyboard]);
  }
  elseif($text == '🔈 آلبوم های جدید')
  {
    $album = $db->getNewAlbums();
    $answer = 'درحال حاضر هیچ آلبومی در بخش "'.$text.'" وجود نداره:(';
    $keyboard=$sample_key;
    if($album)
    {

      $rows = array_chunk($album,20);
      $answer = '🔅 در اینجا شما لیستی از جدیدترین آلبوم‌هارو،به‌ترتیب مشاهده می‌کنید: '.PHP_EOL.PHP_EOL;
      if(!empty($rows[1]))
      {
        $keyboard = makeInlineKeyboard([
          [['➡️ صفحه‌ی بعدی','al-1']]
        ]);
      }
      foreach($rows[0] as $row)
      {
        $answer .= '📁 نام آلبوم : '.$row['title'].PHP_EOL.'🗣 هنرمند : '.$row['artist'].PHP_EOL.'🔅 #'.str_replace(' ','',$row['_artist']).' #'.explode('(',str_replace(' ','',$row['_artist']))[0].PHP_EOL.'📥 دانلود آلبوم : /dl_'.$row['hash'].PHP_EOL.'➖➖➖➖➖➖'.PHP_EOL;
      }
    }
    $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid, 'text'=> mb_convert_encoding($answer,'UTF-8','auto'),'reply_markup'=> $keyboard]);
  }
  elseif(preg_match('/^\/dl_(.*)$/i',$text,$hash))
  {
    $hash = $hash[1];
    if(strlen($hash) == 7)
    {
      $res = $db->getMp3Info($hash);
      if($res)
      {
        $file_id = $res['file_id'];
        $likes = $res['likes'];
        $dislikes = $res['dislikes'];
        $hash = $res['hash'];
        $favorites = '➕ افزودن به لیست مورد علاقه';
        if($db->isMp3ExistsFavorites($hash,$fromid)) $favorites = '➖ حذف از لیست مورد علاقه';
        $keyboard = makeInlineKeyboard([
          [['❤ ('.$likes.')','like_'.$hash],['💔 ('.$dislikes.')','dislike_'.$hash]],
          [[$favorites,'favorites_'.$hash]],
          [['🔗 اشتراک گذاری', '#dl_'.$hash,3]],
        ]);
        $row = $db->getFirstAds();
        if($row && @$settings['auto_ads'] == true && @$udb['last_ads_id'] != $row['id'] && time() > @$udb['last_ads_time'])
        {
          $args =[];
          @$args['photo'] = $args['video']  = $args['voice'] = $args['audio']  = $args['document']  = $args['sticker'] = $args['video_note'] = $row['file_id'];
          @$args['text'] = $row['message'];
          @$args['caption'] = $row['message'];
          @$args['chat_id'] = $chatid;
          @$method = $row['method'];
          $bot->makeRequest($method,$args);
          $id = $row['id'];
          $db->update('ads',['users_count'=> $row['users_count'] +1],"id='$id'");
          $db->update('users',['last_ads_id'=> $id,'last_ads_time'=> time()],"user_id='$fromid'");
          $row = $db->getFirstAds();

          if($row['users_count'] >= $row['sent_count'])
          {
            $args['chat_id'] = logs;
            unset($args['reply_to_message_id']);
            $msgid = $bot->makeRequest($method,$args)['result']['message_id'];
            $answer = 'تبلیغ مورد نظر برای '.$row['users_count'].' کاربر ارسال شد.';
            $bot->sendMessage(['chat_id'=> logs, 'reply_to_message_id'=> $msgid,'text'=> $answer]);
            $db->delete('ads',"id='$id'");
          }
        }
        $caption = '[🎵 جستجوی موزیک](https://t.me/'.$me['username'].'?start=search)';
        $bot->sendAudio(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid, 'audio'=> $file_id,'reply_markup'=> $keyboard,'caption'=> $caption,'parse_mode'=>'MarkDown']);
        $db->addToDownloadList($hash , $fromid);
        $db->addMp3DownloadCount($hash);
      }else{
        $answer = 'موزیک موردنظر یافت نشد';
        $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid,'text'=> $answer]);
      }
    }
    elseif(strlen($hash) == 8)
    {
      $musics = $db->getAlbumMusics($hash);
      if($musics)
      {
        $album = $db->getAlbumInfo($hash);
        $answer = '✅ شما آلبوم "'.$album['title'].'" با صدای هنرمند "'.$album['artist'].'" را انتخاب نمودید.'.PHP_EOL.' 🔸 این آلبوم '.count($musics).' ترک دارد،شما می‌توانید آهنگ ‌های این آلبوم را در لیست زیر مشاهده کنید.'.PHP_EOL.PHP_EOL.'🔅 #'.str_replace(' ','',$album['_title']).' #'.explode('(',str_replace(' ','',$album['_artist']))[0];
        $key = [];
        $key[] = [['🔙 برگشت']];
        foreach($musics as $row)
        {
          $key[] = [['🎶 '.$album['_artist'].' -'.$row['title']]];
        }
        $keyboard = makeKeyboard($key);
        $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid,'text'=> $answer,'reply_markup'=> $keyboard]);
      }else {
        $answer = 'آلبوم مورد نظر یافت نشد';
        $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid,'text'=> $answer]);
      }

    }
  }
  elseif(preg_match('/^🎶 (.*)$/i',$text,$title))
  {
    $ex = explode(' -',$title[1]);
    $res = $db->getMusicByTitle($ex[1],$ex[0]);
    If($res)
    {
      $hash = $res['hash'];
      $db->addToDownloadList($hash , $fromid);
      $db->addMp3DownloadCount($hash);
      $file_id = $res['file_id'];
      $likes = $res['likes'];
      $dislikes = $res['dislikes'];
      $hash = $res['hash'];
      $favorites = '➕ افزودن به لیست مورد علاقه';
      if($db->isMp3ExistsFavorites($hash,$fromid)) $favorites = '➖ حذف از لیست مورد علاقه';
      $keyboard = makeInlineKeyboard([
        [['❤ ('.$likes.')','like_'.$hash],['💔 ('.$dislikes.')','dislike_'.$hash]],
        [[$favorites,'favorites_'.$hash]],
        [['🔗 اشتراک گذاری', '#dl_'.$hash,3]],
      ]);

      $caption = '[🎵 جستجوی موزیک](https://t.me/'.$me['username'].'?start=search)';
      $bot->sendAudio(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid, 'audio'=> $file_id,'reply_markup'=> $keyboard,'caption'=> $caption,'parse_mode'=>'MarkDown']);
    }
  }
  elseif(preg_match('/^\/ads_(.*)$/',$text,$match))
  {
    $hash = $match[1];
    $row = $db->getAdsByHash($hash);
    $answer = '❌ تبلیغ مورد نظر یافت نشد.';
    $keyboard = $menu;
    if($row && !empty($row))
    {
      $args =[];
      @$args['photo'] = $args['video']  = $args['voice'] = $args['audio']  = $args['document']  = $args['sticker'] = $args['video_note'] = $row['file_id'];
      @$args['text'] = $row['message'];
      @$args['caption'] = $row['message'];
      @$args['chat_id'] = $chatid;
      @$args['reply_to_message_id'] = $messageid;
      $keyboard = makeInlineKeyboard([[['🗑 حذف تبلیغ','delads_'.$hash],['✏️ ویرایش تبلیغ','editads_'.$hash]]]);
      @$args['reply_markup'] = $keyboard;
      $bot->makeRequest($row['method'],$args);
      return false;
    }
    $bot->sendMessage(['chat_id'=> $chatid,'text'=> $answer,'reply_to_message_id'=> $messageid,'reply_markup'=> $keyboard]);
  }

  elseif($text == 'منوی مدیریت' && $is_sudo )
  {
    step();
    $answer = '🔅 به منوی مدیریتی ربات خوش آمدید،ربات تحت فرمان شماست؛با استفاده از کیبردهای زیر می‌توانید ربات را مدیریت نمایید.';

    $bot->sendMessage(['chat_id'=> $chatid,'text'=> $answer,'reply_to_message_id'=> $messageid,'reply_markup'=> $menu]);
  }
  elseif($text == 'تنظیم کانال')
  {
    $keyboard = makeKeyboard([[['منوی مدیریت'],['منوی اصلی']]]);
    $answer = 'برای تنظیم کانال؛لطفا نام کاربری کانال را ارسال نمایید.توجه داشته باشید این کانال بعنوان کانال عضویت و کانال ارسال پست‌ها تنظیم می‌شود.';
    $bot->sendMessage(['chat_id'=> $chatid,'text'=> $answer,'reply_to_message_id'=> $messageid,'reply_markup'=> $keyboard]);
    step('sc');
  }
  elseif($text == 'ارسال تبلیغات' && $is_sudo)
  {
    $keyboard = makeKeyboard([[['منوی مدیریت'],['منوی اصلی']]]);
    $answer = 'لطفا تبلیغتان را در قالب #متن ، #عکس ، #ویدیو و ... ارسال کنید ، برای تبلیغاتی که شامل رسانه می‌شود می‌توانید از کپشن استفاده نمایید.';
    $bot->sendMessage(['chat_id'=> $chatid,'text'=> $answer,'reply_to_message_id'=> $messageid,'reply_markup'=> $keyboard]);
    step('sendAds');
  }

  elseif($text == 'ویرایش تبلیغات' && $is_sudo)
  {
    $rows = $db->getAdsList();
    $answer = '❌ هیچ تبلیغی تاکنون صورت نگرفته است.';
    if($rows)
    {
      if(!empty($rows))
      {
        $id = 1;
        $answer = '🔅 لیست تبلیغاتی که اخیرا توسط شما یا سایر ادمین‌های ربات به لیست تبلیغات اضافه شده اند: '.PHP_EOL.PHP_EOL;
        date_default_timezone_set('Asia/Tehran');
        foreach($rows as $row)
        {
          $answer .= $id.'- 💢 مشاهده تبلیغ /ads_'.$row['hash'].PHP_EOL.' ✅تعداد ارسالی : '.$row['sent_count']. 'عدد'.PHP_EOL.' 👁‍🗨تعداد بازدید : '.$row['users_count'].' بار'.PHP_EOL.'⏰ تاریخ ثبت : '.date('Y/m/d - H:i:s',$row['time']).PHP_EOL.'➖➖➖➖➖➖'.PHP_EOL;
          $id++;
        }
        date_default_timezone_set('Europe/Berlin');
      }
    }
    $bot->sendMessage(['chat_id'=> $chatid,'text'=>$answer,'reply_to_message_id'=> $messageid,'reply_markup'=> $menu]);
  }
  elseif(preg_match('/^عضویت در کانال (.*)/ui',@$text,$state)  && $is_sudo)
  {
    switch(@$state[1])
    {
      case '❌':
        $state = 1;
        $status = 'فعال';
        break;
    case '✅':
      $state = 0;
      $status = 'غیرفعال';
      break;
    }
    $db->update('settings',['join_ch'=> $state],"id='1'");
    $answer = 'حالت عضویت در کانال پشتیبانی ربات "'.$status.'" شد.';
    $bot->sendMessage(['chat_id'=> $chatid,'text'=>$answer,'reply_to_message_id'=> $messageid,'reply_markup'=> menuKey()]);
  }
  elseif(preg_match('/^وضعیت تبلیغ خودکار  (.*)/ui',@$text,$state)  && $is_sudo)
  {
    switch(@$state[1])
    {
      case '❌':
        $state = 1;
        $status = 'فعال';
        break;
    case '✅':
      $state = 0;
      $status = 'غیرفعال';
      break;
    }
    $db->update('settings',['auto_ads'=> $state],"id='1'");
    $answer = 'حالت تبلیغات خودکار "'.$status.'" شد.';
    $bot->sendMessage(['chat_id'=> $chatid,'text'=>$answer,'reply_to_message_id'=> $messageid,'reply_markup'=> menuKey()]);
  }
  elseif(@$text == 'مشاهده آمار و وضعیت ربات' && $is_sudo)
  {
    $users = $db->select('users','','id')->num_rows;
    $music = $db->select('sounds','','id')->num_rows;
    $join_ch =  str_replace([1,0],['✅','❌'],(int )$settings['join_ch']);
    $auto_ads = str_replace([1,0],['✅','❌'],(int) $settings['auto_ads']);
    $edited = $db->getEditedMp3();
    $ch = isset($settings['channel'])?$settings['channel']:'تنظیم نشده';
    $answer = '🔅 وضعیت ربات : '.PHP_EOL.' * عضویت در کانال : '.$join_ch.PHP_EOL.' * تبلیغ خودکار : '.$auto_ads.PHP_EOL.' * تعداد کاربران : '.$users.PHP_EOL.' * تعداد موزیک‌های موجود در دیتابیس : '.$music.PHP_EOL.' * تعداد موزیک های ادیت شده : '.$edited.PHP_EOL.' * کانال : '.$ch.PHP_EOL;
    $bot->sendMessage(['chat_id'=> $chatid,'text'=>$answer,'reply_to_message_id'=> $messageid,'reply_markup'=> menuKey()]);
  }
  elseif(@$text == 'ارسال پیام به کاربران' && $is_sudo)
  {
    step('sall');
    $answer = 'لطفاً پیامی که می‌خواهید برای تمامی کاربران ربات فرستاده شود را ارسال کنید؛پیام مورد نظر می‌توانید بصورت #عکس،#متن،#ویدئو و ... باشد.';
    $keyboard = makeKeyboard([[['منوی مدیریت'],['منوی اصلی']]]);
    $bot->sendMessage(['chat_id'=> $chatid,'text'=>$answer,'reply_to_message_id'=> $messageid,'reply_markup'=> $keyboard]);
  }
  elseif(@$text == 'فروارد پیام به کاربران' && $is_sudo)
  {
    step();
    step('fall');
    $answer = 'لطفاً پیامی که می‌خواهید برای تمامی کاربران ربات فروارد شود را ارسال کنید؛پیام مورد نظر می‌توانید بصورت #عکس،#متن،#ویدئو و ... باشد.';
    $keyboard = makeKeyboard([[['منوی مدیریت'],['منوی اصلی']]]);
    $bot->sendMessage(['chat_id'=> $chatid,'text'=>$answer,'reply_to_message_id'=> $messageid,'reply_markup'=> $keyboard]);
  }
  elseif(@$udb['step'] == 'fall' && isset($update['message']))
  {
    step();
    $args = [];
    $args['hash'] = create_hash(5);
    $args['chat_id'] = $chatid;
    $args['message_id'] = $messageid;
    $args['method'] = 'forwardMessage';
    $db->insert('sendmsg',$args);
    $answer = '✅ پیام شما در صف ارسال قرار گرفت و بزودی برای تمامی کاربران ربات ارسال می‌شود،اگر پیامی در صف ارسال قرار داشته باشد ،پس از اتمام ارسال پیامهای درصف پیام شما به کاربران فرستاده می‌شود.';
    $bot->sendMessage(['chat_id'=> $chatid,'text'=>$answer,'reply_to_message_id'=> $messageid,'reply_markup'=> menuKey()]);

  }
  elseif(@$udb['step'] == 'sall' && isset($update['message']))
  {
    $get =  getMethod();
    $type = $get['type'];
    $method = $get['method'];
    $args = [];
    $args['chat_id'] = $chatid;
    $args['message_id'] = $messageid;
    if(isset($update['message']['photo'])) $args['file_id'] = end($update['message']['photo'])['file_id'];
    elseif(isset($update['message'][$type])) $args['file_id'] = $update['message'][$type]['file_id'];
    if(isset($update['message']['text'])) $args['message'] = $text;
    elseif(isset($update['message']['caption'])) $args['message'] = $update['message']['caption'];
    $args['hash'] = create_hash(5);
    $args['method'] = $method;
    $db->insert('sendmsg',$args);
    $answer = '✅ پیام شما در صف ارسال قرار گرفت و بزودی برای تمامی کاربران ربات ارسال می‌شود،اگر پیامی در صف ارسال قرار داشته باشد ،پس از اتمام ارسال پیامهای درصف پیام شما به کاربران فرستاده می‌شود.';
    $bot->sendMessage(['chat_id'=> $chatid,'text'=>$answer,'reply_to_message_id'=> $messageid,'reply_markup'=> menuKey()]);
  }
  elseif(@$udb['step'] == 'sc'  && isset($update['message']))
  {
    $keyboard = makeKeyboard([[['منوی مدیریت'],['منوی اصلی']]]);
    $answer = '❌ نام کاربری کانال اشتباه و یا ربات در کانال مورد نظر ادمین نمی‌باشد.';
    if(checkChannel($text) == 'administrator')
    {
      step();
      $db->update('settings',['channel'=> $text],"id='1'");
      $keyboard = $menu;
      $answer = '✅ کانال مورد نظر تنظیم شد.';
    }
    $bot->sendMessage(['chat_id'=> $chatid,'text'=>$answer,'reply_to_message_id'=> $messageid,'reply_markup'=> $keyboard]);
  }
  elseif(preg_match('/^u_count_(\d+)/i',@$udb['step'],$match))
  {
    if(is_numeric($text))
    {
      $id = $match[1];
      $hash = create_hash(5);
      $ads =[];
      $ads['sent_count'] = (int) $text;
      $ads['users_count'] = 0;
      $ads['hash'] = $hash;
      $ads['time'] = time();
      $db->update('ads',$ads,"id='$id'");
      step();
      $answer = 'تبلیغ شما با موفقیت ثبت شد،پس از ارسال تمامی تبالیغ در صف ارسال که از قبل توسط شما یا سایر ادمین‌های ربات تنظیم شده است؛این تبلیغ برای کاربران ارسال می‌شود.';
    }else{
      $answer = 'برای تعیین تعداد ارسال پست تبلیغاتی مورد نظر می‌بایست یک عدد لاتین ارسال کنید مثال:'.PHP_EOL.'<pre>23619</pre>';
    }
    $bot->sendMessage(['chat_id'=> $chatid,'text'=> $answer,'reply_to_message_id'=> $messageid,'parse_mode'=> 'html','reply_markup'=> $menu]);
  }
  elseif(@$udb['step'] == 'sendAds')
  {
    $obj = getMethod();
    $method = $obj['method'];
    $type = $obj['type'];
    $ads=[];
    if(isset($update['message']['photo'])) $ads['file_id'] = end($update['message']['photo'])['file_id'];
    elseif(isset($update['message'][$type])) $ads['file_id'] = $update['message'][$type]['file_id'];
    if(isset($update['message']['text'])) $ads['message'] = $text;
    elseif(isset($update['message']['caption'])) $ads['message'] = $update['message']['caption'];
    $ads['method'] = $method;
    $db->insert('ads',$ads);

    $id = $db->getFisrtId('ads');
    step('u_count_'. $id);
    $answer = 'می‌خواهید پست تبلیغاتی مورد نظر یه چند نفر فرستاده شود؟تعداد را بصورت عدد انگلیسی وارد کنید.';
    $keyboard = makeKeyboard([[['منوی مدیریت'],['منوی اصلی']]]);
    $bot->sendMessage(['chat_id'=> $chatid,'text'=> $answer,'reply_to_message_id'=> $messageid,'reply_markup'=> $keyboard]);
  }
  elseif(preg_match('/^editads_(.*)$/',@$udb['step'],$match))
  {
    $hash = $match[1];
    $obj = getMethod();
    $method = $obj['method'];
    $type = $obj['type'];
    $ads=[];
    if(isset($update['message']['photo'])) $ads['file_id'] = end($update['message']['photo'])['file_id'];
    elseif(isset($update['message'][$type]) && empty($ads['file_id'])) $ads['file_id'] = $update['message'][$type]['file_id'];
    if(isset($update['message']['text'])) $ads['message'] = $text;
    elseif(isset($update['message']['caption'])) $ads['message'] = $update['message']['caption'];
    $ads['method'] = $method;
    $db->update('ads',$ads,"hash='$hash'");

    $id = $db->getAdsByHash($hash)['id'];
    step('u_count_'. $id);
    $answer = 'می‌خواهید پست تبلیغاتی مورد نظر یه چند نفر فرستاده شود؟تعداد را بصورت عدد انگلیسی وارد کنید.';
    $keyboard = makeKeyboard([[['منوی مدیریت'],['منوی اصلی']]]);
    $bot->sendMessage(['chat_id'=> $chatid,'text'=> $answer,'reply_to_message_id'=> $messageid,'reply_markup'=> $keyboard]);
    unset($data);
  }
  elseif(isset($update['message']['text']))
  {
    $keyboard=$main_keyboard;
  	$answer = '👀 هیچ نتیجه ای برای این عبارت پیدا نشد:'.PHP_EOL.'<pre>'.strip_tags($text).'</pre>'.PHP_EOL.PHP_EOL.'اگر از نام خواننده یا آهنگ مورد نظر خود مطمئن نیستید با استفاده از لینک زیر نام درست را پیدا کنید:'.PHP_EOL.PHP_EOL.'<a href="http://www.google.com/search?q='.urlencode($text).'">🔎 جستجو در گوگل</a>';
  	if($_rows = $db->search($text))
  	{
      $_rows = array_chunk($_rows,10);
      if(!empty($_rows[1]))
      {
        $keyboard = makeInlineKeyboard([
          [['➡️ صفحه‌ی بعدی','s-1-'.$text]]
        ]);
      }
  		$answer ='🔅 نمایش نتایج جستجوی شما برای <pre>'.$text.'</pre> :'.PHP_EOL.'ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ '.PHP_EOL.PHP_EOL;
      $x = 20;
      $rows = array_reverse($_rows[0]);
      $count = count($rows);
      if($count<20) $x= $count;
    foreach($rows as $row)
    {
        $answer .= $x.'. <strong>'.strip_tags($row['title']).'</strong>'.PHP_EOL.'📥 دانلود /dl_'.$row['hash'].PHP_EOL.'🕒 '.date('i:s',$row['duration']).' - 💾 '.round(($row['file_size']/1024)/1024,2).' مِگابایت'.PHP_EOL.'➖➖➖➖➖➖'.PHP_EOL;
        $x--;
      }
      $current =1;
      $answer .= PHP_EOL.PHP_EOL.'📃 صفحه '.$current.' از '.count($_rows).PHP_EOL.'🎊 تعداد ترانه‌ی یافت شده با عبارت مورد جستجوی شما : '.count($_rows);
      $answer = mb_convert_encoding($answer,'UTF-8','auto');
   }
   $bot->sendMessage(['chat_id'=> $chatid, 'reply_to_message_id'=> $messageid, 'text'=> $answer,'parse_mode'=>'HTML','reply_markup'=> $keyboard,'disable_web_page_preview'=> true]);
  }


elseif(preg_match('/^like_(.*)$/',@$data,$hash))
{

  $hash = $hash[1];
  $answer = 'شما قبلا ❤️ کرده بودید.';
  if($db->likeMp3($hash,$fromid))
  {
    $answer = '❤️ نظر شما برای موسیقی مورد نظر ثبت شد.';
  }
  $likes = $db->getMp3Likes($hash);
  $dislikes = $db->getMp3Dislikes($hash);
  $favorites = '➕ افزودن به لیست مورد علاقه';
  if($db->isMp3ExistsFavorites($hash,$fromid)) $favorites = '➖ حذف از لیست مورد علاقه';
  $keyboard = makeInlineKeyboard([
    [['❤ ('.$likes.')','like_'.$hash],['💔 ('.$dislikes.')','dislike_'.$hash]],
    [[$favorites,'favorites_'.$hash]],
    [['🔗 اشتراک گذاری', '#dl_'.$hash,3]],
  ]);
  if(empty($messageid)){
    $messageid = $callback['inline_message_id'];
    $keyboard = makeInlineKeyboard([
      [['❤ ('.$likes.')','like_'.$hash],['💔 ('.$dislikes.')','dislike_'.$hash]],
      [['🔗 اشتراک گذاری', '#dl_'.$hash,3]],
    ]);
  }
  $bot->editMessageReplyMarkup(['chat_id'=> $chatid,'message_id'=> $messageid,'inline_message_id'=> $messageid,'reply_markup'=>$keyboard ]);
  $bot->answerCallbackQuery(['callback_query_id'=> $callback_id,'text'=> $answer,'show_alert'=> true]);
  unset($data);
}
elseif(preg_match('/^dislike_(.*)$/',@$data,$hash))
{
  $hash = $hash[1];
  $answer = 'شما قبلا 💔 کرده بودید.';
  if($db->dislikeMp3($hash,$fromid))
  {
    $answer = '💔 نظر شما برای موسیقی مورد نظر ثبت شد.';
  }
  $likes = $db->getMp3Likes($hash);
  $dislikes = $db->getMp3Dislikes($hash);
  $favorites = '➕ افزودن به لیست مورد علاقه';
  if($db->isMp3ExistsFavorites($hash,$fromid)) $favorites = '➖ حذف از لیست مورد علاقه';
  $keyboard = makeInlineKeyboard([
    [['❤ ('.$likes.')','like_'.$hash],['💔 ('.$dislikes.')','dislike_'.$hash]],
    [[$favorites,'favorites_'.$hash]],
    [['🔗 اشتراک گذاری', '#dl_'.$hash,3]],
  ]);
  if(empty($messageid)){
    $messageid = $callback['inline_message_id'];
    $keyboard = makeInlineKeyboard([
      [['❤ ('.$likes.')','like_'.$hash],['💔 ('.$dislikes.')','dislike_'.$hash]],
      [['🔗 اشتراک گذاری', '#dl_'.$hash,3]],
    ]);
  }
  $bot->editMessageReplyMarkup(['chat_id'=> $chatid,'message_id'=> $messageid,'inline_message_id'=> $messageid,'reply_markup'=>$keyboard ]);
  $bot->answerCallbackQuery(['callback_query_id'=> $callback_id,'text'=> $answer,'show_alert'=> true]);
  unset($data);
}
elseif($data == 'sm')
{
  $answer = '🌟 حالت منوی ساده ';
  $bot->sendMessage(['chat_id'=> $chatid, 'text'=> $answer,'reply_markup'=> $sample_key]);
}
elseif(preg_match('/^delads_(.*)$/',@$data,$match))
{
  $hash = $match[1];
  $delete = $db->deleteAds($hash);
  if($delete)
  {
    $answer = 'تبلیغ مورد نظر از لیست تبلیغات ربات پاک شد.';
    $keyboard = makeInlineKeyboard([[]]);
    $bot->editMessageReplyMarkup(['chat_id'=> $chatid,'message_id'=> $messageid,'reply_markup'=> $keyboard]);
  }else{
    $answer = '❌ تبلیغ مورد نظر وجود ندارد.';
  }
  $bot->answerCallbackQuery(['callback_query_id'=> $callback_id,'text'=> $answer,'show_alert'=> true]);
}
elseif(preg_match('/^editads_(.*)/',@$data,$match))
{
  $hash = $match[1];
  $keyboard = makeInlineKeyboard([[]]);
  $bot->editMessageReplyMarkup(['chat_id'=> $chatid,'message_id'=> $messageid,'reply_markup'=> $keyboard]);
  $keyboard = makeKeyboard([[['منوی مدیریت'],['منوی اصلی']]]);
  $answer = 'لطفا تبلیغتان را در قالب #متن ، #عکس ، #ویدیو و ... ارسال کنید ، برای تبلیغاتی که شامل رسانه می‌شود می‌توانید از کپشن استفاده نمایید.';
  $bot->sendMessage(['chat_id'=> $chatid,'text'=> $answer,'reply_to_message_id'=> $messageid,'reply_markup'=> $keyboard]);
  step('editads_'.$hash);
}
elseif(preg_match('/^favorites_(.*)$/i',@$data,$match))
{
  $hash = $match[1];
  if($db->isMp3ExistsFavorites($hash,$fromid))
  {
    $answer = '✅ موسیقی مورد نظر از لیست آهنگ‌های مورد علاقه‌تان حذف شد.';
    $favorites = '➕ افزودن به لیست مورد علاقه';
    $db->deleteFromFavoritesList($hash,$fromid);
  }else{
    $answer = '✅ موسیقی مورد نظر به لیست آهنگ‌های مورد علاقه‌تان اضافه شد.';
    $favorites = '➖ حذف از لیست مورد علاقه';
    $db->addToFavoritesList($hash,$fromid);
  }
  $res = $db->getMp3Info($hash);
  $likes = $res['likes'];
  $dislikes = $res['dislikes'];
  $keyboard = makeInlineKeyboard([
    [['❤ ('.$likes.')','like_'.$hash],['💔 ('.$dislikes.')','dislike_'.$hash]],
    [[$favorites,'favorites_'.$hash]],
    [['🔗 اشتراک گذاری', '#dl_'.$hash,3]],
  ]);
  $bot->editMessageReplyMarkup(['chat_id'=> $chatid,'message_id'=> $messageid,'reply_markup'=>$keyboard ]);
  $bot->answerCallbackQuery(['callback_query_id'=> $callback_id,'text'=> $answer,'show_alert'=> true]);
  unset($data);
}
elseif(preg_match('/^md-(\d+)$/i',@$data,$match))
{
  $current = $match[1];
  $previous = $current-1;
  $next = $current+1;
  $downloads = $db->getMostDownloads();
  if($downloads)
  {
    $alert = 'لیست آهنگ‌ها بروز شد، شما اکنون در صفحه '.($current+1).' هستید.';
    $rows = array_chunk($downloads,20);
    $keyboard;
    $answer = '🔅 در اینجا شما آهنگ‌هایی که بیشترین تعداد دانلود را داشته‌اند،به‌ترتیب مشاهده می‌کنید: '.PHP_EOL.PHP_EOL;
    if(empty($rows[$next]))
    {
      $keyboard = makeInlineKeyboard([
        [['صفحه‌ی قبلی⬅️','md-'.$previous]]
      ]);
      $alert = 'لیست آهنگ‌ها بروز شد،شما اکنون در صفحه‌ی آخر هستید.';
    }else{
      $keyboard = makeInlineKeyboard([
        [['صفحه‌ی قبلی⬅️','md-'.$previous],['➡️ صفحه‌ی بعدی','md-'.$next]]
      ]);
    }
    if(empty($rows[$previous]))
    {
      $keyboard = makeInlineKeyboard([
        [['➡️ صفحه‌ی بعدی','md-1']]
      ]);
      $alert = 'لیست آهنگ‌ها بروز شد،شما اکنون در صفحه‌ی اول هستید.';
    }
    foreach($rows[$current] as $row)
    {
      $answer .= '🎵 نام : '.substr($row['title'],0,50).PHP_EOL.'🕒 زمان : '.date('i:s',$row['duration']).' دقیقه '.PHP_EOL.'📀حجم : '.round(($row['file_size']/1024)/1024,2).' مگابایت'.PHP_EOL.'📥 دانلود آهنگ : /dl_'.$row['hash'].PHP_EOL.'➖➖➖➖➖➖'.PHP_EOL;
    }

    $bot->editMessageText(['chat_id'=> $chatid,'message_id'=>$messageid,'text'=> mb_convert_encoding($answer,'UTF-8','auto'),'reply_markup'=> $keyboard]);
  }
  $bot->answerCallbackQuery(['callback_query_id'=> $callback_id, 'text'=> $alert]);
}
elseif(preg_match('/^ml-(\d+)$/i',@$data,$match))
{
  $current = $match[1];
  $previous = $current-1;
  $next = $current+1;
  $likes = $db->getMostLiked();
  if($likes)
  {
    $alert = 'لیست آهنگ‌ها بروز شد، شما اکنون در صفحه '.($current+1).' هستید.';
    $rows = array_chunk($likes,20);
    $keyboard;
    $answer = '🔅 در اینجا شما آهنگ‌هایی که بیشترین تعداد ❤️ داشته‌اند،به‌ترتیب مشاهده می‌کنید: '.PHP_EOL.PHP_EOL;
    if(empty($rows[$next]))
    {
      $keyboard = makeInlineKeyboard([
        [['صفحه‌ی قبلی⬅️','ml-'.$previous]]
      ]);
      $alert = 'لیست آهنگ‌ها بروز شد،شما اکنون در صفحه‌ی آخر هستید.';
    }else{
      $keyboard = makeInlineKeyboard([
        [['صفحه‌ی قبلی⬅️','ml-'.$previous],['➡️ صفحه‌ی بعدی','ml-'.$next]]
      ]);
    }
    if(empty($rows[$previous]))
    {
      $keyboard = makeInlineKeyboard([
        [['➡️ صفحه‌ی بعدی','ml-1']]
      ]);
      $alert = 'لیست آهنگ‌ها بروز شد،شما اکنون در صفحه‌ی اول هستید.';
    }
    foreach($rows[$current] as $row)
    {
      $answer .= '🎵 نام : '.substr($row['title'],0,50).PHP_EOL.'🕒 زمان : '.date('i:s',$row['duration']).' دقیقه '.PHP_EOL.'📀حجم : '.round(($row['file_size']/1024)/1024,2).' مگابایت'.PHP_EOL.'📥 دانلود آهنگ : /dl_'.$row['hash'].PHP_EOL.'➖➖➖➖➖➖'.PHP_EOL;
    }

    $bot->editMessageText(['chat_id'=> $chatid,'message_id'=>$messageid,'text'=> mb_convert_encoding($answer,'UTF-8','auto'),'reply_markup'=> $keyboard]);
  }
  $bot->answerCallbackQuery(['callback_query_id'=> $callback_id, 'text'=> $alert]);
}
elseif(preg_match('/^al-(\d+)$/i',@$data,$match))
{
  $current = $match[1];
  $previous = $current-1;
  $next = $current+1;
  $albums = $db->getNewAlbums();
  if($albums)
  {
    $alert = 'لیست آلبوم‌ها بروز شد، شما اکنون در صفحه '.($current+1).' هستید.';
    $rows = array_chunk($albums,20);
    $keyboard;
    $answer = '🔅 در اینجا شما لیستی از جدیدترین آلبوم‌هارو،به‌ترتیب مشاهده می‌کنید: '.PHP_EOL.PHP_EOL;
    if(empty($rows[$next]))
    {
      $keyboard = makeInlineKeyboard([
        [['صفحه‌ی قبلی⬅️','al-'.$previous]]
      ]);
      $alert = 'لیست آلبوم‌ها بروز شد،شما اکنون در صفحه‌ی آخر هستید.';
    }else{
      $keyboard = makeInlineKeyboard([
        [['صفحه‌ی قبلی⬅️','al-'.$previous],['➡️ صفحه‌ی بعدی','al-'.$next]]
      ]);
    }
    if(empty($rows[$previous]))
    {
      $keyboard = makeInlineKeyboard([
        [['➡️ صفحه‌ی بعدی','al-1']]
      ]);
      $alert = 'لیست آلبوم‌ها بروز شد،شما اکنون در صفحه‌ی اول هستید.';
    }
    foreach($rows[$current] as $row)
    {
      $answer = '📁 نام آلبوم : '.$row['title'].PHP_EOL.'🗣 هنرمند : '.$row['artist'].PHP_EOL.'🔅 #'.str_replace(' ','',$row['_artist']).' #'.explode('(',str_replace(' ','',$row['_artist']))[0].PHP_EOL.'📥 دانلود آلبوم : /dl_'.$row['hash'].PHP_EOL.'➖➖➖➖➖➖'.PHP_EOL;
    }

    $bot->editMessageText(['chat_id'=> $chatid,'message_id'=>$messageid,'text'=> mb_convert_encoding($answer,'UTF-8','auto'),'reply_markup'=> $keyboard]);
  }
  $bot->answerCallbackQuery(['callback_query_id'=> $callback_id, 'text'=> $alert]);
}
elseif(preg_match('/^n-(\d+)$/i',@$data,$match))
{
  $current = $match[1];
  $previous = $current-1;
  $next = $current+1;
  $latest = $db->getLatestMusics($fromid);
  if($latest)
  {
    $alert = 'لیست آهنگ‌ها بروز شد، شما اکنون در صفحه '.($current+1).' هستید.';
    $rows = array_chunk($latest,20);
    $keyboard='';
    $answer = '🔅 در اینجا شما لیستی از جدیدترین آهنگهارو،به‌ترتیب مشاهده می‌کنید: '.PHP_EOL.PHP_EOL;
    if(empty($rows[$next]))
    {
      $keyboard = makeInlineKeyboard([
        [['صفحه‌ی قبلی⬅️','n-'.$previous]]
      ]);
      $alert = 'لیست آهنگ‌ها بروز شد،شما اکنون در صفحه‌ی آخر هستید.';
    }else{
      $keyboard = makeInlineKeyboard([
        [['صفحه‌ی قبلی⬅️','n-'.$previous],['➡️ صفحه‌ی بعدی','n-'.$next]]
      ]);
    }
    if(empty($rows[$previous]))
    {
      $keyboard = makeInlineKeyboard([
        [['➡️ صفحه‌ی بعدی','n-1']]
      ]);
      $alert = 'لیست آهنگ‌ها بروز شد،شما اکنون در صفحه‌ی اول هستید.';
    }
    foreach($rows[$current] as $row)
    {
      $answer .= '🎵 نام : '.substr($row['title'],0,50).PHP_EOL.'🕒 زمان : '.date('i:s',$row['duration']).' دقیقه '.PHP_EOL.'📀حجم : '.round(($row['file_size']/1024)/1024,2).' مگابایت'.PHP_EOL.'📥 دانلود آهنگ : /dl_'.$row['hash'].PHP_EOL.'➖➖➖➖➖➖'.PHP_EOL;
    }

    $bot->editMessageText(['chat_id'=> $chatid,'message_id'=>$messageid,'text'=> mb_convert_encoding($answer,'UTF-8','auto'),'reply_markup'=> $keyboard]);
  }
  $bot->answerCallbackQuery(['callback_query_id'=> $callback_id, 'text'=> $alert]);
}
elseif(preg_match('/^rd-(\d+)$/i',@$data,$match))
{
  $current = $match[1];
  $previous = $current-1;
  $next = $current+1;
  $recently = $db->getRecentlyDownloads($fromid);
  if($recently)
  {
    $alert = 'لیست آهنگ‌ها بروز شد، شما اکنون در صفحه '.($current+1).' هستید.';
    $rows = array_chunk($recently,20);
    $keyboard='';
    $answer = '🔅 در اینجا شما آهنگ‌هایی که اخیراً دانلود کرده اید،به‌ترتیب مشاهده می‌کنید: '.PHP_EOL.PHP_EOL;
    if(empty($rows[$next]))
    {
      $keyboard = makeInlineKeyboard([
        [['صفحه‌ی قبلی⬅️','rd-'.$previous]]
      ]);
      $alert = 'لیست آهنگ‌ها بروز شد،شما اکنون در صفحه‌ی آخر هستید.';
    }else{
      $keyboard = makeInlineKeyboard([
        [['صفحه‌ی قبلی⬅️','rd-'.$previous],['➡️ صفحه‌ی بعدی','rd-'.$next]]
      ]);
    }
    if(empty($rows[$previous]))
    {
      $keyboard = makeInlineKeyboard([
        [['➡️ صفحه‌ی بعدی','rd-1']]
      ]);
      $alert = 'لیست آهنگ‌ها بروز شد،شما اکنون در صفحه‌ی اول هستید.';
    }
    foreach($rows[$current] as $row)
    {
      $answer .= '🎵 نام : '.substr($row['title'],0,50).PHP_EOL.'🕒 زمان : '.date('i:s',$row['duration']).' دقیقه '.PHP_EOL.'📀حجم : '.round(($row['file_size']/1024)/1024,2).' مگابایت'.PHP_EOL.'📥 دانلود آهنگ : /dl_'.$row['hash'].PHP_EOL.'➖➖➖➖➖➖'.PHP_EOL;
    }

    $bot->editMessageText(['chat_id'=> $chatid,'message_id'=>$messageid,'text'=> mb_convert_encoding($answer,'UTF-8','auto'),'reply_markup'=> $keyboard]);
  }
  $bot->answerCallbackQuery(['callback_query_id'=> $callback_id, 'text'=> $alert]);
}
elseif(preg_match('/^l-(\d+)$/i',@$data,$match))
{
  $current = $match[1];
  $previous = $current-1;
  $next = $current+1;
  $liked = $db->getUserLiked($fromid);
  if($liked)
  {
    $alert = 'لیست آهنگ‌ها بروز شد، شما اکنون در صفحه '.($current+1).' هستید.';
    $rows = array_chunk($liked,20);
    $keyboard='';
    $answer = '🔅 در اینجا شما آهنگ‌هایی که اخیراً  پسندیده‌اید،به‌ترتیب مشاهده می‌کنید: '.PHP_EOL.PHP_EOL;
    if(empty($rows[$next]))
    {
      $keyboard = makeInlineKeyboard([
        [['صفحه‌ی قبلی⬅️','l-'.$previous]]
      ]);
      $alert = 'لیست آهنگ‌ها بروز شد،شما اکنون در صفحه‌ی آخر هستید.';
    }else{
      $keyboard = makeInlineKeyboard([
        [['صفحه‌ی قبلی⬅️','l-'.$previous],['➡️ صفحه‌ی بعدی','l-'.$next]]
      ]);
    }
    if(empty($rows[$previous]))
    {
      $keyboard = makeInlineKeyboard([
        [['➡️ صفحه‌ی بعدی','l-1']]
      ]);
      $alert = 'لیست آهنگ‌ها بروز شد،شما اکنون در صفحه‌ی اول هستید.';
    }
    foreach($rows[$current] as $row)
    {
      $answer .= '🎵 نام : '.substr($row['title'],0,50).PHP_EOL.'🕒 زمان : '.date('i:s',$row['duration']).' دقیقه '.PHP_EOL.'📀حجم : '.round(($row['file_size']/1024)/1024,2).' مگابایت'.PHP_EOL.'📥 دانلود آهنگ : /dl_'.$row['hash'].PHP_EOL.'➖➖➖➖➖➖'.PHP_EOL;
    }

    $bot->editMessageText(['chat_id'=> $chatid,'message_id'=>$messageid,'text'=> mb_convert_encoding($answer,'UTF-8','auto'),'reply_markup'=> $keyboard]);
  }
  $bot->answerCallbackQuery(['callback_query_id'=> $callback_id, 'text'=> $alert]);
}
elseif(preg_match('/^f-(\d+)$/i',@$data,$match))
{
  $current = $match[1];
  $previous = $current-1;
  $next = $current+1;
  $favorites = $db->getUserFavorites($fromid);
  if($favorites)
  {
    $alert = 'لیست آهنگ‌ها بروز شد، شما اکنون در صفحه '.($current+1).' هستید.';
    $rows = array_chunk($favorites,20);
    $keyboard='';
    $answer = '🔅 در اینجا شما آهنگ‌هایی که به لیست علاقه مندی اضافه کرده‌اید،به‌ترتیب مشاهده می‌کنید: '.PHP_EOL.PHP_EOL;
    if(empty($rows[$next]))
    {
      $keyboard = makeInlineKeyboard([
        [['صفحه‌ی قبلی⬅️','f-'.$previous]]
      ]);
      $alert = 'لیست آهنگ‌ها بروز شد،شما اکنون در صفحه‌ی آخر هستید.';
    }else{
      $keyboard = makeInlineKeyboard([
        [['صفحه‌ی قبلی⬅️','f-'.$previous],['➡️ صفحه‌ی بعدی','f-'.$next]]
      ]);
    }
    if(empty($rows[$previous]))
    {
      $keyboard = makeInlineKeyboard([
        [['➡️ صفحه‌ی بعدی','f-1']]
      ]);
      $alert = 'لیست آهنگ‌ها بروز شد،شما اکنون در صفحه‌ی اول هستید.';
    }
    foreach($rows[$current] as $row)
    {
      $answer .= '🎵 نام : '.substr($row['title'],0,50).PHP_EOL.'🕒 زمان : '.date('i:s',$row['duration']).' دقیقه '.PHP_EOL.'📀حجم : '.round(($row['file_size']/1024)/1024,2).' مگابایت'.PHP_EOL.'📥 دانلود آهنگ : /dl_'.$row['hash'].PHP_EOL.'➖➖➖➖➖➖'.PHP_EOL;
    }

    $bot->editMessageText(['chat_id'=> $chatid,'message_id'=>$messageid,'text'=> mb_convert_encoding($answer,'UTF-8','auto'),'reply_markup'=> $keyboard]);
  }
  $bot->answerCallbackQuery(['callback_query_id'=> $callback_id, 'text'=> $alert]);
}
elseif(preg_match('/^s-(\d+)-(.*)$/i',@$data,$match))
{
  $current = $match[1];
  $previous = $current-1;
  $next = $current+1;
  $query = $match[2];
  if($_rows = $db->search($query))
  {
    $_rows = array_chunk($_rows,10);
    if(empty($_rows[$next]))
    {
      $keyboard = makeInlineKeyboard([
        [['صفحه‌ی قبلی⬅️','s-'.$previous.'-'.$query]]
      ]);
      $alert = 'لیست آهنگ‌ها بروز شد،شما اکنون در صفحه‌ی آخر هستید.';
    }else{
      $alert = 'لیست آهنگ‌ها بروز شد، شما اکنون در صفحه '.$current.' هستید';
      $keyboard = makeInlineKeyboard([
        [['صفحه‌ی قبلی⬅️','s-'.$previous.'-'.$query],['➡️ صفحه‌ی بعدی','s-'.$next.'-'.$query]]
      ]);
    }
    if(empty($_rows[$previous]))
    {
      $keyboard = makeInlineKeyboard([
        [['➡️ صفحه‌ی بعدی','s-1-'.$query]]
      ]);
      $alert = 'لیست آهنگ‌ها بروز شد،شما اکنون در صفحه‌ی اول هستید.';
    }
    $answer ='🔅 نمایش نتایج جستجوی شما برای <pre>'.$query.'</pre> :'.PHP_EOL.'ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ ـ '.PHP_EOL.PHP_EOL;
  $x = 20;
    $rows = array_reverse($_rows[$current]);
     if(count($rows)<20) $x=count($rows);
    foreach($rows as $row)
    {
      $answer .= $x.'. <strong>'.strip_tags($row['title']).'</strong>'.PHP_EOL.'📥 دانلود /dl_'.$row['hash'].PHP_EOL.'🕒 '.date('i:s',$row['duration']).' - 💾 '.round(($row['file_size']/1024)/1024,2).' مِگابایت'.PHP_EOL.'➖➖➖➖➖➖'.PHP_EOL;
      $x--;
    }
    $current++;
    $answer .= PHP_EOL.PHP_EOL.'📃 صفحه '.$current.' از '.count($_rows).PHP_EOL.'🎊 تعداد ترانه‌ی یافت شده با عبارت مورد جستجوی شما : '.count($_rows);
    $answer = mb_convert_encoding($answer,'UTF-8','auto');
  }
  $bot->editMessageText(['chat_id'=> $chatid,'message_id'=>$messageid,'text'=> mb_convert_encoding($answer,'UTF-8','auto'),'reply_markup'=> $keyboard,'parse_mode'=>'HTML']);
  $bot->answerCallbackQuery(['callback_query_id'=> $callback_id, 'text'=> $alert]);
}
elseif(@$query == '#history')
{

  $answer = 'هیچ موزیکی تا کنون دانلود نکرده اید :(';
  $results =[];
  $downloads = $db->getRecentlyDownloads($fromid);
  if($downloads)
  {

    $rows = array_chunk($downloads,30)[0];
    $answer = '🔅 لیست دانلودهای اخیر شما';
    $keyboard = ['inline_keyboard'=> [ [['text'=>'⏰ تاریخچه دانلود','switch_inline_query_current_chat'=> '#history' ]] ]];
    $x = 1;
    foreach($rows as $row)
    {
      $hash = $row['hash'];
      $downloads = $row['downloads'];
      $likes = $row['likes'];
      $dislikes = $row['dislikes'];
      $title = $row['title'];
      $description = '❤️ '.$likes.' - 💔 '.$dislikes.' - 📥 '.$downloads;
      $thumb_url = !empty($row['src_url'])?$row['src_url']:'https://cdn3.iconfinder.com/data/icons/ultimate-social/150/41_itunes-512.png';
      if(!empty($row['file_id'])){ $results[] = ['type'=> 'article','id'=> base64_encode(rand(0,99999999)),'title'=> $x.'-'.$title,'description'=> $description,'thumb_url'=> $thumb_url,'input_message_content'=> ['message_text'=> '/dl_'. $hash],'reply_markup'=> $keyboard];$x++;}

    }
  }
  $bot->answerInlineQuery(['inline_query_id'=> $inlinequery_id,'results'=> json_encode($results),'cache_time'=> 1,'is_personal'=> true,'switch_pm_text'=>$answer ,'switch_pm_parameter'=> 'inline']);
}
elseif(@$query == '#albums')
{

  $answer = 'هیچ آلبومی وجود ندارد.';
  $results =[];
  $albums = $db->getNewAlbums();
  if($albums)
  {

    $rows = array_chunk($albums,30)[0];
    $answer = '🔅 لیست جدیدترین آلبوم‌ها';
    $keyboard = ['inline_keyboard'=> [ [['text'=>'🔈 آلبوم های جدید','switch_inline_query_current_chat'=> '#albumes' ]] ]];
    $x = 1;
    foreach($rows as $row)
    {
      $hash = $row['hash'];
      if(!empty($row['hash'])){ $results[] = ['type'=> 'article','id'=> base64_encode(rand(0,99999999)),'thumb_url'=>'https://image.freepik.com/free-icon/music-album_318-43305.jpg','title'=> $row['title'].' '.$row['artist'],'description'=> $row['_title'].' '.$row['_artist'],'input_message_content'=> ['message_text'=> '/dl_'. $hash],'reply_markup'=> $keyboard];$x++;}

    }
  }
  $bot->answerInlineQuery(['inline_query_id'=> $inlinequery_id,'results'=> json_encode($results),'cache_time'=> 1,'is_personal'=> true,'switch_pm_text'=>$answer ,'switch_pm_parameter'=> 'inline']);
}
elseif(@$query == 'دانلودهای من')
{
  $downloads = $db->getRecentlyDownloads($fromid);
  $answer = 'هیچ موزیکی تا کنون دانلود نکرده اید :(';
  $results =[];
  if($downloads)
  {

    $rows = array_chunk($downloads,30)[0];
    $answer = '🔅 لیست دانلودهای اخیر شما';
    foreach($rows as $row)
    {
      $likes = $row['likes'];
      $dislikes = $row['dislikes'];
      $hash = $row['hash'];
      $caption = '[🎵 جستجوی موزیک](https://t.me/'.$me['username'].'?start=search)';
      $keyboard = ['inline_keyboard'=> [ [['text'=> '❤ ('.$likes.')','callback_data'=>'like_'.$hash],['text'=>'💔 ('.$dislikes.')','callback_data'=>'dislike_'.$hash]],[['text'=>'🔗 اشتراک گذاری','switch_inline_query'=> '#dl_'.$hash]] ]];
      if(!empty($row['file_id']))$results[] = ['type'=> 'audio','id'=> base64_encode(rand(0,99999999)),'audio_file_id'=> $row['file_id'],'reply_markup'=> $keyboard,'caption'=> $caption,'parse_mode'=> 'MarkDown'];
    }
  }
  $bot->answerInlineQuery(['inline_query_id'=> $inlinequery_id,'results'=> json_encode($results),'cache_time'=> 1,'is_personal'=> true,'switch_pm_text'=>$answer ,'switch_pm_parameter'=> 'inline']);
}
elseif(@$query == '#favorites')
{
  $favorites = $db->getUserFavorites($fromid);
  $answer = 'هیچ موزیکی تا کنون به لیست موردعلاقه‌تان اضافه نکرده اید :(';
  $results =[];
  if($favorites)
  {

    $rows = array_chunk($favorites,30)[0];
    $answer = '🔅 لیست موردعلاقه‌ی شما';
    foreach($rows as $row)
    {
      $likes = $row['likes'];
      $dislikes = $row['dislikes'];
      $hash = $row['hash'];
      $caption = '[🎵 جستجوی موزیک](https://t.me/'.$me['username'].'?start=search)';
      $keyboard = ['inline_keyboard'=> [ [['text'=> '❤ ('.$likes.')','callback_data'=>'like_'.$hash],['text'=>'💔 ('.$dislikes.')','callback_data'=>'dislike_'.$hash]],[['text'=>'🔗 اشتراک گذاری','switch_inline_query'=> '#dl_'.$hash]] ]];
      if(!empty($row['file_id']))$results[] = ['type'=> 'audio','id'=> base64_encode(rand(0,99999999)),'audio_file_id'=> $row['file_id'],'reply_markup'=> $keyboard,'caption'=> $caption,'parse_mode'=> 'MarkDown'];
    }
  }
  $bot->answerInlineQuery(['inline_query_id'=> $inlinequery_id,'results'=> json_encode($results),'cache_time'=> 1,'is_personal'=> true,'switch_pm_text'=>$answer ,'switch_pm_parameter'=> 'inline']);
  unset($results);
}

elseif(@$query == '#mylikes')
{
  $liked = $db->getUserLiked($fromid);
  $answer = 'هیچ موزیکی تا کنون هیچ موزیکی را نپسندیده اید :(';
  $results =[];
  if($liked)
  {

    $rows = array_chunk($liked,30)[0];
    $answer = '🔅 لیست موردپسندها‌ی شما';
    foreach($rows as $row)
    {
      $likes = $row['likes'];
      $dislikes = $row['dislikes'];
      $hash = $row['hash'];
      $caption = '[🎵 جستجوی موزیک](https://t.me/'.$me['username'].'?start=search)';
      $keyboard = ['inline_keyboard'=> [ [['text'=> '❤ ('.$likes.')','callback_data'=>'like_'.$hash],['text'=>'💔 ('.$dislikes.')','callback_data'=>'dislike_'.$hash]],[['text'=>'🔗 اشتراک گذاری','switch_inline_query'=> '#dl_'.$hash]],[['text'=> '❤️ لایک ها','switch_inline_query_current_chat'=> '#mylikes']] ]];
      if(!empty($row['file_id']))$results[] = ['type'=> 'audio','id'=> base64_encode(rand(0,99999999)),'audio_file_id'=> $row['file_id'],'reply_markup'=> $keyboard,'caption'=> $caption,'parse_mode'=> 'MarkDown'];
    }
  }
  $bot->answerInlineQuery(['inline_query_id'=> $inlinequery_id,'results'=> json_encode($results),'cache_time'=> 1,'is_personal'=> true,'switch_pm_text'=>$answer ,'switch_pm_parameter'=> 'inline']);
  unset($results);
}

elseif(@$query == '#md')
{
  $downloads = $db->getMostDownloads();
  $answer = 'هیچ موزیکی در حال حاضر در این بخش وجود ندارد.';
  $results =[];
  if($downloads)
  {

    $rows = array_chunk($downloads,30)[0];
    $answer = '🔅 لیست آهنگ‌هایی که تاکنون بیشترین دانلود را داشته اند.';
    foreach($rows as $row)
    {

      $likes = $row['likes'];
      $dislikes = $row['dislikes'];
      $hash = $row['hash'];
      $caption = '[🎵 جستجوی موزیک](https://t.me/'.$me['username'].'?start=search)';
      $keyboard = ['inline_keyboard'=> [ [['text'=> '❤ ('.$likes.')','callback_data'=>'like_'.$hash],['text'=>'💔 ('.$dislikes.')','callback_data'=>'dislike_'.$hash]],[['text'=>'🔗 اشتراک گذاری','switch_inline_query'=> '#dl_'.$hash]] ]];
      if(!empty($row['file_id']))$results[] = ['type'=> 'audio','id'=> base64_encode(rand(0,99999999)),'audio_file_id'=> $row['file_id'],'reply_markup'=> $keyboard,'caption'=> $caption,'parse_mode'=> 'MarkDown'];
    }
  }
  $bot->answerInlineQuery(['inline_query_id'=> $inlinequery_id,'results'=> json_encode($results),'cache_time'=> 1,'is_personal'=> true,'switch_pm_text'=>$answer ,'switch_pm_parameter'=> 'inline']);
  unset($results);
}
elseif(@$query == '#latest')
{
  $latest = $db->getLatestMusics();
  $answer = 'هیچ موزیکی در حال حاضر در این بخش وجود ندارد.';
  $results =[];
  if($latest)
  {

    $rows = array_chunk($latest,30)[0];
    $answer = '🔅 لیست جدیدترین‌ها';
    foreach($rows as $row)
    {

      $likes = $row['likes'];
      $dislikes = $row['dislikes'];
      $hash = $row['hash'];
      $caption = '[🎵 جستجوی موزیک](https://t.me/'.$me['username'].'?start=search)';
      $keyboard = ['inline_keyboard'=> [ [['text'=> '❤ ('.$likes.')','callback_data'=>'like_'.$hash],['text'=>'💔 ('.$dislikes.')','callback_data'=>'dislike_'.$hash]],[['text'=>'🔗 اشتراک گذاری','switch_inline_query'=> '#dl_'.$hash]],[['text'=>'🎼آهنگ های جدید','switch_inline_query_current_chat'=>'#latest']] ]];
      if(!empty($row['file_id']))$results[] = ['type'=> 'audio','id'=> base64_encode(rand(0,99999999)),'audio_file_id'=> $row['file_id'],'reply_markup'=> $keyboard,'caption'=> $caption,'parse_mode'=> 'MarkDown'];
    }
  }
  $bot->answerInlineQuery(['inline_query_id'=> $inlinequery_id,'results'=> json_encode($results),'cache_time'=> 1,'is_personal'=> true,'switch_pm_text'=>$answer ,'switch_pm_parameter'=> 'inline']);
  unset($results);
}
elseif(@$query == '#likes')
{
  $liked = $db->getMostLiked();
  $answer = 'هیچ موزیکی در حال حاضر در این بخش وجود ندارد.';
  $results =[];
  if($liked)
  {

    $rows = array_chunk($liked,30)[0];
    $answer = '🔅 لیست آهنگ‌هایی که تاکنون بیشترین ❤️ را داشته اند.';
    foreach($rows as $row)
    {

      $likes = $row['likes'];
      $dislikes = $row['dislikes'];
      $hash = $row['hash'];
      $caption = '[🎵 جستجوی موزیک](https://t.me/'.$me['username'].'?start=search)';
      $keyboard = ['inline_keyboard'=> [ [['text'=> '❤ ('.$likes.')','callback_data'=>'like_'.$hash],['text'=>'💔 ('.$dislikes.')','callback_data'=>'dislike_'.$hash]],[['text'=>'🔗 اشتراک گذاری','switch_inline_query'=> '#dl_'.$hash]] ]];
      if(!empty($row['file_id']))$results[] = ['type'=> 'audio','id'=> base64_encode(rand(0,99999999)),'audio_file_id'=> $row['file_id'],'reply_markup'=> $keyboard,'caption'=> $caption,'parse_mode'=>'MarkDown'];
    }
  }
  $bot->answerInlineQuery(['inline_query_id'=> $inlinequery_id,'results'=> json_encode($results),'cache_time'=> 1,'is_personal'=> true,'switch_pm_text'=>$answer ,'switch_pm_parameter'=> 'inline']);
  unset($results);
}
elseif(@$query == '#advertise')
{
  unset($results);
  $results = [];
  $answer = '💸 تبلیغات شما در مجموعه ما'.PHP_EOL.'<pre>برند خود را به بیش از صدهزار کاربر ایرانی معرفی کنید.</pre>'.PHP_EOL.PHP_EOL.'برای اطلاعات بیشتر در مورد قیمت ها و نحوه تبلیغات، لطفا از طریق دکمه زیر با آی دی تبلیغات ما تماس بگیرید.'.PHP_EOL.PHP_EOL.'📱 منوی اصلی: /home'.PHP_EOL.PHP_EOL.'#advertisement #ignore';
  $keyboard = ['inline_keyboard'=>[ [['text'=>'🆔 ارتباط با بخش تبلیغات','url'=>'https://t.me/'.$advertiser]]] ];
  $results[] = ['type'=> 'article','title'=> '💸 تبلیغات شما در مجموعه ما','id'=> base64_encode(rand(0,99999999)),'input_message_content'=> ['message_text'=> $answer,'parse_mode'=> 'HTML'],'reply_markup'=>$keyboard];
  $answer = '📣 پخش سراسری موزیک'.PHP_EOL.'صدای خود را به گوش صدها هزار علاقه‌مند موسیقی برسانید.'.PHP_EOL.PHP_EOL.'برای پخش آثار هنری خود در مجموعه کانال‌های دنیای ترانه و آهنگیفای با آی دی  پشتیبانی هنرمندان تماس بگیرید.'.PHP_EOL.PHP_EOL.PHP_EOL.'📱 منوی اصلی: /home'.PHP_EOL.PHP_EOL.'#advertise_music #ignore';
  $results[] = ['type'=> 'article','title'=> '📣 پخش سراسری موزیک','id'=> base64_encode(rand(0,99999999)),'input_message_content'=> ['message_text'=> $answer,'parse_mode'=> 'HTML'],'reply_markup'=>$keyboard];
  $bot->answerInlineQuery(['inline_query_id'=> $inlinequery_id,'results'=> json_encode($results),'cache_time'=> 1,'is_personal'=> true]);
}
elseif(@$query == '#submit_music')
{
  unset($results);
  $results = [];
  $answer = '🎙 چگونه آهنگ خود را در بات ثبت کنم؟'.PHP_EOL.PHP_EOL.'برای ثبت موزیک خود در بات کافیست آهنگ مورد نظر را همان‌طور که برای دوستان خود در تلگرام آهنگ ارسال می کنید، برای بات فوروارد یا آپلود نمایید.'.PHP_EOL.PHP_EOL.'آهنگ‌های ارسالی بر اساس کیفیت فایل موزیک و کیفیت اطلاعات موزیک (نام خواننده و نام آهنگ) امتیاز دهی شده و در بات نمایش داده می‌شوند.'.PHP_EOL.PHP_EOL.'<pre>منظور از کیفیت اطلاعات موزیک، مُتونیست که به عنوان نام آهنگ و نام خواننده در تلگرام نمایش داده می‌شوند. در صورتی که این متون حاوی نام کانال، سایت یا ... باشند، از امتیاز فایل ارسالی کاسته خواهد شد.</pre>'.PHP_EOL.PHP_EOL.'📱 منوی اصلی: /home'.PHP_EOL.PHP_EOL.'#submit_music #ignore';
  $keyboard = ['inline_keyboard'=>[ [['text'=>'🆔 ارتباط با بخش هنرمندان','url'=>'https://t.me/'.$submit_musicer]]] ];
  $results[] = ['type'=> 'article','title'=> '🎙 چگونه آهنگ خود را در بات ثبت کنم؟','id'=> base64_encode(rand(0,99999999)),'input_message_content'=> ['message_text'=> $answer,'parse_mode'=> 'HTML'],'reply_markup'=>$keyboard];
  $answer = '📣 پخش سراسری موزیک'.PHP_EOL.'صدای خود را به گوش صدها هزار علاقه‌مند موسیقی برسانید.'.PHP_EOL.PHP_EOL.'برای پخش آثار هنری خود در مجموعه کانال‌های دنیای ترانه و آهنگیفای با آی دی  پشتیبانی هنرمندان تماس بگیرید.'.PHP_EOL.PHP_EOL.PHP_EOL.'📱 منوی اصلی: /home'.PHP_EOL.PHP_EOL.'#advertise_music #ignore';
  $results[] = ['type'=> 'article','title'=> '📣 پخش سراسری موزیک','id'=> base64_encode(rand(0,99999999)),'input_message_content'=> ['message_text'=> $answer,'parse_mode'=> 'HTML'],'reply_markup'=>$keyboard];
  $bot->answerInlineQuery(['inline_query_id'=> $inlinequery_id,'results'=> json_encode($results),'cache_time'=> 1,'is_personal'=> true]);
}
elseif(preg_match('/^#dl_(.*)$/i',@$query,$hash))
{
  $hash = $hash[1];
  if( strlen($hash) == 7)
  {
    $answer = '❌ موزیک مورد نظر وجود ندارد';
    $res = $db->getMp3Info($hash);
    if($res)
    {
      $caption = '[🎵 جستجوی موزیک](https://t.me/'.$me['username'].'?start=search)';
      $answer = $res['title'];
      $likes = $res['likes'];
      $dislikes = $res['dislikes'];
      $hash = $res['hash'];
      $keyboard = ['inline_keyboard'=> [ [['text'=> '❤ ('.$likes.')','callback_data'=>'like_'.$hash],['text'=>'💔 ('.$dislikes.')','callback_data'=>'dislike_'.$hash]],[['text'=>'🔗 اشتراک گذاری','switch_inline_query'=> '#dl_'.$hash]] ]];
      $types = [];
      if(!empty($res['src_url'])){
        $des = '👍 '.$res['likes'].'  👎 '.$res['dislikes'].'  📥 '.$res['downloads'].'  💾 '.round(($res['file_size']/1024)/1024,2);
        $src_caption = '🎵 '.$res['title'].PHP_EOL.'💢 #Music '.PHP_EOL.'🕒 '.date('i:s',$res['duration']).' - 📀'.round(($res['file_size']/1024)/1024,2).PHP_EOL.'📥 '.$res['downloads'].' - ❤️ '.$res['likes'].' - 💔 '.$res['dislikes'].PHP_EOL.PHP_EOL.'👾 t.me/'.$me['username'].'?start='.$res['hash'];
        $types[] = ['type'=> 'article','id'=> base64_encode(rand(0,99999999)),'thumb_url'=> $res['src_url'],'title'=> $answer,'description'=> $des,'input_message_content'=>['message_text'=>$src_caption]];
      }
      if(!empty($res['file_id'])){
        $types[] = ['type'=> 'audio','id'=> base64_encode(rand(0,99999999)),'audio_file_id'=> $res['file_id'],'caption'=> $caption,'parse_mode'=> 'MarkDown','reply_markup'=> $keyboard];
      }
      if(!empty($res['file_id320'])){
        $types[] = ['type'=> 'audio','id'=> base64_encode(rand(0,99999999)),'audio_file_id'=> $res['file_id320'],'caption'=> $caption,'parse_mode'=> 'MarkDown','reply_markup'=> $keyboard];
      }
      if(!empty($res['cover'])){
        $types[] = ['type'=> 'voice','id'=> base64_encode(rand(0,99999999)),'title'=> '#cover','voice_file_id'=> $res['cover'],'caption'=> '#cover'.PHP_EOL.$caption.PHP_EOL,'parse_mode'=> 'MarkDown','reply_markup'=> $keyboard];
      }
      $results = json_encode($types);
    }
    $x = $bot->answerInlineQuery(['inline_query_id'=> $inlinequery_id,'results'=> $results,'cache_time'=> 1,'is_personal'=> true,'switch_pm_text'=>$answer ,'switch_pm_parameter'=> 'inline']);
    unset($results);
  }
  if(strlen($hash) == 8)
  {
    $musics = $db->getAlbumMusics($hash);
    $answer = '❌ آلبوم مورد نظر یافت نشد';
    $results =[];
    if($musics)
    {
      $album = $db->getAlbumInfo($hash);
      $answer = '✅ شما آلبوم "'.$album['title'].'" با صدای هنرمند "'.$album['artist'].'" را انتخاب نمودید.'.PHP_EOL.' 🔸 این آلبوم '.count($musics).' ترک دارد،شما می‌توانید آهنگ ‌های این آلبوم را در لیست زیر مشاهده کنید.'.PHP_EOL.PHP_EOL.'🔅 #'.str_replace(' ','',$album['_title']).' #'.explode('(',str_replace(' ','',$album['_artist']))[0];
      $results = [];
      foreach($musics as $row)
      {
        $row = $db->getMp3Info($row['hash']);
        $likes = $row['likes'];
        $dislikes = $row['dislikes'];
        $hash = $row['hash'];
        $caption = '[🎵 جستجوی موزیک](https://t.me/'.$me['username'].'?start=search)';
        $keyboard = ['inline_keyboard'=> [ [['text'=> '❤ ('.$likes.')','callback_data'=>'like_'.$hash],['text'=>'💔 ('.$dislikes.')','callback_data'=>'dislike_'.$hash]],[['text'=>'🔗 اشتراک گذاری','switch_inline_query'=> '#dl_'.$hash]] ]];
        $results[] = ['type'=> 'audio','id'=> base64_encode(rand(0,99999999)),'audio_file_id'=> $row['file_id'],'reply_markup'=> $keyboard,'caption'=> $caption,'parse_mode'=> 'MarkDown'];
      }
    }
    $bot->answerInlineQuery(['inline_query_id'=> $inlinequery_id,'results'=> json_encode($results),'cache_time'=> 1,'is_personal'=> true,'switch_pm_text'=> $answer,'switch_pm_parameter'=> 'inline']);
    unset($results);
  }

}
elseif(isset($update['inline_query']['query']) && !empty($update['inline_query']['query']))
{
  $answer = '❌ هیچ نتیجه ای برای عبارت مورد نظر یافت نشد';
  $results =[];
  $rows = $db->search($query);
  if(!empty($rows))
  {
    $rows = array_chunk($rows,50);
    $answer = '🎶 نتایج جستجو برای عبارت "'.$query.'" ';
    foreach($rows[0] as $row)
    {
      $hash = $row['hash'];
      $file_id = $row['file_id'];
      $likes = $row['likes'];
      $dislikes = $row['dislikes'];
      $hash = $row['hash'];
      $caption = '[🎵 جستجوی موزیک](https://t.me/'.$me['username'].'?start=search)';
      $keyboard = ['inline_keyboard'=> [ [['text'=> '❤ ('.$likes.')','callback_data'=>'like_'.$hash],['text'=>'💔 ('.$dislikes.')','callback_data'=>'dislike_'.$hash]],[['text'=>'🔗 اشتراک گذاری','switch_inline_query'=> '#dl_'.$hash]] ]];
      if(!empty($file_id)) $results[] = ['type'=> 'audio','id'=> base64_encode(rand(0,99999999)),'audio_file_id'=> $row['file_id'],'reply_markup'=> $keyboard,'caption'=>$caption, 'parse_mode'=> 'MarkDown'];
    }
  }
  $bot->answerInlineQuery(['inline_query_id'=> $inlinequery_id,'results'=> json_encode($results),'cache_time'=> 1,'is_personal'=> true,'switch_pm_text'=> $answer,'switch_pm_parameter'=> 'inline']);
  unset($results);
}
