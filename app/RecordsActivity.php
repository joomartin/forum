<?php
/**
 * Created by PhpStorm.
 * User: joomartin
 * Date: 2018. 02. 05.
 * Time: 18:36
 */

namespace App;

trait RecordsActivity
{
    protected static function bootRecordsActivity()
    {
        if (auth()->guest()) return;

        foreach (static::getRecordEvents() as $event) {
            static::$event(function ($model) use ($event) {
                $model->recordActivity($event);
            });
        }

        // Csak model instance -eken fut le! Tehát $reply->favorites()->where()->delete() esetén nem működik, az csak egy sima sql, helyette:
        // $this->favorites()->where->get()->each->delete();
        static::deleting(function ($model) {
             $model->activities()->delete();
        });
    }

    protected static function getRecordEvents()
    {
        return ['created'];
    }

    protected function recordActivity(string $event): void
    {
        $this->activities()->create([
            'user_id'   => auth()->id(),
            'type'      => $this->getActivityType($event),
        ]);
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    protected function getActivityType(string $event): string
    {
        $type = strtolower(class_basename($this));
        return "{$event}_{$type}";
    }
}