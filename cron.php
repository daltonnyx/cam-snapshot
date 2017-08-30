<?php

require_once "./camera-snapshot.php";
require_once "./insecam-fetch.php";

if(!file_exists("./camera.txt"))
  die("No camera.txt file found");
$fh = fopen("./camera.txt", "r");

while( ($camera = fgets($fh)) !== false ) {
  $web_fetch = new WebFetch($camera);
  $image_src = $web_fetch->getImageUrl();
  if($image_src === false)
    continue;
  $cam_snapshot = new CamSnapshot($image_src);
  $cam_snapshot->fetchTo("./data/");
}
