<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Like;
use App\Models\LikeComment;
use App\Models\ReplayComment;
use App\Models\User;
use App\Models\Post;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
     User::factory(10)->create();


    }
}
