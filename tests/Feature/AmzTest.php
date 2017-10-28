<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Amz\ProductInfo;
use App\Jobs\RunTestForOffersOnListings;
use App\Jobs\CreateEmailReportForUser;
use App\Amz\Seller;

class AmzTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_it_can_return_offers_counts_for_one_asin()
    {
        $pi = new ProductInfo("B01MF9EMLE");
        $counts = $pi->getLowestOfferCounts();
        // Should have at least one count returned.
        $this->assertTrue($counts[0]["count"] > 0);
    }

    public function test_it_can_return_offers_counts_for_three_asins()
    {
        $pi = new ProductInfo(["B01MF9EMLE", "B01M6Z93N7", "B01L2IQ2G4"]);
        $counts = $pi->getLowestOfferCounts();
        $this->assertTrue($counts[0]["count"] > 0);
        $this->assertTrue($counts[1]["count"] > 0);
        $this->assertTrue($counts[2]["count"] > 0);
    }

    public function test_job_runtestforoffersonlistings() {
      $j = new RunTestForOffersOnListings(1);
      $j->handle();
    }

    public function test_job_createemailreportforuser() {
      $j = new CreateEmailMultipleOffersReportForUser(1);
      $j->handle();
    }

    public function test_seller_from_url_has_correct_id() {
      $url = "https://www.amazon.com/gp/aag/main/ref=olp_merch_name_1?ie=UTF8&asin=B01LKAN5D2&isAmazonFulfilled=0&seller=A2IQVTJWR5DV4C";
      $seller = Seller::createFromOfferUrl($url);
      $this->assertEquals($seller->id, "A2IQVTJWR5DV4C");
    }

}
