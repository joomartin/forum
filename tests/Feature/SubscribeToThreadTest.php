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

        // And each time a new reply is left
        $thread->addReply([
            'user_id'   => auth()->id(),
            'body'      => 'Some new reply'
        ]);

        // Then a notification should be prepared for the user
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
