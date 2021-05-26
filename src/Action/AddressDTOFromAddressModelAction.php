<?php


namespace Wefabric\Address\Action;


use Illuminate\Database\Eloquent\Model;
use Wefabric\Address\AddressDTO;

class AddressDTOFromAddressModelAction
{

    /**
     * @param Model $addressModel
     * @return AddressDTO
     */
    public function execute(Model $addressModel)
    {
        return new AddressDTO($addressModel->toArray());
    }

}
