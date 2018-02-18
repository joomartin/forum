<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MentionUsersTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function mentioned_users_in_a_reply_are_notified()
    {
        // Given have a user John Doe
        $john = create('User', ['name' => 'JohnDoe']);
        $this->signIn($john);

        // And other Jane Doe
        $jane = create('User', ['name' => 'JaneDoe']);
        $frank = create('User', ['name' => 'FrankDoe']);

        // And have a thread
        $thread = create('Thread');

        // When John Doe replies and mention @JaneDoe
        $reply = make('Reply', [
            'body'      => 'Hey @JaneDoe look at this. Also @FrankDoe.',
            'thread_id' => $thread->id
        ]);

        $this->json('post', $thread->path('replies'), $reply->toArray());

        // Then Jane Doe should be notified
        $this->assertCount(1, $jane->notifications);
        $this->assertStringEndsWith($reply->thread->title, $jane->notifications->first()->data['message']);

        $this->assertCount(1, $frank->notifications);
        $this->assertStringEndsWith($reply->thread->title, $frank->notifications->first()->data['message']);

    }

    /** @test */
    public function mentioned_users_in_a_thread_are_notified()
    {
        // Given have a user John Doe
        $john = create('User', ['name' => 'JohnDoe']);
        $this->signIn($john);

        // And other Jane Doe
        $jane = create('User', ['name' => 'JaneDoe']);
        $frank = create('User', ['name' => 'FrankDoe']);

        // When John Doe creates a thread and mention @JaneDoe
        $thread = make('Thread', [
            'channel_id'    => create('Channel')->id,
            'title'         => 'tit',
            'body'          => 'Hey @JaneDoe look at this. Also @FrankDoe.'
        ]);

        $this->json('post', '/threads', $thread->toArray());

        // Then Jane Doe should be notified
        $this->assertCount(1, $jane->notifications);
        $this->assertStringEndsWith($thread->title, $jane->notifications->first()->data['message']);

        $this->assertCount(1, $frank->notifications);
        $this->assertStringEndsWith($thread->title, $jane->notifications->first()->data['message']);
    }

    /** @test */
    public function it_can_fetch_all_mentioned_users_starting_with_goven_characters()
    {
        $user = create('User', ['name' => 'JohnDoe']);
        $user1 = create('User', ['name' => 'JohnDoe2']);
        $user2 = create('User', ['name' => 'JaneDoe']);

        $result = $this->json('GET', '/api/users', ['name' => 'john']);

        $this->assertCount(2, $result->json());
    }
}
