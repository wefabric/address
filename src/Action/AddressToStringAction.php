<?php


namespace Wefabric\Address\Action;


use Wefabric\Address\AddressDTO;

class AddressToStringAction
{

    /**
     * @param array $address
     * @return string
     */
    public function execute(array $address): string
    {
        return $address['street'].' '. $address['housenumber'].$address['housenumber_addition'].' '.$address['postcode'].' '.$address['city'];
    }
}
