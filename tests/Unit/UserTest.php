<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_user_can_fetch_their_most_recent_reply()
    {
        $user = create('App\User');
        $reply = create('App\Reply', ['user_id' => $user->id]);

        $this->assertEquals($user->lastReply->id, $reply->id);
    }

    /** @test */
    function it_can_fetch_all_mentioned_users_starting_with_the_given_characters()
    {
        create('App\User', ['name' => 'johndoe']);
        create('App\User', ['name' => 'johndoe2']);
        create('App\User', ['name' => 'janedoe']);
        $results = $this->json('GET', '/api/users', ['name' => 'john']);

        $this->assertCount(2, $results->json());
    }

    /** @test */
    function a_user_can_determine_their_avatar_path()
    {
        $user = create('App\User');

        $this->assertEquals(asset('images/avatars/default.jpg'), $user->avatar_path);

        $user->avatar_path = 'avatars/me.jpg';

        $this->assertEquals(asset('avatars/me.jpg'), $user->avatar_path);

    }
}