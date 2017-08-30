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
          CURLOPT_TIMEOUT => 30,
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
        $this->getSnapshot();
        try {
            fseek($this->file, 0);
            $start = false;
            $line_seek = 0;
            $imgfile = fopen("$dir/image-".time().".jpg", "a");
            while(($line = fgets($this->file)) !== false) {
                if( trim($line) == $boundary && $start )
                    break;
                if($start && $line_seek < 3) {
                    $line_seek++;
                    continue;
                }
                if($start)
                    fwrite($imgfile, $line);
                if( trim($line) == $boundary && !$start )
                    $start = true;
            }
            fclose($imgfile);

            $err = curl_error($curl);
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
        $this->boundary = '--'.$match[1];
    }

    function __destruct() {
        fclose($this->file);
        unset($this->filename);
    }
}
