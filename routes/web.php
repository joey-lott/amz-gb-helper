<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Sonnenglas\AmazonMws\AmazonReport;
use Sonnenglas\AmazonMws\AmazonReportRequestList;
use Sonnenglas\AmazonMws\AmazonReportList;
use Sonnenglas\AmazonMws\AmazonReportRequest;
use Sonnenglas\AmazonMws\AmazonProductInfo;
use League\Csv\Reader;
use App\ListingsToTestForOffers;
use App\Jobs\RunTestForOffersOnListings;

Route::get('/', function () {
  echo "<a href='/request-report'>request report</a><br>";
  echo "<a href='/list-requested-reports'>list requested reports</a><br>";
  echo "<a href='/list-reports'>list reports</a><br>";
});


Route::get('/request-report', function () {
  $arl = new AmazonReportRequest("realpeoplegoods");
  $arl->setReportType("_GET_MERCHANT_LISTINGS_ALL_DATA_");
  $r = $arl->requestReport();
  dd($r->response);
});

Route::get('/list-requested-reports', function () {
  $arl = new AmazonReportRequestList("realpeoplegoods");
  $arl->setReportTypes("_GET_MERCHANT_LISTINGS_ALL_DATA_");
  $list = $arl->fetchRequestList();
  dd($arl->getList());
});

Route::get('/list-reports', function () {
  $arl = new AmazonReportList("realpeoplegoods");
  $arl->setReportTypes("_GET_MERCHANT_LISTINGS_ALL_DATA_");
  $arl->fetchReportList();
  $list = $arl->getList();
  foreach($list as $report) {
//    dd($report);
    echo "<a href='list-reports/".$report["ReportId"]."'>".$report["ReportId"]."</a><br>";
  }
});

Route::get('/list-reports/{id}', function ($id) {
  $arl = new AmazonReport("realpeoplegoods");
  //$arl->setReportTypes("_GET_MERCHANT_LISTINGS_ALL_DATA_");
  $arl->setReportId($id);
  $csv = $arl->fetchReport();
  $reader = Reader::createFromString($csv);
  $reader->setHeaderOffset(0);
  $reader->setDelimiter("\t");
  //dump(count($reader));
  foreach($reader as $key => $val) {
    $asin = $val["asin1"];
    $uid = auth()->user()->id;
    $lttfo = new ListingsToTestForOffers;
    $lttfo->asin = $asin;
    $lttfo->user_id = $uid;
    $lttfo->save();
//    echo "<a href='/asin/".$val["asin1"]."/lowest-offers'>".$val["item-name"]."</a><br>";
  }

  RunTestForOffersOnListings::dispatch(auth()->user()->id);
  echo "job dispatched";
});

Route::get('/asin/{id}/lowest-offers', function ($id) {
  $arl = new AmazonProductInfo("realpeoplegoods");
  //$arl->setReportTypes("_GET_MERCHANT_LISTINGS_ALL_DATA_");
  //$arl->setIdType("ASIN");
  $arl->setASINs($id);
  $arl->fetchLowestOffer();
  $products = $arl->getProduct();
  $product = $products[0];
  $data = $product->getData();
  $lol = $data["LowestOfferListings"];
  dump(count($lol));
  //$reader = Reader::createFromString($csv);
  //$reader->setHeaderOffset(0);
  //$reader->setDelimiter("\t");
  //dump(count($reader));
  //foreach($reader as $key => $val) {
  //  echo "<a href='/asin/".$val["asin1"]."/lowest-offers'>".$val["item-name"]."</a><br>";
  //}
//  dd($reader);

});

Route::get('/phpinfo', function () {
  //echo phpinfo();
});

Route::get('/hijackers/input', function () {
  return view("hijackers.input");
});
Route::post('/hijackers/input', "HijackersController@submit");

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
