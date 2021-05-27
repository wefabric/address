<?php

namespace Wefabric\Address\Google;

use Wefabric\Address\Exception\StreetViewStaticApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class StreetViewStaticApi
{
    const URL = 'https://maps.googleapis.com/maps/api/streetview';

    public ?Client $client = null;

    /**
     * @return StreetViewStaticApi
     */
    public static function make(): StreetViewStaticApi
    {
        return new self();
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        if(!$this->client) {
            $this->setClient(new Client());
        }

        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @param int $width
     * @param int $height
     * @param array $options
     * @return string
     * @throws StreetViewStaticApiException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getThumbnail(float $latitude, float $longitude, int $width = 1024, int $height = 1024, array $options = []): string
    {
        $defaultQueryParameters = [
            'fov' => 90,
            'pitch' => 0,
            'return_error_codes' => true
        ];

        $queryParameters = array_replace_recursive($defaultQueryParameters, $options['query'] ?? []);

        $queryParameters['size'] = $width.'x'.$height;
        $queryParameters['location'] = $latitude.','.$longitude;
        $queryParameters['key'] = config('address.google.api_key');
        //$queryParameters['signature'] = '';

        try {
            $response = $this->getClient()->get($this->createUrl($queryParameters));
            $result = (string)$response->getBody();
        } catch (RequestException $e) {
            throw new StreetViewStaticApiException($e->getMessage(), $e->getCode());
        }

        return $result;
    }

    /**
     * @param array $queryParameters
     * @return string
     */
    public function createUrl(array $queryParameters): string
    {
        return self::URL.'?'.http_build_query($queryParameters);
    }
}
