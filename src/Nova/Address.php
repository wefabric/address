<?php

namespace Wefabric\Address\Nova;

use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Wefabric\Address\Nova\Actions\GetStreetViewImage;

class Address extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Address::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'string';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
            Text::make(__('Straat'), 'street')->sortable()->hideFromIndex()->rules('required', 'max:255'),
            Text::make(__('Address'), function (\App\Models\Address $address) {
                return $address->getStringAttribute();
            })->hideWhenUpdating()->hideWhenCreating(),
            Number::make(__('Housenumber'), 'housenumber')->sortable()->hideFromIndex()->rules('required'),
            Text::make(__('Toevoeging'), 'housenumber_addition')->sortable()->hideFromIndex(),
            Text::make(__('Postcode'), 'postcode')->sortable()->hideFromIndex()->rules('required'),
            Text::make(__('Huisnummer'), 'fullHousenumber')->sortable()->hideFromIndex()->hideWhenCreating()->hideWhenUpdating(),
            Text::make(__('Woonplaats'), 'city')->sortable()->rules('required', 'max:255')->hideFromIndex(),
            Text::make(__('Gemeente'), 'municipality')->sortable()->hideFromIndex(),
            Text::make(__('Provincie'), 'province')->sortable()->hideFromIndex(),
            Text::make(__('Datum'), 'created_at')->sortable()->hideFromIndex()->hideWhenCreating()->hideWhenUpdating(),
            Text::make(__('Street View'), function (\Wefabric\Address\Models\Address $address) {
                $result = '-';
                if($image = $address->getStreetViewImage()) {
                    $result = sprintf('<img src="%s" width="480" height="480"/>', $image->getUrl());
                }
                return $result;
            })->asHtml()->hideFromIndex()->hideWhenCreating()->hideWhenUpdating()
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            new GetStreetViewImage()
        ];
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Addresses');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('Address');
    }
}
