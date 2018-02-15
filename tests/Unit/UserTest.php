<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_fetch_their_most_recent_reply()
    {
        $user = create('User');
        $replyFirst = create('Reply', ['user_id' => $user->id]);
        $replyLast = create('Reply', ['user_id' => $user->id, 'created_at' => Carbon::now()->addHour(1)]);

        $this->assertEquals($replyLast->id, $user->lastReply->id);
    }
}
