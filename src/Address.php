<?php


namespace Wefabric\Address;


use Illuminate\Database\Eloquent\Model;
use Wefabric\Address\Action\AddressDTOFromAddressModelAction;
use Wefabric\Address\Action\AddressDTOFromPostcodeAPIAction;
use Wefabric\Address\Action\AddressModelFromPostcodeAPIAction;
use Wefabric\Address\Action\SplitHousenumberAndAdditionAction;

class Address
{
    /**
     * @return Address
     */
    public static function make(): Address
    {
        return new self();
    }

    /**
     * @param Models\Address $addressModel
     * @return AddressDTO
     */
    public function DTOFromModel(Models\Address $addressModel)
    {
        return app(AddressDTOFromAddressModelAction::class)->execute($addressModel);
    }

    /**
     * @param string $postcode
     * @param string $houseNumber
     * @param bool $cache
     * @return AddressDTO|null
     */
    public function DTOFromPostcodeAPI(string $postcode, string $houseNumber, bool $cache = false): ?AddressDTO
    {
        return app(AddressDTOFromPostcodeAPIAction::class)->execute($postcode, $houseNumber, $cache);
    }

    /**
     * @param string $postcode
     * @param string $houseNumber
     * @param bool $cache
     * @return Models\Address
     */
    public function modelFromPostcodeApi(string $postcode, string $houseNumber, bool $cache = false): Models\Address
    {
        return app(AddressModelFromPostcodeAPIAction::class)->execute($postcode, $houseNumber, $cache);
    }

    /**
     * @param string $housenumberAndAddition
     * @return array
     */
    public function splitHousenumberAndAddition(string $housenumberAndAddition): array
    {
        return app(SplitHousenumberAndAdditionAction::class)->execute($housenumberAndAddition);
    }

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return config('address.model_class') ?? \Wefabric\Address\Models\Address::class;
    }
}
