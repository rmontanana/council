<?php

namespace App;

trait Favoritable
{
    protected static function bootFavoritable()
    {
        static::deleting(function($model) {
            $model->favorites->each->delete();
        });
    }

    /**
     * @return mixed
     */
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorited');
    }

    /**
     * @return mixed
     */
    public function favorite()
    {
        $attributes = ['user_id' => auth()->id()];
        if (! $this->favorites()->where($attributes)->exists()) {
            return $this->favorites()->create($attributes);
        }
    }

    /**
     * @return mixed
     */
    public function unFavorite()
    {
        $attributes = ['user_id' => auth()->id()];

        $this->favorites()->where($attributes)->get()->each->delete();
    }

    /**
     * @return bool
     */
    public function isFavorited()
    {
        return !! $this->favorites->where('user_id', auth()->id())->count();
    }

    /**
     * @return mixed
     */
    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }

    /**
     * @return bool
     */
    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }
}