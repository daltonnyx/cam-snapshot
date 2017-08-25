<?php 
$curl = curl_init();
$file = fopen("/tmp/image.tmp", "w+");
curl_setopt_array($curl, array(
  CURLOPT_URL => "http://82.176.75.252:8889/mjpg/video.mjpg?COUNTER",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_FILE => $file,
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
curl_exec($curl);
fseek($file,0);
$match = [];
while(($line = fgets($file)) !== false) { 
    if(preg_match('/boundary=(\w+)/', $line, $match))
        break;
}
$boundary='--'.$match[1];
fseek($file, 0);
$start = false;
$line_seek = 0;
$imgfile = fopen("./image-".time().".jpg", "a");
while(($line = fgets($file)) !== false) {
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
fclose($file);
fclose($imgfile);
unset("/tmp/image.tmp");
$err = curl_error($curl);

