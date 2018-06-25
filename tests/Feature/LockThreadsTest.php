<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class LockThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function non_administrators_may_not_lock_trheads()
    {
        $this->signIn();

        $this->withExceptionHandling();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store', $thread))->assertStatus(403);

        $this->assertFalse($thread->fresh()->locked);

    }

    /** @test */
    function administrators_can_lock_threads()
    {
        $this->signIn(factory('App\User')->states('administrator')->create());//En User.php se ha puesto JohnDoe a piñón

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store', $thread))->assertStatus(200);

        $this->assertTrue($thread->fresh()->locked);
    }

    /** @test */
    function administrators_can_unlock_threads()
    {
        $this->signIn(factory('App\User')->states('administrator')->create());//En User.php se ha puesto JohnDoe a piñón

        $thread = create('App\Thread', ['user_id' => auth()->id(), 'locked' => true]);

        $this->delete(route('locked-threads.destroy', $thread))->assertStatus(200);

        $this->assertFalse($thread->fresh()->locked);
    }

    /** @test */
    public function once_locked_a_thread_may_not_receive_new_replies()
    {
        $this->signIn();

        $this->withExceptionHandling();

        $thread = create('App\Thread', ['locked' => true]);

        $this->post(route('locked-threads.store', $thread), [
            'body' => 'Foobar',
            'user_id' => auth()->id()
        ])->assertStatus(403);
    }
}