<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use Favoritable, RecordsActivity;

    protected $guarded = [];
    protected $with = ['owner', 'favorites'];
    protected $appends = ['favoritesCount', 'isFavorited', 'isBest'];

    protected static function boot()
    {
        parent::boot();

        static::created(function($reply) {
            $reply->thread->increment('replies_count');
        });

        static::deleted(function($reply) {
            $reply->thread->decrement('replies_count');
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function wasJustPublished()
    {
        return $this->created_at->gt(Carbon::now()->subMinute());
    }

    /**
     * Determine the path to the reply
     *
     * @return string
     */
    public function path()
    {
        return $this->thread->path() . "#reply-{$this->id}";
    }

    /**
     * Return the usernames mentioned in the body
     *
     * @return mixed
     */
    public function mentionedUsers()
    {
        preg_match_all('/\@([\w\-]+)/', $this->body, $matches);

        return $matches[1];
    }

    /**
     * Set the anchors to the profiles of the mentioned users
     *
     * @param string $body
     */
    public function setBodyAttribute($body)
    {
        $this->attributes['body'] = preg_replace('/@([\w\-]+)/', '<a href="/profiles/$1">$0</a>', $body);
    }

    /**
     * Determine if the current reply is marked as the best
     *
     * @return bool
     */
    public function isBest()
    {
        return $this->thread->best_reply_id == $this->id;
    }

    /**
     * @return bool
     */
    public function getIsBestAttribute()
    {
        return $this->isBest();
    }

    public function getBodyAttribute($body)
    {
        return \Purify::clean($body);
    }
}
