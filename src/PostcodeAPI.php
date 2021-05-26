<?php


namespace Wefabric\Address;

use Illuminate\Support\Facades\Storage;
use nickurt\PostcodeApi\Entity\Address;
use nickurt\PostcodeApi\ProviderFactory as PostcodeApiProvider;
use nickurt\PostcodeApi\Providers\Provider;

class PostcodeAPI
{
    const CACHE_PATH = '/postcode-api/%s%s.json';

    /**
     * @var null|Provider
     */
    protected ?Provider $provider = null;

    /**
     * @param string $postcode
     * @param string $houseNumber
     * @return \nickurt\PostcodeApi\Entity\Address
     */
    public function get(string $postcode, string $houseNumber, bool $cache): ?Address
    {
        if($cache && $result = $this->getFromCache($postcode, $houseNumber)) {
            return $result;
        }
        $result = $this->getApi()->findByPostcodeAndHouseNumber($postcode, $houseNumber);

        if(null === $result->getStreet() && null === $result->getHouseNo()) {
            return null;
        }

        if($cache) {
            $this->saveToCache($postcode, $houseNumber, $result);
        }
        return $result;
    }

    /**
     * @return \nickurt\PostcodeApi\Providers\Provider
     */
    public function getApi(): Provider
    {
        if(!$this->provider) {
            $this->provider = PostcodeApiProvider::create('Pro6PP_NL');
        }
        return $this->provider;
    }

    /**
     * @param string $postcode
     * @param string $houseNumber
     * @param Address $address
     * @return bool
     */
    public function saveToCache(string $postcode, string $houseNumber, Address $address): bool
    {
        return Storage::put($this->getCacheKey($postcode, $houseNumber), json_encode($address->toArray()));
    }

    /**
     * @param string $postcode
     * @param string $houseNumber
     * @return Address|null
     */
    public function getFromCache(string $postcode, string $houseNumber): ?Address
    {
        $result = null;
        try {
            if($cacheData = json_decode(Storage::get($this->getCacheKey($postcode, $houseNumber)), true)) {
                $result = new Address();
                $result->setStreet($cacheData['street']);
                $result->setHouseNo($cacheData['house_no']);
                $result->setTown($cacheData['town']);
                $result->setMunicipality($cacheData['municipality']);
                $result->setProvince($cacheData['province']);
                $result->setLatitude($cacheData['latitude']);
                $result->setLongitude($cacheData['longitude']);
            }
        } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {}

        return $result;
    }

    /**
     * @param string $postcode
     * @param string $houseNumber
     * @return string
     */
    private function getCacheKey(string $postcode, string $houseNumber)
    {
        return sprintf(self::CACHE_PATH, $postcode, $houseNumber);
    }

    /**
     * @return string
     */
    public function getCountryCodeFromApi(): string
    {
        $namespace = new \ReflectionClass($this->getApi());
        return preg_replace('/(.*\\\\[a-z]*_)/m', '', $namespace->getNamespaceName());
    }
}
