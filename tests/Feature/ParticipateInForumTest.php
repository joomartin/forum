<?php

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function unauthenticated_users_may_not_add_replies()
    {
        $this->withExceptionHandling()
            ->post('/threads/some-channel/1/replies', [])
            ->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_may_participate_in_forum_threads()
    {
        $user = create('User');
        $this->be($user);

        $thread = create('Thread');
        $reply = make('Reply');

        $this->post($thread->path('replies'), $reply->toArray());

        $this->assertDatabaseHas('replies', [
            'body' => $reply->body
        ]);
        $this->assertEquals(1, $thread->fresh()->replies_count);
    }

    /** @test */
    public function a_reply_requires_a_body()
    {
        $this->withExceptionHandling()->signIn();

        $thread = create('Thread');
        $reply = make('Reply', ['body' => null]);

        $this->post($thread->path('replies'), $reply->toArray())
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function unauthorized_users_cannot_delete_replies()
    {
        $this->withExceptionHandling();

        $reply = create('Reply');

        $this->delete('/replies/' . $reply->id)
            ->assertRedirect('/login');

        $this->signIn()
            ->delete('/replies/' . $reply->id)
            ->assertStatus(403);

        $this->assertDatabaseHas('replies', ['id' => $reply->id]);
    }

    /** @test */
    public function authorized_users_can_delete_replies()
    {
        $user = create('User');
        $this->signIn($user);

        $reply = create('Reply', ['user_id' => $user->id]);

        $this->delete('/replies/' . $reply->id)
            ->assertStatus(302);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertEquals(0, $reply->thread->fresh()->replies_count);
    }

    /** @test */
    public function authorized_users_can_update_reply()
    {
        $user = create('User');
        $this->signIn($user);

        $reply = create('Reply', ['user_id' => $user->id]);

        $body = 'Edited Body';
        $this->patch('/replies/' . $reply->id, ['body' => '' . $body . ''])
            ->assertStatus(200);

        $this->assertDatabaseHas('replies', [
            'id' => $reply->id,
            'body' => $body
        ]);
    }

    /** @test */
    public function unauthorized_users_cannot_update_replies()
    {
        $this->withExceptionHandling();

        $reply = create('Reply');

        $this->patch('/replies/' . $reply->id)
            ->assertRedirect('/login');

        $this->signIn()
            ->patch('/replies/' . $reply->id)
            ->assertStatus(403);

        $this->assertDatabaseHas('replies', ['id' => $reply->id]);
    }

    /** @test */
    public function replies_that_contain_spam_may_not_be_created()
    {
        $this->signIn();

        $thread = create('Thread');
        $reply = make('Reply', [
            'body'  => 'Yahoo Customer Support'
        ]);

        $this->expectException(\Exception::class);

        $this->post($thread->path('replies'), $reply->toArray());
    }
}
