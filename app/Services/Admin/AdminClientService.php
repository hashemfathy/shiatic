<?php

namespace App\Services\Admin;

use App\Client;
use App\Services\Base\BaseClientService;

class AdminClientService extends BaseClientService
{
    public function fields()
    {
        return  [
            [
                'field' => 'name',
                'type' => 'text',
                'label' => 'Name'
            ],
            [
                'field' => 'gender',
                'type' => 'text',
                'label' => 'gender'
            ],
            [
                'field' => 'phone',
                'type' => 'text',
                'label' => 'Phone'
            ],
            [
                'field' => 'code_num',
                'type' => 'integer',
                'label' => 'Code'
            ],
            [
                'field' => 'called',
                'type' => 'boolean',
                'label' => 'Called'
            ]
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
                'relationName' => \Str::plural(class_basename(Client::class)),
                'fetch_url' => "clients/json"
            ],
            $additionalAttributes
        );
    }
}
