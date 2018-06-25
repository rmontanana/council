<?php

namespace App;

use App\Events\ThreadReceiveNewReply;
use App\Filters\ThreadFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Thread extends Model
{
    use RecordsActivity, Searchable;
    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $guarded = [];

    protected $with = ['creator', 'channel']; //Este no se puede deshabilitar el globalscope sí

    protected $appends = ['isSubscribedTo'];

    protected $casts = [
        'locked' => 'bool'
    ];

    protected static function boot()
    {
        parent::boot();

//        static::addGlobalScope('creator', function($builder) {
//            $builder->with('creator');
//        });

        static::deleting(function ($thread) {
           $thread->replies->each->delete();
        });

        static::created(function ($thread) {
            $thread->update(['slug' => $thread->title]);
        });
    }


    /**
     * Get a string path for the thread.
     *
     * @return string
     */
    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->slug}";
    }

    /**
     * A thread belongs to a creator.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A thread is assigned a channel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * A thread may have many replies.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * Add a reply to the thread.
     *
     * @param array $reply
     *
     * @return Model
     */
    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        event(new ThreadReceiveNewReply($reply));

        return $reply;

    }

    /**
     * Apply all relevant thread filters.
     *
     * @param  Builder       $query
     * @param  ThreadFilters $filters
     * @return Builder
     */
    public function scopeFilter($query, ThreadFilters $filters)
    {
        return $filters->apply($query);
    }

    /**
     * @param int|null $userId
     * @return $this
     */
    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id'   => $userId ?: auth()->id()
        ]);
        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    /**
     * @param null $userId
     */
    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();
    }

    /**
     * @return mixed
     */
    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function hasUpdatesFor($user)
    {
        $key = $user->visitedThreadCacheKey($this);

        return $this->updated_at > cache($key);
    }

    /**
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Set the proper slug attribute
     *
     * @param $value
     */
    public function setSlugAttribute($value)
    {
        $slug = str_slug($value);

        if (static::whereSlug($slug)->exists()) {
            $slug = "{$slug}-" . $this->id;
        }

        $this->attributes['slug'] = $slug;
    }

    public function markBestReply(Reply $reply)
    {
        $this->best_reply_id = $reply->id;

        $this->save();
    }

    public function toSearchableArray()
    {
        return $this->toArray() + ['path' => $this->path()];
    }

    public function getBodyAttribute($body)
    {
        return \Purify::clean($body);
    }
}
