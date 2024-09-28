<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class StarWarsApiService
{
    private const BASE_URL = 'https://swapi.py4e.com/api/people/';
    private const ITEM = 'ITEM';
    private const COLLECTION = 'COLLECTION';

    public function __construct(
        private readonly HttpClientInterface $httpClient
    )
    {

    }

    public function getPersonnages(): array
    {
// Initialize an array to hold all personnages.
        $allPersonnages = [];
        $url = self::BASE_URL; // Start with the first page.

        // Loop to get all pages of people.
        do {
            $response = $this->httpClient->request('GET', $url, []);
            $data = $response->toArray();

            // Merge the current page of people into the full list.
            $allPersonnages = array_merge($allPersonnages, $data['results']);

            // Check if there is a next page.
            $url = $data['next']; // Set the URL to the next page.
        } while ($url); // Continue as long as there is a next page.

        return $allPersonnages;
    }

    public function getPersonnage(int $id): array
    {
        return $this->makeRequest(self::ITEM, $id);
    }

    private function makeRequest(string $type, ?int $id = null): array
    {
        $url = $id ? self::BASE_URL . $id :
            self::BASE_URL;

        $response = $this->httpClient->request('GET', $url, []);

        $data = match ($type) {
            self::COLLECTION => $response->toArray() ['results'],
            self::ITEM => $response->toArray(),
        };
        return $data;
    }
}

