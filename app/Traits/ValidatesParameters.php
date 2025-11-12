<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait ValidatesParameters
{
    /**
     * Validate data against a rules class.
     *
     * @param array $data
     * @param string $requestClass A class that has $rules and $messages properties
     * @return array The validated data
     *
     * @throws ValidationException
     */
    protected function validateParams(array $data, string $requestClass): array
    {
        $request = new $requestClass();

        // if (!property_exists($request, 'rules')) {
        //     throw new \InvalidArgumentException("La clase {$requestClass} no tiene propiedades 'rules' o 'messages'");
        // }

        $rules = $request->rules();

        $messages = $request->messages();

        return Validator::make($data, $rules, $messages)->validate();
    }
}
