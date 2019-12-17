<?php

namespace App\Console\Commands;

use App\Coin\CoinAuto;
use App\Deposit;
use App\Project;
use Illuminate\Console\Command;
use Monero\Transaction;

class walletNotify extends Command
{
    private $coin;
    private $wallet;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the blockchain for transactions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->coin = CoinAuto::newCoin();
        $this->wallet = $this->coin->newWallet();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $blockheight = $this->wallet->blockHeight();
        if ($blockheight < 1) {
            $this->error('failed to fetch blockchain height');

            return;
        }

        $transactions = $this->coin->onNotifyGetTransactions($this, $this->wallet);
        $transactions->each(function ($transaction) {
            $this->processPayment($transaction);
        });

        $this->updateAllConfirmations($blockheight);
    }

    /**
     * @param Transaction $transaction
     *
     * @return null|void
     */
    public function processPayment(Transaction $transaction)
    {
        $amountCoins = $transaction->amount / 10 ** $this->wallet->digitsAfterTheRadixPoint();
        $details = 'address: ' . $transaction->address . ' amount: '. $amountCoins . ' txid: '.$transaction->id;

        $deposit = Deposit::where('tx_id', $transaction->id)->where('subaddr_index', $transaction->subaddr_index)->first();
        if ($deposit) {
            if ($deposit->block_received == 0) {
                $deposit->block_received = $transaction->block_height;
                $deposit->save();
            }
            return null;
        }

        $this->createDeposit($transaction);

        if ($project = Project::where('subaddr_index', $transaction->subaddr_index)->first()) {
            // update the project total
            $project->raised_amount = $project->raised_amount + $amountCoins;
            $project->save();
            $this->info('Donation to "' . $project->filename . '" '. $details);
        } else {
            $this->error('Unrecognized donation, ' . $details);
        }

        return;
    }

    /**
     * Adds confirmations on for all xmr transactions with confirmations below 50
     *
     * @param int blockheight
     *
     * @return int
     */
    public function updateAllConfirmations($blockheight)
    {
        $count = 0;
        //update all xmr deposit confirmations
        Deposit::where('confirmations', '<', 50)
            ->where('block_received', '>', 0)
            ->each(function ($deposit) use ($blockheight, &$count) {
                $this->updateConfirmation($blockheight, $deposit);
                $count++;
            });

        return $count;
    }

    /**
     * Updates the confirmations for the deposit and calls the process method if it is not assigned to a payflow
     *
     * @param $blockheight
     * @param Deposit $deposit
     *
     * @return bool
     */
    public function updateConfirmation($blockheight, Deposit $deposit)
    {
        $diff = $blockheight - $deposit->block_received + 1;
        $deposit->confirmations = $diff;
        $deposit->save();

        return false;
    }

    /**
     * Creates a deposit entry in the deposit table
     *
     * @param Transaction $transaction
     *
     * @return Deposit
     */
    public function createDeposit(Transaction $transaction)
    {
        $deposit = new Deposit;
        $deposit->tx_id = $transaction->id;
        $deposit->amount = $transaction->amount;
        $deposit->confirmations = $transaction->confirmations;
        $deposit->subaddr_index = $transaction->subaddr_index;
        $deposit->time_received = $transaction->time_received;
        $deposit->block_received = $transaction->block_height;
        $deposit->save();
    }

}
