<?php

namespace Wefabric\Address\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Wefabric\Address\Models\Address;

class GetStreetViewImage extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Get Street View image';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            if($model instanceof Address) {
                $model->setStreetViewImageToCollection();
            }
        }

        return Action::message('wefabric_address::address.street_view_image_added');
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return __('wefabric_address::address.street_view_image_action_name');
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [];
    }
}
