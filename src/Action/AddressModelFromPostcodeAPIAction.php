<?php


namespace Wefabric\Address\Action;


use Illuminate\Database\Eloquent\Model;

class AddressModelFromPostcodeAPIAction
{
    /**
     * @param string $postcode
     * @param string $houseNumber
     * @param bool $cache
     * @return Model
     */
    public function execute(string $postcode, string $houseNumber, bool $cache = false): Model
    {
        if($dto = app(AddressDTOFromPostcodeAPIAction::class)->execute($postcode, $houseNumber, $cache)){
            return $dto->toModel();
        }
        return new Address();
    }
}
