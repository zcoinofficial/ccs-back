<?php

namespace App\Coin;

use Illuminate\Console\Command;

use Monero\WalletCommon;
use Monero\WalletOld;

class CoinMonero implements Coin
{
    public function newWallet() : WalletCommon
    {
        return new WalletOld();
    }

    public function onNotifyGetTransactions(Command $command, WalletCommon $wallet)
    {
        $min_height = $command->argument('height') ?? Deposit::max('block_received');
        return $wallet->scanIncomingTransfers(max($min_height, 50) - 50);
    }

    public function subaddrIndex($addressDetails, $project)
    {
        return $addressDetails['subaddr_index'];
    }
}
