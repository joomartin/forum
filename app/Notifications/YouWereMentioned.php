<?php

namespace App\Notifications;

use App\Model;
use App\Reply;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class YouWereMentioned extends Notification
{
    use Queueable;

    /**
     * @var Model
     */
    protected $model;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Model $model)
    {
        //
        $this->model = $model;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $message = $this->model instanceof Reply
            ? $this->model->owner->name . ' mentioned you in ' . $this->model->thread->title
            : $this->model->creator->name . ' mentioned you in ' . $this->model->title;

        $link = $this->model->path();

        return compact('message', 'link');
    }
}
