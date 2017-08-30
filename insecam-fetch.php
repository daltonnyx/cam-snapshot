<?php

class WebFetch {

  private $ch;

  function __construct($id) {
    $this->ch = curl_init();
    curl_setopt_array($this->ch, array(
      CURLOPT_URL => "http://www.insecam.org/en/view/$id/",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
    ));
  }

  protected function fetch() {
    $response = curl_exec($this->ch);
    $err = curl_error($this->ch);
    curl_close($his->ch);
    if($err)
      return $err;
    return $response;
  }

  public function getImageUrl() {
    $html = new DOMDocument();
    @$html->loadHTML($this->fetch());
    $imageNode = $html->getElementById("image0");
    if($imageNode == null) {
      return false;
    }
    return $imageNode->getAttribute("src");
  }
}
