<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;
use App\HasMentionedUsers;
use App\Notifications\YouWereMentioned;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyMentionedUsers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  $event
     * @return void
     */
    public function handle($event)
    {
        /**
         * @var HasMentionedUsers $event->model
         */
        $event->model->notifyMentionedUsers();
    }
}
