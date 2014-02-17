<?php

namespace rsanchez\Deep\Model\Hydrator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use rsanchez\Deep\Model\Fieldtype;
use rsanchez\Deep\Model\Hydrator\HydratorInterface;
use rsanchez\Deep\Model\AssetsFile;
use rsanchez\Deep\Model\AssetsSelection;

class AssetsHydrator implements HydratorInterface
{
    public function __invoke(Collection $collection)
    {
        if ($collection->isEmpty()) {
            return;
        }

        $selections = AssetsFile::with('uploadPref')->entryId($collection->modelKeys())->get();

        $collection->each(function ($entry) use ($selections) {

            // loop through all assets fields
            $entry->channel->fields->filter(function ($field) {

                return $field->field_type === 'assets';

            })->each(function ($field) use ($entry, $selections) {

                $entry->setAttribute($field->field_name, $selections->filter(function ($selection) use ($entry, $field) {
                    return $entry->getKey() === $selection->getKey() && $field->field_id === $selection->field_id;
                }));

            });


        });
    }
}
