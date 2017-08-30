<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once "./camera-snapshot.php";
require_once "./insecam-fetch.php";
echo "require done \n";
if(!file_exists("./camera.txt"))
  die("No camera.txt file found");
$fh = fopen("./camera.txt", "r");
echo "opened Camera file \n";
while( ($camera = fgets($fh)) !== false ) {
  $camera = str_replace("\n", "", $camera);
  $camera = str_replace("\r", "", $camera);
  $web_fetch = new WebFetch($camera);
  echo "fetching camera id $camera\n";
  $image_src = $web_fetch->getImageUrl();
  if($image_src === false) {
    echo "failed to get camera url of $camera \n";
    continue;
  }
  $cam_snapshot = new CamSnapshot($image_src);
  if(!file_exists("./data/$camera/")) {
    mkdir("./data/$camera/", 0755, true);
  }
  $cam_snapshot->fetchTo("./data/$camera/");
}
