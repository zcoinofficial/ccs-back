<?php

namespace App\Console\Commands;

use App\Deposit;
use Illuminate\Console\Command;

class depositList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deposit:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Print all deposits in JSON format';

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
        $this->info(Deposit::all()->toJson(JSON_PRETTY_PRINT));
    }

}
