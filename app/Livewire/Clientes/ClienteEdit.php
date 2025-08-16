<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class ClienteEdit extends Component
{
    public Cliente $cliente;

    public $nombre;
    public $dni;
    public $tipo_cliente;
    public $percepcion_iva;
    public $cuit;
    public $telefono;
    public $direccion;
    public $email;

    public function mount(Cliente $cliente)
    {
        $this->cliente = $cliente;

        // Cargar valores en propiedades públicas
        $this->nombre = $cliente->nombre;
        $this->dni = $cliente->dni;
        $this->tipo_cliente = $cliente->tipo_cliente;
        $this->percepcion_iva = $cliente->percepcion_iva;
        $this->cuit = $cliente->cuit;
        $this->telefono = $cliente->telefono;
        $this->direccion = $cliente->direccion;
        $this->email = $cliente->email;
    }

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

    public function update()
    {
        $this->validate();

        $this->cliente->update([
            'nombre' => $this->nombre,
            'dni' => $this->dni,
            'tipo_cliente' => $this->tipo_cliente,
            'percepcion_iva' => $this->percepcion_iva,
            'cuit' => $this->cuit,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'email' => $this->email,
        ]);

        session()->flash('message', '✅ Cliente actualizado correctamente.');
    }

    public function delete()
    {
        $this->cliente->delete();

        return redirect()->route('clientes.index')->with('message', '🗑️ Cliente eliminado correctamente.');
    }

    public function render()
    {
        return view('livewire.clientes.cliente-edit');
    }
}
