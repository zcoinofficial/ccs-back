<?php

namespace Monero\Contracts;

/**
 * Interface WalletManager
 * @package Monero
 */
interface WalletManager
{
    /**
     * Gets the balance
     *
     * @return int the overall value after inputs unlock
     */
    public function balance();

    /**
     * Gets the unlocked balance
     *
     * @return int the spendable balance
     */
    public function unlockedBalance();

    /**
     * Gets the primary address
     *
     * @return string wallets primary address
     */
    public function address();

    /**
     * Gets the current block height
     *
     * @return int block height
     */
    public function blockHeight();

    /**
     * Creates a new subaddress
     *
     * @return array ['address', 'address_index']
     */
    public function createSubaddress();

    /**
     * Gets any incoming transactions
     *
     * @return array
     */
    public function incomingTransfers();

    /**
     * Checks for any payments made to the paymentIds
     *
     * @param array     $paymentIds list of payment ids to be searched for
     * @param int       $minHeight  the lowest block the search should start with
     *
     * @return array    payments received since min block height with a payment id provided
     */
    public function payments($paymentIds, $minHeight);

    /**
     * creates a uri for easier wallet parsing
     *
     * @param string    $address    address comprising of primary, sub or integrated address
     * @param int       $amount     atomic amount requested
     * @param string    $paymentId  payment id when not using integrated addresses
     *
     * @return string the uri string which can be used to generate a QR code
     */
    public function createUri($address, $amount = null, $paymentId = null);

    /**
     * creates a random 64 char payment id
     *
     * @return string
     */
    public function generatePaymentId();

}
