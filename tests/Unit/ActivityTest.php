<?php

namespace Tests\Feature;

use App\Activity;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ActivityTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_records_activity_when_a_thread_is_created()
    {
        $this->signIn();
        $thread = create('Thread');

        $activity = Activity::first();

        $this->assertEquals($activity->subject->id, $thread->id);
    }

    /** @test */
    public function it_records_activity_when_a_reply_is_created()
    {
        $this->signIn();
        create('Reply');

        $activities = Activity::all();

        $this->assertCount(2, $activities);
    }

    /** @test */
    public function it_fetches_an_activity_feed_for_a_user()
    {
        // Given we have a thread
        $this->signIn();

        create('Thread', ['user_id' => auth()->id()], 2);

        // And another from a week ago
        auth()->user()->activities()->first()->update([
            'created_at' => Carbon::now()->subWeek()->format('Y-m-d')
        ]);

        // When we fetches feed
        $feed = Activity::feed(auth()->user(), 50);

        //Then it should be returned in a proper format
        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->format('Y-m-d')
        ));

        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->subWeek()->format('Y-m-d')
        ));
    }
}
