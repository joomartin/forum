<?php

namespace Tests\Feature;

use App\Activity;
use App\Channel;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateThreadTest extends TestCase
{
    use DatabaseMigrations;

    public function publishThread(array $attrs = [])
    {
        $this->withExceptionHandling()
            ->signIn();

        $thread = make('Thread', $attrs);

        return $this->post('/threads', $thread->toArray());
    }

    /** @test */
    public function guest_not_have_permission_to_create_thread()
    {
        $this->withExceptionHandling();

        $this->get('/threads/create')
            ->assertRedirect('/login');

        $this->post('/threads')
            ->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_can_create_new_threads()
    {
        // have a signed in user
        $this->signIn();

        // we hit endpoint
        $thread = make('Thread');
        $response = $this->post('/threads', $thread->toArray());

        // visit thread page should see thread
        $this->get($response->headers->get('Location'))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    /** @test */
    public function a_thread_requires_a_title()
    {
        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_thread_requires_a_body()
    {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function a_thread_requires_a_valid_channel()
    {
        factory(Channel::class, 2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }

    /** @test */
    public function unauthorized_users_may_not_delete_threads()
    {
        $this->withExceptionHandling();

        $thread = create('Thread');

        $this->delete($thread->path())->assertRedirect('/login');

        $this->signIn();
        $this->delete($thread->path())->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_delete_threads()
    {
        // Given we have a thread and a reply
        $this->signIn();

        $thread = create('Thread', ['user_id' => auth()->id()]);
        $reply = create('Reply', ['thread_id' => $thread->id]);

        // When we delete the thread
        $response = $this->json('DELETE', $thread->path());
        $response->assertStatus(204);

        // Then both the thread and the reply are missing
        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        // And created_ activities are deleted
        $this->assertDatabaseMissing('activities', [
            'subject_id'    => $thread->id,
            'subject_type'  => get_class($thread),
            'type'          => 'created_thread'
        ]);

        $this->assertDatabaseMissing('activities', [
            'subject_id'    => $reply->id,
            'subject_type'  => get_class($reply),
            'type'          => 'created_reply'
        ]);

        // And the only activity is deleted_thread
        $this->assertDatabaseHas('activities', [
            'subject_id'    => $thread->id,
            'subject_type'  => get_class($thread),
            'type'          => 'deleted_thread'
        ]);

        $this->assertEquals(1, Activity::count());
    }
}
