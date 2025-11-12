<?php

namespace App\Requests;

class BaseGetRequest
{

    public function rules(): array
    {
        return [
            'paginate' => 'nullable|boolean',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1',
            'order' => 'sometimes|string|in:asc,desc',
        ];
    }

    public function messages(): array
    {
        return [
            'paginate.required' => 'Debe seleccionar si se debe paginar o no',
            'page.required' => 'Debe seleccionar la página',
            'per_page.required' => 'Debe seleccionar el número de elementos por página',
            'order.required' => 'Debe seleccionar el orden de la consulta',
        ];
    }
}
