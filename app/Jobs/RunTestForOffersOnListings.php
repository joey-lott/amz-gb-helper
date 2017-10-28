<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\ListingsToTestForOffers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Amz\ProductInfo;
use App\ListingsWithMultipleOffers;

class RunTestForOffersOnListings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $userid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userid)
    {
        $this->userid = $userid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      // Make sure that we run this job no more than 10 times every 2 seconds.
      // That maxes out the allowed 36000 calls per hour allowed by Amazon
      // for this API call to get the lowst offer counts.
      Redis::throttle('listings_to_test_for_offers')->allow(10)->every(2)->then(function() {
        // All the listings to test are stored in the DB. Get the top 20 records for the user, and extract an array of ASINs.
        $asins = DB::table('listings_to_test_for_offers')->where('user_id', $this->userid)->take(20)->pluck('asin')->all();
        // Only test for lowest offer counts and queue another job if there are asins found
        if(count($asins) > 0) {

          // ProductInfo takes an array on ASINs.
          $pi = new ProductInfo($asins);

          // Get the lowest offer counts for all the ASINs. This returns an array of associative arrays with two keys: asin and count
          $counts = $pi->getLowestOfferCounts();
          foreach($counts as $items) {
            $c = $items["count"];
            if($c <= 1) continue;
            $a = $items["asin"];
            $uid = $this->userid;
            $lwmo = new ListingsWithMultipleOffers();
            $lwmo->count = $c;
            $lwmo->asin = $a;
            $lwmo->user_id = $uid;
            $lwmo->save();
          }

          // Delete all the records for the asins just tested. We don't need to test them again.
          DB::table('listings_to_test_for_offers')->whereIn('asin', $asins)->delete();

          // There may be more listings to test for this user, so dispatch another job.
          RunTestForOffersOnListings::dispatch($this->userid);
        }
        else {
          CreateEmailMultipleOffersReportForUser::dispatch($this->userid);
        }
       }, function() {
         return $this->release(10);
       });
    }
}
