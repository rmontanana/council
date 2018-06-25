<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        Factory('App\Thread', 50)->create();
        $thread = \App\Thread::all()->last();
        Factory('App\Reply', 30)->create(['thread_id' => $thread->id]);
    }
}
