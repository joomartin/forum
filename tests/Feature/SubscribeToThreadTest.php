<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SubscribeToThreadTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_subscribe_to_threads()
    {
        $this->signIn();

        // Given we have a thread
        $thread = create('Thread');

        // When the user subscribes to it
        $this->post($thread->path('subscriptions'));

        $this->assertCount(1, $thread->fresh()->subscriptions);
    }

    /** @test */
    public function a_user_can_unsubscribe_from_threads()
    {
        $this->signIn();

        // Given we have a thread
        $thread = create('Thread');

        $thread->subscribe();

        // When the user subscribes to it
        $this->delete($thread->path('subscriptions'));

        $this->assertCount(0, $thread->subscriptions);
    }
}
