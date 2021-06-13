<?php

namespace Wefabric\Address\Google;

use Wefabric\Address\Exception\StreetViewStaticApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class StreetViewStaticApi
{
    const URL = 'https://maps.googleapis.com/maps/api/streetview';

    const WIDTH = '1024';

    const HEIGHT = '1024';

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
    public function getThumbnailByLatLong(float $latitude, float $longitude, int $width = 0, int $height = 0, array $options = [])
    {
        return $this->getThumbnail($latitude.','.$longitude, $width, $height, $options);
    }

    /**
     * @param string $location
     * @param int $width
     * @param int $height
     * @param array $options
     * @return string
     * @throws StreetViewStaticApiException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getThumbnail(string $location, int $width = 0, int $height = 0, array $options = []): string
    {
        $defaultQueryParameters = [
            'fov' => 80,
            'pitch' => 0,
            'return_error_codes' => true
        ];

        if($width === 0) {
            $width = self::WIDTH;
        }

        if($height === 0) {
            $height = self::HEIGHT;
        }

        $queryParameters = array_replace_recursive($defaultQueryParameters, $options['query'] ?? []);

        $queryParameters['size'] = $width.'x'.$height;
        $queryParameters['location'] = $location;
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
