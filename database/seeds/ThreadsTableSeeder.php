<?php

use Illuminate\Database\Seeder;

class ThreadsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [];
        $threads = [];

        for ($i = 0; $i < 50; $i++) {
            $users[] = factory(\App\User::class)->create([
                'email'     => "user.{$i}@example.com",
                'password'  => bcrypt('asdfasdf')
            ]);

            $threads[] = factory(App\Thread::class)->create([
                'user_id'   => collect($users)->random()->id
            ]);
        }

        collect($threads)
            ->map(function ($t) use ($users) {
                return [
                    'thread_id' => $t->id,
                    'user_id'   => collect($users)->random()->id
                ];
            })
            ->each(function ($attr) {
                factory(App\Reply::class, rand(0, 20))->create($attr);
            });
    }
}
