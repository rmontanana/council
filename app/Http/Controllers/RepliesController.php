<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Reply;
use App\Thread;
use \Exception;

class RepliesController extends Controller
{
    /**
     * RepliesController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    /**
     * Fetch all relevant replies
     *
     * @param        $channelId
     * @param Thread $thread
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(10);
    }

    /**
     * Persists a new reply
     *
     * @param                $channel_id
     * @param Thread         $thread
     * @param CreatePostRequest $form
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($channel_id, Thread $thread, CreatePostRequest $form)
    {
        if ($thread->locked) {
            return response('Thread is locked', 422);
        }
        
        return $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ])->load('owner');
    }

    /**
     * Deletes the given reply
     *
     * @param Reply $reply
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws Exception
     */
    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if (request()->expectsJson()) {
            return response(['status' => 'Reply deleted']);
        }

        return back();
    }

    /**
     * Update an existing reply
     *
     * @param Reply $reply
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        $data = request()->validate(['body' => 'required|spamfree']);
        $reply->update($data);
    }
}
