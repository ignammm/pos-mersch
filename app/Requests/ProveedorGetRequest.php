<?php

namespace App\Requests;

class ProveedorGetRequest extends BaseGetRequest
{
    public array $sortFields = [
        'id',
        'nombre',
        'email',
        'direccion',
        'created_at',
        'updated_at',
    ];

    public array $searchColumns = [
        'nombre',
        'direccion',
        'email',
        'telefono',
    ];

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'searchterm' => 'nullable|string|max:100',
            'sortby' => 'nullable|string|in:' . implode(',', $this->sortFields),
            'searchcolumn' => 'nullable|string|in:' . implode(',', $this->searchColumns),
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);
    }

    public function messages(): array
    {
        return  array_merge(parent::messages(), []);
    }
}
