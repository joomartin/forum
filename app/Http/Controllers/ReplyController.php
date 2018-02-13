<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Inspections\Spam;
use App\Reply;
use App\Thread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    /**
     * ReplyController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    public function index(Channel $channel, Thread $thread)
    {
        return $thread->replies()->paginate(5);
    }

    public function store($channelId, Thread $thread)
    {
        try {
            $this->validateReply();

            $reply = $thread->addReply([
                'body'      => request('body'),
                'user_id'   => auth()->id()
            ]);
        } catch (\Exception $ex) {
            return response('Sorry, your reply could not be saved.', 422);
        }

        return $reply->load('owner');
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
        try {
            $this->authorize('update', $reply);
            $this->validateReply();
        } catch (\Exception $ex) {
            return response('Sorry, your reply could not be saved.', 422);
        }

        $reply->update(request(['body']));
    }

    protected function validateReply()
    {
        $this->validate(request(), ['body' => 'required']);

        resolve(Spam::class)->detect(request('body'));
    }
}
