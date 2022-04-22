<?php


namespace Wefabric\Address;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Spatie\DataTransferObject\DataTransferObject;
use Wefabric\Address\Action\AddressToStringAction;
use Wefabric\Address\Google\Maps;

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
    public function getGoogleMapsUrl(): string
    {
        return Maps::getUrl($this->toString());
    }
    
    /**
     * @return string
     */
    public function toString(): string
    {
        return app(AddressToStringAction::class)->execute($this->all());
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = $this->all();
        $data['string'] = $this->toString();
        return $data;
    }

    /**
     * @return Model
     */
    public function toModel(): Model
    {
       return \Wefabric\Address\Address::make()->getModelClass()::findOrCreate($this->all());
    }

    public function __get($key)
    {
        if($key === 'full_housenumber') {
            return $this->housenumber.$this->housenumber_addition;
        }
    }
}
