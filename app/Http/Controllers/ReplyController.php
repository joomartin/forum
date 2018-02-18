<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Http\Requests\CreateReplyRequest;
use App\Notifications\YouWereMentioned;
use App\Reply;
use App\Rules\SpamFree;
use App\Thread;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class ReplyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    public function index(Channel $channel, Thread $thread)
    {
        return $thread->replies()->paginate(5);
    }

    public function store($channelId, Thread $thread, CreateReplyRequest $form)
    {
        /**
         * @event ThreadReceivedNewReply
         */
        return $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ])->load('owner');
    }

    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if (request()->expectsJson()) {
            return response(['status' => 'Reply deleted'], 200);
        }

        return back();
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        $this->validate(request(), [
            'body' => ['required', new SpamFree]
        ]);

        $reply->update(request(['body']));
    }
}
