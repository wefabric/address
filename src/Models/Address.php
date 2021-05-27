<?php

namespace Wefabric\Address\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\File;
use Wefabric\Address\Action\AddressToStringAction;
use Wefabric\Address\Exception\AddressException;
use Wefabric\Address\Google\StreetViewStaticApi;

class Address extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

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
         $name = Hash::make($this->latitude.$this->longitude.time());
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
    public function downloadStreetViewImage(int $width = 1024, int $height = 1024, array $options = []): string
    {
        $streetViewActive = config('address.google.street_view_active');

        if(!$streetViewActive) {
            throw new AddressException('Google StreetView is not active. Set your GOOGLE_STREET_VIEW_ACTIVE to true in your environment variable and add a valid GOOGLE_API_KEY');
        }

        if($this->scopeHasCoordinates()) {
            return StreetViewStaticApi::make()->getThumbnail($this->latitude, $this->longitude, $width, $height, $options);
        }
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

}
