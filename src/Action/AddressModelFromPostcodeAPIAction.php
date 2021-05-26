<?php


namespace Wefabric\Address\Action;


use Wefabric\Address\Models\Address;

class AddressModelFromPostcodeAPIAction
{
    /**
     * @param string $postcode
     * @param string $houseNumber
     * @param bool $cache
     * @return Address
     */
    public function execute(string $postcode, string $houseNumber, bool $cache = false): Address
    {
        if($dto = app(AddressDTOFromPostcodeAPIAction::class)->execute($postcode, $houseNumber, $cache)){
            return $dto->toModel();
        }
        return new Address();
    }
}
