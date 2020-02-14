<?php

namespace App\Services\Admin;

use App\Services\Base\BaseVisitService;
use App\Visit;

class AdminVisitService extends BaseVisitService
{
    public function fields()
    {
        return  [
            [
                'field' => 'client_name',
                'type' => 'text',
                'label' => 'Client'
            ],
            [
                'field' => 'complaint',
                'type' => 'text',
                'label' => 'Complaint'
            ],
            [
                'field' => 'price',
                'type' => 'text',
                'label' => 'Price'
            ],
            [
                'field' => 'date',
                'type' => 'datetime-local',
                'label' => 'Date'
            ],
            [
                'field' => 'duration',
                'type' => 'text',
                'label' => 'Duration'
            ],
            [
                'field' => 'client_id',
                'type' => 'integer',
                'label' => 'client_id'
            ],
        ];
    }



    public function mapForShow($model)
    {
        return array_map(function ($field) use ($model) {
            return [
                'type' => $field['type'],
                'label' => $field['label'],
                'value' => $model->{$field['field']}
            ];
        }, $this->fields());
    }

    public function getRelationDetails(array $additionalAttributes): array
    {
        return array_merge(
            [
                'fileds' => $this->fields(),
                'relationName' => \Str::plural(class_basename(Visit::class)),
                'fetch_url' => "visits/json"
            ],
            $additionalAttributes
        );
    }
}
