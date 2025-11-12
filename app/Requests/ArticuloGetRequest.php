<?php

namespace App\Requests;

class ArticuloGetRequest extends BaseGetRequest
{
    public array $sortFields = [
        'id',
        'articulo',
        'codigo_interno',
        'codigo_fabricante',
        'rubro',
        'marca',
        'precio',
        'unidad'
    ];

    public array $searchColumns = [
        'articulo',
        'codigo_interno',
        'codigo_fabricante',
        'codigo_proveedor',
        'rubro',
        'marca',
        'proveedor.nombre'
    ];

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'searchterm' => 'nullable|string|max:100',
            'sortby' => 'nullable|string|in:' . implode(',', $this->sortFields),
            'order' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'paginate' => 'nullable|boolean',
            'searchcolumn' => 'nullable|string|in:' . implode(',', $this->searchColumns),
            'articulo' => 'nullable|string|min:3',
            'codigo_interno' => 'nullable|string|min:3',
            'codigo_fabricante' => 'nullable|string|min:3',
            'marca' => 'nullable|string|min:3',
            'proveedor.id' => 'nullable|string',
        ]);
    }

    public function messages(): array
    {
        return  array_merge(parent::messages(), [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo es obligatorio',
            'email.email' => 'Debe ser un correo válido',
        ]);
    }
}
