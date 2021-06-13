<?php


namespace Wefabric\Address\Google;


use Wefabric\Address\Models\Address;

class Maps
{

    const BASE_URL = 'https://www.google.com/maps/search/';

    /**
     * @param Address $address
     * @param array $args
     * @return string
     */
    public static function getUrlByAddressModel(Address $address, array $args = []): string
    {
        $query = $address->getStringAttribute();
        return self::getUrl($query);
    }

    /**
     * @param string $query
     * @param array $args
     * @return string
     */
    public static function getUrl(string $query, array $args = []): string
    {
        $defaultArgs = [
            'api' => 1,
            'query' => ''
        ];

        $args['query'] = $query;
        return self::BASE_URL.'?'.http_build_query(array_replace_recursive($defaultArgs, $args));
    }


}
