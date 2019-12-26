<?php
/* * * * * * * * * * * *\
* uupload.ir uploader class
* Writed in PHP
* By @LordDeveLoper
* 1397/11/7
\* * * * * * * * * * * */
class uupload
{
    public $host='http://uupload.ir/',$url,$params,$error,$links;
    public function httpRequest()
    {
        $ch = curl_init($this->url);
        curl_setopt_array($ch ,
            [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $this->params,
                CURLOPT_HEADER => ['Content-Type: multipart/form-data'],
                CURLOPT_RETURNTRANSFER => true
            ]
        );
        $this->pageData = curl_exec($ch);
        curl_close($ch);
        return $this;
    }
    public function addParam($key='',$value='')
    {
        $this->params[$key] = $value;
        return $this;
    }
    public function upload($args=[])
    {
        if(!file_exists($args['file'])) $this->error[] = 'File path invailed';
        $mimetype = mime_content_type($args['file']);
        $name = basename($args['file']);
        $cfile = new CURLFile($args['file'],$mimetype,$name);
        $this
        ->addParam('submit',' آپلود')
        ->addParam('upload_type', 'standard')
        ->addParam('userfile[]', $cfile);
        $this->url = $this->host.'upload.php';
        $this->httpRequest();
        return $this;
    }
    public function getLinks()
    {
        @$dom = new DOMDocument();
        @$dom->loadHTML($this->pageData);
        @$tags = $dom->getElementsByTagName('input');
        if($tags)
        {
          if($tags->item(0)) $this->links['direct_link'] = $tags->item(0)->getAttribute('value');
          if($tags->item(1)) $this->links['preview'] = $tags->item(1)->getAttribute('value');
        }
        return $this;
    }
}
