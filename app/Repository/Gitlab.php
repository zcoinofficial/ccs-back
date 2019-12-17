<?php

namespace App\Repository;

use GuzzleHttp\Client;

class MergeRequest implements Proposal
{
    private $merge_request;

    public function __construct($merge_request)
    {
        $this->merge_request = $merge_request;
    }

    public function iid() : int
    {
        return $this->merge_request->iid;
    }

    public function id() : int
    {
        return $this->merge_request->id;
    }

    public function url() : string
    {
        return $this->merge_request->web_url;
    }

    public function title() : string
    {
        return $this->merge_request->title;
    }

    public function author() : string
    {
        return $this->merge_request->author->username;
    }

    public function created_at() : int
    {
        return strtotime($this->merge_request->created_at);
    }
}

class Gitlab implements Repository
{
    private $client;
    private $base_url;
    private const stateToString = [ State::Opened => 'opened',
                                    State::Merged => 'merged'];

    public function __construct(Client $client, string $repository_url)
    {
        $this->client = $client;
        $this->base_url = $repository_url;
    }

    public function mergeRequests($state)
    {
        $url = $this->base_url . '/merge_requests?scope=all&per_page=50&state=' . Self::stateToString[$state];
        $response = $this->client->request('GET', $url);
        return collect(json_decode($response->getBody()))->map(function ($merge_request) {
            return new MergeRequest($merge_request);
        });
    }

    public function getNewFiles($merge_request)
    {
        $url = $this->base_url . '/merge_requests/' . $merge_request->iid() . '/changes';
        $response = $this->client->request('GET', $url);
        return collect(json_decode($response->getBody())->changes)->filter(function ($change) {
            return $change->new_file;
        })->map(function ($change) {
            return $change->new_path;
        });
    }
}
