<?php

namespace App\Coin;

use App\Deposit;
use App\Project;
use Illuminate\Console\Command;

use Monero\WalletCommon;
use Monero\WalletZcoin;

class CoinZcoin implements Coin
{
    public function newWallet() : WalletCommon
    {
        return new WalletZcoin();
    }

    public function onNotifyGetTransactions(Command $command, WalletCommon $wallet)
    {
        $skip_txes = Deposit::whereNotNull('tx_id')->where('confirmations', '>', 10)->count();
        return $wallet->scanIncomingTransfers($skip_txes)->each(function ($tx) {
            $project = Project::where('address', $tx->address)->first();
            if ($project) {
                $tx->subaddr_index = $project->subaddr_index;
            }
        });
    }

    public function subaddrIndex($addressDetails, $project)
    {
        return $project->id;
    }
}
