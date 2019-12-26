<?php
/*
اوپن شده در کانال وین تب 
@Win_Tab
اوپن.کننده مرداب
@Mr_MoRdaB

*/
class musicBerooz
{
  public $host= 'https://musicberooz.ir';
  public function httpRequest(string $url)
  {
    $ch = curl_init('https://musics-land.ir/tt.php?url='.$url);
    curl_setopt_array($ch,[
      CURLOPT_RETURNTRANSFER => true,
    ]);
    $result = curl_exec($ch);
    if($error = curl_error($ch)) return $error;
    curl_close($ch);
    return $result;
  }

  public function getCategory(string $title='')
  {
    if(stripos($title,'آلبوم')) return 1;
    elseif(stripos($title,'ریمیکس')) return 2;
    if(stripos($title,'آهنگ')) return 3;
  }
  public function htmlParser($string='',string $type='GET')
  {

    @$dom = new DOMDocument();
    @$dom->loadHTML($string);
    if($type=='get')
    {
      @$tags = $dom->getElementsByTagName('div');
      $res = [];
      $x =0;
      foreach($tags as $tag)
      {
        $attr = $tag->getAttribute('class');
        if($attr == 'info')
        {
          $title = $tag->getElementsByTagName('strong')->item(0)->getElementsByTagName('span')->item(0)->nodeValue;
          $artist = $tag->getElementsByTagName('strong')->item(0)->getElementsByTagName('span')->item(1)->nodeValue;
          $_title = $tag->getElementsByTagName('strong')->item(1)->getElementsByTagName('span')->item(0)->nodeValue;
          $_artist = $tag->getElementsByTagName('strong')->item(1)->getElementsByTagName('span')->item(1)->nodeValue;
          $src = $tag->getElementsByTagName('img')->item(0)->getAttribute('src');
          $id = $tag->getElementsByTagName('div')[0]->getElementsByTagName('div')[0]->getAttribute('data-id');
          $href = $tag->getElementsByTagName('a')[0]->getAttribute('href');
          $category = $tag->getElementsByTagName('li')->item(1)->getElementsByTagName('a')->item(0)->nodeValue;
          if(!empty($title)) $res[$x]['title'] = $title;
          if(!empty($artist)) $res[$x]['artist'] = $artist;
          if(!empty($_title)) $res[$x]['_title'] = $_title;
          if(!empty($_artist)) $res[$x]['_artist'] = $_artist;
          if(!empty($src)) $res[$x]['src'] = $src;
          if(!empty($id)) $res[$x]['id'] = $id;
          if(!empty($href)) $res[$x]['href'] = $href;
          if(!empty($category)) $res[$x]['category'] = $category;
          $x++;
        }
      }
      return $res;
    }
    elseif($type == 'info')
    {
      @$tags = $dom->getElementsByTagName('div');
      foreach($tags as $tag)
      {
        $attr = $tag->getAttribute('class');
        if($attr == 'info')
        {
          $title = $dom->getElementsByTagName('title')->item(0)->nodeValue;
          $category = $this->getCategory($title);
          if($category == 3)
          {
            $title = $tag->getElementsByTagName('strong')->item(0)->getElementsByTagName('span')->item(1)->nodeValue;
            $artist = $tag->getElementsByTagName('strong')->item(0)->getElementsByTagName('span')->item(0)->nodeValue;
            $_title = $tag->getElementsByTagName('strong')->item(1)->getElementsByTagName('span')->item(1)->nodeValue;
            $_artist = $tag->getElementsByTagName('strong')->item(1)->getElementsByTagName('span')->item(0)->nodeValue;
            $src = $tag->getElementsByTagName('img')->item(0)->getAttribute('src');
            $_320 = $tag->getElementsByTagName('div')->item(1)->getElementsByTagName('a')->item(0)->getAttribute('href');
            $_128 = $tag->getElementsByTagName('div')->item(2)->getElementsByTagName('a')->item(0)->getAttribute('href');
            $category = 'آهنگ';
          }
          elseif($category == 2)
          {
            $title = $tag->getElementsByTagName('strong')->item(0)->getElementsByTagName('span')->item(1)->nodeValue;
            $artist = $tag->getElementsByTagName('strong')->item(0)->getElementsByTagName('span')->item(2)->nodeValue;
            $_title = $tag->getElementsByTagName('strong')->item(1)->getElementsByTagName('span')->item(1)->nodeValue;
            $_artist = $tag->getElementsByTagName('strong')->item(1)->getElementsByTagName('span')->item(0)->nodeValue;
            $src = $tag->getElementsByTagName('img')->item(0)->getAttribute('src');
            $category = 'ریمیکس';
            $_128 = $tag->getElementsByTagName('div')->item(1)->getElementsByTagName('a')->item(0)->getAttribute('href');
            $_320 = $tag->getElementsByTagName('div')->item(2)->getElementsByTagName('a')->item(0)->getAttribute('href');
          }
          elseif($category == 1)
          {
            $title = $tag->getElementsByTagName('strong')->item(0)->getElementsByTagName('span')->item(1)->nodeValue;
            $artist = $tag->getElementsByTagName('strong')->item(0)->getElementsByTagName('span')->item(0)->nodeValue;
            $_title = $tag->getElementsByTagName('strong')->item(1)->getElementsByTagName('span')->item(1)->nodeValue;
            $_artist = $tag->getElementsByTagName('strong')->item(1)->getElementsByTagName('span')->item(0)->nodeValue;
            $src = $tag->getElementsByTagName('img')->item(0)->getAttribute('src');
            $category = 'آلبوم';
            $single_320 = $tag->getElementsByTagName('p')->item(1)->getElementsByTagName('a');
            $single320 = [];
            foreach($single_320 as $obj)
            {
              $file = $obj->getAttribute('href');
              @$_title = str_ireplace(['320',$_artist,'.mp3'],'',str_replace('-',' ',end(explode('/',$file))));
              if(empty(trim($_title,' '))) $_title = $_artist;
              $single320[] = ['url'=>$file,'title'=> $obj->nodeValue,'_title'=> $_title];
            }
            $single_128 = $tag->getElementsByTagName('p')->item(2)->getElementsByTagName('a');
            $single128 = [];
            foreach($single_128 as $obj)
            {
              $file = $obj->getAttribute('href');
              @$_title = str_ireplace(['128',$_artist,'.mp3'],'',str_replace('-',' ',end(explode('/',$file))));
              if(empty(trim($_title,' '))) $_title = $_artist;
              $single128[] = ['url'=>$file,'title'=> $obj->nodeValue,'_title'=> $_title];
            }
          }
          if(!empty($title)) $res['title'] = $title;
          if(!empty($artist)) $res['artist'] = $artist;
          if(!empty($_title)) $res['_title'] = $_title;
          if(!empty($_artist)) $res['_artist'] = $_artist;
          if(!empty($src)) $res['src'] = $src;
          if(!empty($id)) $res['id'] = $id;
          if(!empty($href)) $res['href'] = $href;
          if(!empty($category)) $res['category'] = $category;
          if(!empty($_128)) $res['128'] = $_128;
          if(!empty($_320)) $res['320'] = $_320;
          if(!empty($single320)) $res['single320'] = $single320;
          if(!empty($single128)) $res['single128'] = $single128;
        }
      }
      return $res;
    }
    return false;
  }
  public function getNewSounds()
  {
    $string = $this->httpRequest($this->host);
    return $this->htmlParser($string,'get');
  }
  public function getSoundInfo(string $href='')
  {
    $string = $this->httpRequest($href);
    return $this->htmlParser($string,'info');
  }
}
