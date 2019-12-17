<?php

namespace App\Console\Commands;

use App\Coin\CoinAuto;
use App\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:addresses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates monero addresses for any merged proposals';

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
        $coin = CoinAuto::newCoin();
        $wallet = $coin->newWallet();

        $projects = Project::whereNotNull('filename')->whereNull('address')->where('state', 'FUNDING-REQUIRED')->get();
        foreach ($projects as $project) {
            $addressDetails = $wallet->getPaymentAddress();
            $project->address_uri = $wallet->createQrCodeString($addressDetails['address']);
            $project->address = $addressDetails['address'];
            $project->subaddr_index = $coin->subaddrIndex($addressDetails, $project);
            Storage::disk('public')->put("/img/qrcodes/{$project->subaddr_index}.png", $project->generateQrcode());
            $project->qr_code = "img/qrcodes/{$project->subaddr_index}.png";
            $project->raised_amount = 0;
            $project->save();

            $this->info('Project: ' . $project->filename . ', address: ' . $project->address);
        }

    }
}
