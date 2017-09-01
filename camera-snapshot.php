<?php

class CamSnapshot {

    private $ch;
    private $file;
    private $boundary;
    private $filename;

    function __construct($url) {
        $this->ch = curl_init();
        $this->filename = "/tmp/image-".time().".tmp";
        $this->file = fopen($this->filename, "w+");
        curl_setopt_array($this->ch, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_FILE => $this->file,
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 10,
          CURLOPT_HEADER => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "accept: image/webp,image/apng,image/*,*/*;q=0.8",
            "cache-control: no-cache",
            "connection: keep-alive",
            "pragma: no-cache",
            "user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.91 Safari/537.36 Vivaldi/1.92.917.39"
          ),
        ));
    }

    public function fetchTo($dir = '.') {
        if($this->getSnapshot() === false) {
          echo "failed to get snapshot from link\n";
          return;
        }
        try {
            fseek($this->file, 0);
            $start = false;
            $line_seek = 0;
            $imgfile = fopen("$dir/image-".time().".jpg", "a");
            if($this->boundary === false) {
                while(($line = fgets($this->file)) !== false) {
                if( !$start && strpos($line, "\xFF\xD8\xFF") !== 0 ) { // Ignore header part
                  continue;
                }
                if( !$start &&  strpos($line, "\xFF\xD8\xFF") === 0 )
                    $start = true;
                fwrite($imgfile, $line);
              }
            }
            else {
              while(($line = fgets($this->file)) !== false) {
                  if( trim($line) == $this->boundary && $start )
                      break;
                  if($start && $line_seek < 3) {
                      $line_seek++;
                      continue;
                  }
                  if($start)
                      fwrite($imgfile, $line);
                  if( trim($line) == $this->boundary && !$start )
                      $start = true;
              }
            }
            fclose($imgfile);
        }
        catch(Exception $e) {
            echo var_dump($e);
        }
    }

    protected function getSnapshot() {
        curl_exec($this->ch);
        fseek($this->file,0);
        $match = [];
        while(($line = fgets($this->file)) !== false) {
            if(preg_match('/boundary=(\w+)/', $line, $match))
                break;
        }
        if( count($match) > 0 )
          $this->boundary = '--'.$match[1];
        else
          $this->boundary = false;
    }

    function __destruct() {
        fclose($this->file);
        unset($this->filename);
    }
}
