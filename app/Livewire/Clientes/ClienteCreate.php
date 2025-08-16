<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Livewire\Component;

class ClienteCreate extends Component
{
    public $nombre;
    public $dni;
    public $tipo_cliente;
    public $percepcion_iva;
    public $cuit;
    public $telefono;
    public $direccion;
    public $email;

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'dni' => 'nullable|numeric|digits_between:7,10',
            'tipo_cliente' => 'required|in:Empresa,Taller,Particular',
            'percepcion_iva' => 'required|in:Responsable Inscripto,Exento,Monotributista,Consumidor Final',
            'cuit' => 'nullable|numeric|digits:11',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ];
    }

    public function submit()
    {
        $this->validate();

        Cliente::create([
            'nombre' => $this->nombre,
            'dni' => $this->dni,
            'tipo_cliente' => $this->tipo_cliente,
            'percepcion_iva' => $this->percepcion_iva,
            'cuit' => $this->cuit,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'email' => $this->email,
        ]);

        $this->reset(); // Limpia los campos

        $this->dispatch('cliente-creado'); // Emite evento para mostrar alerta en el frontend
    }

    public function render()
    {
        return view('livewire.clientes.cliente-create');
    }
}
