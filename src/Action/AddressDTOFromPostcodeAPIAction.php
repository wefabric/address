<?php


namespace Wefabric\Address\Action;


use Wefabric\Address\AddressDTO;
use Wefabric\Address\PostcodeAPI;

class AddressDTOFromPostcodeAPIAction
{

    /**
     * @param string $postcode
     * @param string $houseNumber
     * @param bool $cache
     * @return AddressDTO|null
     */
    public function execute(string $postcode, string $houseNumber, bool $cache = false): ?AddressDTO
    {
        $postcodeApi = new PostcodeAPI();

        if($postcodeApiResult = $postcodeApi->get($postcode, $houseNumber, $cache)) {
            $housenumberAndAddition = app(SplitHousenumberAndAdditionAction::class)->execute($postcodeApiResult->getHouseNo());
            return new AddressDTO(
                [
                    'street' => $postcodeApiResult->getStreet(),
                    'housenumber' => $housenumberAndAddition['housenumber'],
                    'housenumber_addition' => $housenumberAndAddition['housenumber_addition'],
                    'city' => $postcodeApiResult->getTown(),
                    'municipality' => $postcodeApiResult->getMunicipality(),
                    'province' => $postcodeApiResult->getProvince(),
                    'latitude' => $postcodeApiResult->getLatitude(),
                    'longitude' => $postcodeApiResult->getLongitude(),
                    'country_id' => $postcodeApi->getCountryCodeFromApi(),
                    'postcode' => $postcode
                ]
            );
        }
        return null;
    }
}
