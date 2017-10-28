<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Amz\Seller;
use App\Hijackers;
use App\Jobs\CreateEmailHijackersReportForUser;

class HijackersController extends Controller
{

    public function __construct() {
      $this->middleware("auth");
    }

    public function submit(Request $request) {

      // Split the links input on newlines into an array.
      $links = explode("\n", $request->links);
      for($i = 0; $i < 20; $i++) {

        $name = $request["name_".$i];

        // If a name field is not completed, skip this row.
        if($name == "") continue;

        $link = $request["link_".$i];

        // If a link field is not completed, skip this row.
        if($link == "") continue;

        // Remove any extra whitespace (like \r)
        $link = trim($link);

        // Create a seller object from the link (extracts the seller ID from the link)
        $s = Seller::createFromOfferUrl($link);

        // Test if the hijacker is already in the DB. If so, skip.
        $existing = Hijackers::where("seller_id", $s->id)->count();
        if($existing) continue;

        // Create a new hijacker, set the values, and store to DB.
        $h = new Hijackers();
        $h->seller_id = $s->id;
        $h->user_id = auth()->user()->id;
        $h->seller_name = $name;
        $h->save();
      }
      $this->dispatch(new CreateEmailHijackersReportForUser(auth()->user()->id));
    }
}
