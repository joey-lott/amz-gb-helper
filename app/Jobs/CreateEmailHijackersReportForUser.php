<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Redis;
use App\Hijackers;

class CreateEmailHijackersReportForUser implements ShouldQueue
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
      $user = User::findOrFail($this->userid);

      $hijackers = Hijackers::where('user_id', $this->userid)->get()->all();
      Mail::send("emails.hijackers-report", ["hijackers" => $hijackers], function ($m) use ($user) {
            $m->from('joey@joeylott.com', 'Joey Lott');

            $m->to($user->email, $user->name)->subject('Your Amz-GB-Helper Hijackers Report');
        });
    }
}
