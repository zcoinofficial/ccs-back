<?php

namespace Monero;

class Transaction
{
    public $amount;

    public $timeReceived;

    public $time;

    public $address;

    public $id;

    public $confirmations;

    public $subaddr_index;

    public $block_height;

    /**
     * Transaction constructor.
     *
     * @param $id
     * @param $amount
     * @param $address
     * @param $confirmations
     * @param $time
     * @param $timeReceived
     * @param $paymentId
     */
    public function __construct($id, $amount, $address, $confirmations, $time, $timeReceived, $subaddr_index = null, $blockheight = null)
    {
        $this->amount = $amount;
        $this->time_received = $timeReceived;
        $this->time = $time;
        $this->address = $address;
        $this->id = $id;
        $this->confirmations = $confirmations;
        $this->subaddr_index = $subaddr_index;
        $this->block_height = $blockheight;
        $this->correctTimeRecieved();
    }

    /**
     * Adds a time recieved if we did not get one. its a fix for some coins
     *
     * @param $tx_time_received
     * @param $tx_time
     *
     * @return int
     */
    protected function correctTimeRecieved()
    {
        if (isset($this->time_received) && $this->time_received != null) { //fix for coins that only have time but no time_received
            $timereceived = $this->time_received;
        } else {
            $timereceived = $this->time;
        }
        $this->time_received = $timereceived;
    }
}
