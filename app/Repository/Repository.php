<?php

namespace App\Repository;

use GuzzleHttp\Client;

interface State
{
    const Merged = 0;
    const Opened = 1;
    const All = 2;
}

interface Proposal
{
    public function id() : int;
    public function url() : string;
    public function title() : string;
    public function author() : string;
    public function created_at() : int;
}

interface Repository
{
    public function __construct(Client $client, string $repository_url);
    public function mergeRequests($state);
    public function getNewFiles(Proposal $proposal);
}
