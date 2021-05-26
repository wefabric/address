<?php


namespace Wefabric\Address\Action;


use Wefabric\Address\AddressDTO;
use Wefabric\Address\Models\Address;

class AddressDTOFromAddressModelAction
{

    /**
     * @param Address $addressModel
     * @return AddressDTO
     */
    public function execute(Address $addressModel)
    {
        return new AddressDTO($addressModel->toArray());
    }

}
