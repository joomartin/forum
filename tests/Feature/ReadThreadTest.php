<?php

namespace Tests\Feature;

use App\Reply;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReadThreadTest extends TestCase
{
    use DatabaseMigrations;

    protected $thread;

    protected function setUp()
    {
        parent::setUp();

        $this->thread = create('Thread');
    }


    /** @test */
    public function a_user_can_view_all_threads()
    {
        $this->get('/threads')
            ->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_view_a_single_thread()
    {
        $this->get($this->thread->path())
            ->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_filter_thread_by_a_channel()
    {
        $this->signIn();

        $channel = create('Channel');

        $threadInChannel = create('Thread', ['channel_id' => $channel->id]);
        $threadNotInChannel = create('Thread');

        $this->get('/threads/' . $channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_username()
    {
        $user = create('User', ['name' => 'JohnDoe']);
        $this->signIn($user);

        $threadByJohn = create('Thread', ['user_id' => $user->id]);
        $threadNotByJohn = create('Thread');

        $this->get("/threads?by={$user->name}")
            ->assertSee($threadByJohn->title)
            ->assertDontSee($threadNotByJohn->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_popularity()
    {
        $threadWithTwoReplies = create('Thread');
        create('Reply', ['thread_id' => $threadWithTwoReplies->id], 2);

        $threadWithThreeReplies = create('Thread');
        create('Reply', ['thread_id' => $threadWithThreeReplies->id], 3);

        $threadWithNoReplies = $this->thread;

        $response = $this->getJson('/threads?popular=1')->json();

        $this->assertEquals(
            [3, 2, 0],
            array_column($response, 'replies_count')
        );
    }

    /** @test */
    public function a_user_can_filter_threads_by_those_that_are_unanswered()
    {
        $thread = create('Thread');
        $reply = create('Reply', ['thread_id' => $thread->id]);

        $response = $this->getJson('/threads?unanswered=1')->json();

        $this->assertCount(1, $response);
    }

    /** @test */
    public function a_user_can_request_all_replies_for_a_thread_with_pagination()
    {
        $thread = create('Thread');
        create('Reply', ['thread_id' => $thread->id], 2);

        $response = $this->getJson($thread->path('replies'))->json();

        $this->assertCount(2, $response['data']);
        $this->assertEquals(2, $response['total']);
    }
}
