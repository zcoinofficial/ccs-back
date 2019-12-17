<?php

namespace App\Coin;

use Illuminate\Console\Command;

use Monero\WalletCommon;

interface Coin
{
    public function newWallet() : WalletCommon;
    public function onNotifyGetTransactions(Command $command, WalletCommon $wallet);
    public function subaddrIndex($addressDetails, $project);
}

class CoinAuto
{
    public static function newCoin() : Coin
    {
        $coin = env('COIN', 'monero');
        switch ($coin) {
            case 'monero':
                return new CoinMonero();
            case 'zcoin':
                return new CoinZcoin();
            default:
                throw new \Exception('Unsupported COIN ' . $coin);
        }
    }
}
