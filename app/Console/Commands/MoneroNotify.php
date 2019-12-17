<?php

namespace App\Console\Commands;

class moneroNotify extends walletNotify
{
    private $coin;
    private $wallet;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monero:notify
                            {height? : Scan wallet transactions starting from the specified height}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the monero blockchain for transactions';
}
