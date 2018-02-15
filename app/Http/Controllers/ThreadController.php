<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Events\ThreadWasPublished;
use App\Filters\ThreadFilter;
use App\Rules\SpamFree;
use App\Thread;
use App\ThreadSubscription;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ThreadController extends Controller
{
    /**
     * ThreadController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth')->except( ['index', 'show']);
    }

    public function index(Channel $channel, ThreadFilter $filters)
    {
        $threads = $this->getThreads($channel, $filters);

        if (request()->wantsJson())
            return $threads;

        return view('threads.index', compact('threads'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('threads.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'title' => ['required', new SpamFree],
            'body' => ['required', new SpamFree],
            'channel_id' => 'required|exists:channels,id',
        ]);

        $thread = Thread::create([
            'user_id'       => auth()->id(),
            'channel_id'    => $request->channel_id,
            'title'         => $request->title,
            'body'          => $request->body
        ]);

        event(new ThreadWasPublished($thread));
        $thread->subscriptions()->create(['user_id' => auth()->id()]);

        return redirect($thread->path())
            ->with('flash', 'Your thread has been published');
    }

    public function show(string $channelId, Thread $thread)
    {
//        $thread->append('isSubscribedTo');

        if (auth()->check()) {
            auth()->user()->read($thread);
        }

        return view('threads.show', compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Thread  $thread
     * @return Response
     */
    public function update(Request $request, Thread $thread)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return Response
     */
    public function destroy(Channel $channel, Thread $thread)
    {
        $this->authorize('update', $thread);

        $thread->delete();

        if (request()->wantsJson()) {
            return response([], 204);
        }

        return redirect('/threads');
    }

    protected function getThreads(Channel $channel, ThreadFilter $filters): Collection
    {
        $threads = Thread::filter($filters)->latest();

        if ($channel->exists) {
            $threads = $threads->where('channel_id', $channel->id);
        }

        return $threads->get();
    }
}
