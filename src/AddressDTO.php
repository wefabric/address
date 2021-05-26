<?php


namespace Wefabric\Address;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Spatie\DataTransferObject\DataTransferObject;
use Wefabric\Address\Action\AddressToStringAction;

class AddressDTO extends DataTransferObject implements Arrayable
{

    protected bool $ignoreMissing = true;

    public ?int $id;

    public ?string $street;

    public ?int $housenumber;

    public ?string $housenumber_addition = '';

    public ?string $postcode;

    public ?string $city;

    public ?string $country_id;

    public ?string $municipality = '';

    public ?string $province = '';

    public ?float $latitude = 0;

    public ?float $longitude = 0;

    public function __construct(array $parameters = [])
    {
        if(isset($parameters['housenumber']) && is_string($parameters['housenumber'])) {
            $parameters['housenumber'] = (int)$parameters['housenumber'];
        }

        if(isset($parameters['latitude'])) {
            $parameters['latitude'] = (float)$parameters['latitude'];
        }

        if(isset($parameters['longitude'])) {
            $parameters['longitude'] = (float)$parameters['longitude'];
        }
        parent::__construct($parameters);
    }

    /**
     * @return string
     */
    public function toString()
    {
        return app(AddressToStringAction::class)->execute($this->all());
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->all();
    }

    /**
     * @return Model
     */
    public function toModel(): Model
    {
        $model = \Wefabric\Address\Address::make()->getModelClass();
        $address = new $model;
        if($this->id) {
            if(!$address = $model::query()->where('id', $this->id)->first()) {
                $address = new $model;
            }
        }
       return $address->fill($this->all());
    }
}
