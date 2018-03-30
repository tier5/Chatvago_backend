<?php

namespace App\Console\Commands\JWTFixes;

class JWTGenerateCommand extends \Tymon\JWTAuth\Commands\JWTGenerateCommand
{
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->fire();
    }
}
