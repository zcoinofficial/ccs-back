<?php

namespace App\Repository;

use GuzzleHttp\Client;

class PullRequest implements Proposal
{
    private $pull_request;

    public function __construct($pull_request)
    {
        $this->pull_request = $pull_request;
    }

    public function id() : int
    {
        return $this->pull_request->number;
    }

    public function url() : string
    {
        return $this->pull_request->html_url;
    }

    public function title() : string
    {
        return $this->pull_request->title;
    }

    public function author() : string
    {
        return $this->pull_request->user->login;
    }

    public function created_at() : int
    {
        return strtotime($this->pull_request->created_at);
    }
}

class Github implements Repository
{
    private $client;
    private $options = [];
    private $owner_repo;
    private const stateToString = [ State::Opened => 'open' ,
                                    State::Merged => 'closed'];

    public function __construct(Client $client, string $repository_url)
    {
        $this->client = $client;
        $this->owner_repo = parse_url($repository_url, PHP_URL_PATH);
        if ($token = env('GITHUB_ACCESS_TOKEN')) {
            $this->options = ['headers' => ['Authorization' => 'token ' . $token]];
        }
    }

    private function getUrl($url)
    {
      return $this->client->request('GET', $url, $this->options);
    }

    public function mergeRequests($state)
    {
        $url = 'https://api.github.com/repos' . $this->owner_repo . '/pulls?state=' . Self::stateToString[$state];
        $response = $this->getUrl($url);
        $result = collect(json_decode($response->getBody()));
        if ($state == State::Merged) {
            $result = $result->filter(function ($pull_request) {
                return $pull_request->merged_at !== null;
            });
        }
        return $result->map(function ($pull_request) {
            return new PullRequest($pull_request);
        });
    }

    public function getNewFiles($pull_request)
    {
        $url = 'https://api.github.com/repos' . $this->owner_repo . '/pulls/' . $pull_request->id() . '/files';
        $response = $this->getUrl($url);
        return collect(json_decode($response->getBody()))->filter(function ($change) {
            return $change->status == 'added';
        })->map(function ($change) {
            return $change->filename;
        });
    }
}
