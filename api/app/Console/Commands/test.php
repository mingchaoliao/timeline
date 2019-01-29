<?php

namespace App\Console\Commands;

use App\Timeline\Domain\Services\EventService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = [
            'attr1' => '2015'
        ];

        dd(
            Validator::make($data, [
                'attr1' => 'required|date'
            ])->errors()
        );
    }
}
