<?php

namespace Wefabric\Address\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Wefabric\Address\Action\AddressToStringAction;
use Wefabric\Address\Action\DownloadStreetViewImageAction;
use Wefabric\Address\Exception\AddressException;
use Wefabric\Address\Google\Maps;
use Wefabric\Address\Google\StreetViewStaticApi;

class Address extends Model
{
    use HasFactory;
    use SoftDeletes;

    const STREET_VIEW_MEDIA_COLLECTION_KEY = 'google-street-view';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'street',
        'housenumber',
        'housenumber_addition',
        'postcode',
        'city',
        'province',
        'municipality',
        'latitude',
        'longitude',
        'country_id'
    ];

    protected array $required = [
        'street',
        'housenumber',
        'postcode',
        'city',
        'country_id'
    ];

    /**
     * @return string
     */
    public function getStringAttribute()
    {
        return app(AddressToStringAction::class)->execute($this->toArray());
    }

    /**
     * @return string
     */
    public function getFullHousenumberAttribute(): string
    {
        return $this->attributes['housenumber'].$this->attributes['housenumber_addition'];
    }

    /**
     * @return array
     */
    public function toExcelData()
    {
        return (new \Wefabric\Address\Excel\Address($this))->toExcelData();
    }

    /**
     * @return string
     */
    public function getGoogleMapsUrl(): string
    {
        return Maps::getUrlByAddressModel($this);
    }

    /**
     * @return \Spatie\MediaLibrary\MediaCollections\Models\Media|null
     * @throws AddressException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wefabric\Address\Exception\StreetViewStaticApiException
     */
    public function getStreetViewImage(): ?Media
    {
        return $this->getFirstMedia(self::STREET_VIEW_MEDIA_COLLECTION_KEY);
    }

    /**
     * @param string $image
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     * @throws \Wefabric\Address\Exception\StreetViewStaticApiException
     */
    public function setStreetViewImageToCollection(string $image = '')
    {
         $name = Hash::make($this->getStringAttribute().time());
         return $this
             ->addMediaFromString($image ? $image : $this->downloadStreetViewImage())
             ->setName($name)
             ->setFileName($name.'.jpg')
             ->toMediaCollection(self::STREET_VIEW_MEDIA_COLLECTION_KEY);
    }

    /**
     * @param int $width
     * @param int $height
     * @param array $options
     * @return string
     * @throws AddressException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wefabric\Address\Exception\StreetViewStaticApiException
     */
    public function downloadStreetViewImage(int $width = 0, int $height = 0, array $options = []): string
    {
        return app(DownloadStreetViewImageAction::class)->execute($this->getStringAttribute().' '. $this->country_id, $width, $height, $options);
    }

    /**
     * @return array
     */
    public function getCoordinates(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        ];
    }

    /**
     * @return bool
     */
    public function scopeHasCoordinates(): bool
    {
        return $this->latitude && $this->longitude;
    }

    /**
     * Registers the media collection to the Media Library package
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::STREET_VIEW_MEDIA_COLLECTION_KEY)
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg']);
    }

    /**
     * @param Collection|array $addressData
     * @return \Domains\Addresses\Model\Address
     */
    public static function findOrCreate(Collection|array $addressData): Address
    {
        if(is_array($addressData)) {
            $addressData = collect($addressData);
        }

        $addressClass = new self();

        $missing = [];
        foreach ($addressClass->required as $required) {
            if(!$addressData->get($required)) {
                $missing[] = $required;
            }
        }

        if($missing) {
            throw new \InvalidArgumentException(sprintf('Missing required address data: %s', implode(',', $missing)));
        }

        $query = self::query()
            ->where('street', $addressData->get('street'))
            ->where('housenumber', $addressData->get('housenumber'))
            ->where('postcode', $addressData->get('postcode'))
            ->where('city', $addressData->get('city'))
            ->where('country_id', $addressData->get('country_id'));

        if($addressData->get('housenumber_addition')) {
            $query->where('housenumber_addition', $addressData->get('housenumber_addition'));
        } else {
            $query->where(function(Builder $query){
                $query
                    ->whereNull('housenumber_addition')
                    ->orWhere('housenumber_addition', '');
            });
        }

        if(!$address = $query->first()) {
            $modelClass = \Wefabric\Address\Address::make()->getModelClass();
            $address = new $modelClass($addressData->toArray());
            $address->save();
        }

        return $address;
    }
}
