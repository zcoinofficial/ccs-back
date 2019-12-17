<?php

namespace Monero;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

/**
 * Class jsonRPCClient
 * JSON 2.0 RPC Client for cryptocurrency wallet
 */
class jsonRPCClient implements Contracts\WalletManager
{
    private $rpc;

    /**
     * JsonRPCClient constructor.
     * @param array $options
     * @param null $client
     */
    public function __construct($options, $client = null)
    {
        $this->rpc = new jsonRpcBase($options, $client);
    }

    /**
     * Gets the balance
     *
     * @return int the overall value after inputs unlock
     */
    public function balance() : int
    {
        $response = $this->rpc->request('get_balance');
        return $response['balance'];
    }

    /**
     * Gets the unlocked balance
     *
     * @return int the spendable balance
     */
    public function unlockedBalance() : int
    {
        $response = $this->rpc->request('get_balance');
        return $response['unlocked_balance'];
    }

    /**
     * Gets the primary address
     *
     * @return string wallets primary address
     */
    public function address() : string
    {
        $response = $this->rpc->request('get_address');
        return $response['address'];
    }

    /**
     * Gets the current block height
     *
     * @return int block height
     */
    public function blockHeight() : int
    {
        $response = $this->rpc->request('get_height');
        return $response['height'];
    }

    /**
     * Creates a new subaddress
     *
     * @param int       $account_index  account index to create subaddress (maajor index)
     * @param string    $label          label to assign to new subaddress
     * 
     * @return array ['address', 'address_index']
     */
    public function createSubaddress($account_index = 0, $label = '') : array
    {
        $response = $this->rpc->request('create_address', ['account_index' => $account_index, 'label' => $label]);
        return $response;
    }

    /**
     * Gets any incoming transactions
     *
     * @return array
     */
    public function incomingTransfers($min_height = 0) : array
    {
        $response = $this->rpc->request('get_transfers', ['pool' => true, 'in' => true, 'min_height' => $min_height, 'filter_by_height' => $min_height > 0 ? true : false]);

        return $response;
    }

    /**
     * Checks for any payments made to the paymentIds
     *
     * @param array     $paymentIds list of payment ids to be searched for
     * @param int       $minHeight  the lowest block the search should start with
     *
     * @return array    payments received since min block height with a payment id provided
     */
    public function payments($paymentIds, $minHeight) : array
    {
        $response = $this->rpc->request('get_bulk_payments', ['payment_ids' => $paymentIds, 'min_block_height' => $minHeight]);

        return $response;
    }

    /**
     * creates a uri for easier wallet parsing
     *
     * @param string    $address    address comprising of primary, sub or integrated address
     * @param int       $amount     atomic amount requested
     * @param string    $paymentId  payment id when not using integrated addresses
     *
     * @return string the uri string which can be used to generate a QR code
     */
    public function createUri($address, $amount = null, $paymentId = null) : string
    {
        $response = $this->rpc->request('make_uri', ['address' => $address, 'amount' => $amount, 'payment_id' => $paymentId]);

        return $response['uri'];
    }

    /**
     * creates a random 64 char payment id
     *
     * @return string
     */
    public function generatePaymentId(): string
    {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }
}
