<?php

namespace Wefabric\Address\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wefabric\Address\Action\AddressToStringAction;

class Address extends Model
{
    use HasFactory;
    use SoftDeletes;

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

    public function getFullHousenumberAttribute(): string
    {
        return $this->attributes['housenumber'].$this->attributes['housenumber_addition'];
    }

    public function toExcelData()
    {
        return (new \Wefabric\Address\Excel\Address($this))->toExcelData();
    }

}
