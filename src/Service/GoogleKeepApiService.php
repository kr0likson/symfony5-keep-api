<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleKeepApiService
{
    private HttpClientInterface $httpClient;

    public function __construct()
    {
        $this->httpClient = HttpClient::create();
    }

    public function getListOfNotes(string $accessToken)
    {
        $url = 'https://keep.googleapis.com/v1/notes';
        $headers = [
            'Authorization' => 'Bearer ' . $accessToken,
        ];
        $response = $this->httpClient->request(Request::METHOD_GET, $url, ['headers' => $headers]);
        if ($response->getStatusCode() === 200) {
            return $response->toArray();
        } else {
        }
    }
}