<?php

namespace App\Amz;

use Sonnenglas\AmazonMws\AmazonProductInfo;
use Illuminate\Support\Facades\Log;

class ProductInfo {

  private $asins;

  public function __construct($asins) {
    $this->asins = $asins;
  }

  public function getLowestOfferCounts() {
    $api= new AmazonProductInfo("realpeoplegoods");
    $api->setASINs($this->asins);
    $api->fetchLowestOffer();
    $products = $api->getProduct();
    $counts = [];

    if(is_bool($products)) return $counts;

    foreach($products as $product) {

      // The way the Sonnenglas library parses the return value
      // is strange. The array consists of mostly Amazon products as
      // objects. But there is one string element. So filter out the string.
      if(is_string($product)) continue;

      $data = $product->getData();
      $asin = $data["Identifiers"]["MarketplaceASIN"]["ASIN"];
      if(array_key_exists("LowestOfferListings", $data)) {
        $lol = $data["LowestOfferListings"];
        array_push($counts, ["asin" => $asin, "count" => count($lol)]);
      }
    }
    return $counts;
  }

}
