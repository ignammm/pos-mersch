<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConvertirPresupuestoRequest extends FormRequest
{
    public function authorize()
    {
        // return $this->user()->can('convertir', $this->route('presupuesto'));
    }
    
    public function rules()
    {
        return [
            'tipo' => 'required|in:venta,trabajo',
            'cliente_id' => 'required',
            'vehiculo_cliente_id' => 'required',
            'nombre_trabajo' => 'required|string|max:200',
            'descripcion_trabajo' => 'nullable|max:500',
        ];
    }
    
    public function messages()
    {
        return [
            'cliente_id.required' => 'El cliente es obligatorio para el trabajo',
            'vehiculo_cliente_id.required' => 'La patente es obligatoria para el trabajo',
            'nombre_trabajo.required' => 'Es necesario un nombre para el trabajo',
        ];
    }
}