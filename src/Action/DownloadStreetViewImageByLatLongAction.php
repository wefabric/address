<?php


namespace Wefabric\Address\Action;


use Illuminate\Support\Facades\Storage;
use Wefabric\Address\Exception\AddressException;
use Wefabric\Address\Google\StreetViewStaticApi;

class DownloadStreetViewImageByLatLongAction
{
    /**
     * @param float $latitude
     * @param float $longitude
     * @param int $width
     * @param int $height
     * @param array $options
     * @return string
     * @throws AddressException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wefabric\Address\Exception\StreetViewStaticApiException
     */
    public function execute(float $latitude, float $longitude, int $width = 0, int $height = 0, array $options = []): string
    {
        return app(DownloadStreetViewImageAction::class)->execute($latitude.','.$longitude, $width, $height, $options);
    }
}
