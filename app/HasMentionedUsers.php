<?php
/**
 * Created by PhpStorm.
 * User: joomartin
 * Date: 2018. 02. 15.
 * Time: 16:30
 */

namespace App;


use App\Notifications\YouWereMentioned;

trait HasMentionedUsers
{
    public function mentionedUsers(): array
    {
        preg_match_all('/@([\w\-]+)/', $this->body, $matches);

        return $matches[1];
    }

    public function notifyMentionedUsers()
    {
        User::whereIn('name', $this->mentionedUsers())
            ->get()
            ->each
            ->notify(new YouWereMentioned($this));
    }
}