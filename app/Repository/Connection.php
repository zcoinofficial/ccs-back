<?php

namespace App\Repository;

use GuzzleHttp\Client;

class Connection
{
    private $repo;

    public function __construct(Client $client)
    {
        $url = env('REPOSITORY_URL') ?? env('GITLAB_URL');
        if (parse_url($url, PHP_URL_HOST) == 'github.com') {
            $this->repo = new Github($client, $url);
        } else {
            $this->repo = new Gitlab($client, $url);
        }
    }

    public function mergeRequests($state) {
        return $this->repo->mergeRequests($state);
    }

    public function getNewFiles($merge_request_iid) {
        return $this->repo->getNewFiles($merge_request_iid);
    }
}
