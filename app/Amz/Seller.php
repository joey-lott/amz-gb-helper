<?php

namespace App\Amz;

class Seller {

  public $id;

  static public function createFromOfferUrl($url) {
    $chunks = explode("&", $url);
    foreach($chunks as $chunk) {
      $subchunk = explode("=", $chunk);
      if($subchunk[0] == "seller") {
        $seller = new Seller();
        $seller->id = $subchunk[1];
        return $seller;
      }
    }
  }
}
