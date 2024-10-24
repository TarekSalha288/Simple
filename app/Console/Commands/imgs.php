<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class imgs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imgs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Images From Public';

    /**
     * Execute the console command.
     */
    public function handle()
    {
if(File::exists(public_path('imgs/'))){
            File::deleteDirectories(public_path('imgs/'));}

    }
}
