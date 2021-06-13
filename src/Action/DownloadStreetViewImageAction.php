<?php


namespace Wefabric\Address\Action;


use Illuminate\Support\Facades\Storage;
use Wefabric\Address\Exception\AddressException;
use Wefabric\Address\Google\StreetViewStaticApi;

class DownloadStreetViewImageAction
{
    /**
     * @param string $location
     * @param int $width
     * @param int $height
     * @param array $options
     * @return string
     * @throws AddressException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wefabric\Address\Exception\StreetViewStaticApiException
     */
    public function execute(string $location, int $width = 0, int $height = 0, array $options = []): string
    {
        $streetViewActive = config('address.google.street_view_active');

        if(!$streetViewActive) {
            throw new AddressException('Google StreetView is not active. Set your GOOGLE_STREET_VIEW_ACTIVE to true in your environment variable and add a valid GOOGLE_API_KEY');
        }

        if(isset($options['cached']) && $options['cached'] === true) {
            return $this->getFromCache($location, $width, $height, $options);
        }

        return StreetViewStaticApi::make()->getThumbnail($location, $width, $height, $options);

    }

    /**
     * @param string $location
     * @param int $width
     * @param int $height
     * @param array $options
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wefabric\Address\Exception\StreetViewStaticApiException
     */
    private function getFromCache(string $location, int $width = 0, int $height = 0, array $options = []): string
    {
        $path = config('address.google.street_view_cache_path');
        $path .= '/'.base64_encode($location).'-'.$width.'x'.$height.'.jpg';

        if(Storage::exists($path)) {
            return Storage::get($path);
        }

        if(!$result = StreetViewStaticApi::make()->getThumbnail($location, $width, $height, $options)) {
            return '';
        }

        Storage::put($path, $result);
        return $result;
    }
}
