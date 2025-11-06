<?php

namespace App\Services;

use App\Models\Pago;
use App\Models\Caja;
use App\Models\Cliente;
use App\Models\CuentaCorriente;
use App\Models\Factura;
use App\Models\MetodoPago;
use App\Models\MovimientoCaja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PagoService
{
    public function procesarPago($facturaId, $metodoPagoId, $monto, $datosAdicionales = [])
    {
        return DB::transaction(function () use ($facturaId, $metodoPagoId, $monto, $datosAdicionales) {
            
            $factura = Factura::findOrFail($facturaId);
            $metodoPago = MetodoPago::findOrFail($metodoPagoId);
            
            // Procesar según el tipo de método de pago
            return match($metodoPago->tipo) {
                'efectivo' => $this->procesarEfectivo($factura, $metodoPago, $monto, $datosAdicionales),
                'tarjeta_credito', 'tarjeta_debito' => $this->procesarTarjeta($factura, $metodoPago, $monto, $datosAdicionales),
                'transferencia' => $this->procesarTransferencia($factura, $metodoPago, $monto, $datosAdicionales),
                'cuenta_corriente' => $this->procesarCuentaCorriente($factura, $metodoPago, $monto, $datosAdicionales),
                'cheque' => $this->procesarCheque($factura, $metodoPago, $monto, $datosAdicionales),
                'mercadopago' => $this->procesarMercadoPago($factura, $metodoPago, $monto, $datosAdicionales),
                default => $this->procesarOtros($factura, $metodoPago, $monto, $datosAdicionales)
            };
        });
    }

    private function procesarEfectivo($factura, $metodoPago, $monto, $datosAdicionales)
    {
        $pagoData = $this->crearDatosBasePago($factura, $metodoPago, $monto, $datosAdicionales);
        $pagoData['fecha_confirmacion'] = now();
        $pagoData['estado'] = 'completado';

        $pago = Pago::create($pagoData);
        
        // Registrar en caja
        $this->registrarEnCaja($pago, 'ingreso', 'Pago en efectivo - Factura #' . $factura->id);
        
        // Actualizar factura
        $this->actualizarEstadoFactura($factura, $monto, 'pagada');

        return $pago;
    }

    private function procesarTarjeta($factura, $metodoPago, $monto, $datosAdicionales)
    {
        $pagoData = $this->crearDatosBasePago($factura, $metodoPago, $monto, $datosAdicionales);
        $pagoData['fecha_confirmacion'] = now();
        $pagoData['fecha_liquidacion'] = now()->addDays($metodoPago->dias_liquidacion);
        $pagoData['estado'] = 'completado';
        $pagoData['codigo_autorizacion'] = $datosAdicionales['codigo_autorizacion'] ?? $this->generarCodigoAutorizacion();
        $pagoData['datos_tarjeta'] = $datosAdicionales['datos_tarjeta'] ?? null;

        $pago = Pago::create($pagoData);
        
        // Registrar en caja (pero con observación de liquidación futura)
        $this->registrarEnCaja($pago, 'ingreso', 
            'Pago con tarjeta - Factura #' . $factura->id . 
            ' - Liquidación en ' . $metodoPago->dias_liquidacion . ' días'
        );
        
        // Actualizar factura
        $this->actualizarEstadoFactura($factura, $monto, 'pagado');

        return $pago;
    }

    private function procesarTransferencia($factura, $metodoPago, $monto, $datosAdicionales)
    {
        $pagoData = $this->crearDatosBasePago($factura, $metodoPago, $monto, $datosAdicionales);
        $pagoData['fecha_confirmacion'] = now(); // Se confirma manualmente
        $pagoData['fecha_liquidacion'] = now()->addDays(1);
        $pagoData['estado'] = 'completado';
        $pagoData['entidad_bancaria'] = $datosAdicionales['entidad_bancaria'] ?? null;

        $pago = Pago::create($pagoData);
        
        // NO se registra en caja - va directo al banco
        // La factura queda como pendiente hasta confirmación
        $this->actualizarEstadoFactura($factura, $monto, 'pendiente');

        return $pago;
    }

    private function procesarCuentaCorriente($factura, $metodoPago, $monto, $datosAdicionales)
    {
        // Validar que el cliente puede usar cuenta corriente
        $cliente = Cliente::findOrFail($factura->cliente_id);

        // Verificar límite de crédito
        $saldoActual = $this->obtenerSaldoCuentaCorriente($cliente->id);
       
        $pagoData = $this->crearDatosBasePago($factura, $metodoPago, $monto, $datosAdicionales);
        $pagoData['fecha_confirmacion'] = now();
        $pagoData['estado'] = 'completado';
        $pagoData['observaciones'] = 'Venta a cuenta corriente';

        $pago = Pago::create($pagoData);
        
        // Registrar en cuenta corriente
        $this->registrarEnCuentaCorriente($cliente, $factura, $pago, $monto, $saldoActual);
        
        // NO se registra en caja - es crédito
        // La factura queda como pendiente de pago real
       

        return $pago;
    }

    private function procesarCheque($factura, $metodoPago, $monto, $datosAdicionales)
    {
        $pagoData = $this->crearDatosBasePago($factura, $metodoPago, $monto, $datosAdicionales);
        $pagoData['fecha_confirmacion'] = null;
        $pagoData['estado'] = 'pendiente';
        $pagoData['numero_cheque'] = $datosAdicionales['numero_cheque'] ?? null;
        $pagoData['fecha_vencimiento_cheque'] = $datosAdicionales['fecha_vencimiento_cheque'] ?? null;
        $pagoData['entidad_bancaria'] = $datosAdicionales['entidad_bancaria'] ?? null;

        $pago = Pago::create($pagoData);
        
        // NO se registra en caja inmediatamente
        // Se registra cuando el cheque se cobra
        $this->actualizarEstadoFactura($factura, $monto, 'pendiente');

        return $pago;
    }

    private function procesarMercadoPago($factura, $metodoPago, $monto, $datosAdicionales)
    {
        $pagoData = $this->crearDatosBasePago($factura, $metodoPago, $monto, $datosAdicionales);
        $pagoData['fecha_confirmacion'] = now();
        $pagoData['fecha_liquidacion'] = now()->addDays($metodoPago->dias_liquidacion);
        $pagoData['estado'] = 'completado';
        $pagoData['referencia'] = $datosAdicionales['referencia_mp'] ?? null;

        $pago = Pago::create($pagoData);
        
        // Registrar en caja
        $this->registrarEnCaja($pago, 'ingreso', 
            'Pago MercadoPago - Factura #' . $factura->id
        );
        
        $this->actualizarEstadoFactura($factura, $monto, 'pagado');

        return $pago;
    }

    private function procesarOtros($factura, $metodoPago, $monto, $datosAdicionales)
    {
        $pagoData = $this->crearDatosBasePago($factura, $metodoPago, $monto, $datosAdicionales);
        $pagoData['fecha_confirmacion'] = now();
        $pagoData['estado'] = 'completado';
        $pagoData['observaciones'] = $datosAdicionales['observaciones'] ?? 'Pago con método especial';

        $pago = Pago::create($pagoData);
        
        // Por defecto, no registrar en caja para métodos "otros"
        $this->actualizarEstadoFactura($factura, $monto, 'pagado');

        return $pago;
    }

    private function crearDatosBasePago($factura, $metodoPago, $monto, $datosAdicionales)
    {
        $comision = $metodoPago->calcularComision($monto);
        $neto = $monto - $comision;

        // Obtener caja si el método la requiere
        $cajaId = null;
        if ($this->metodoRequiereCaja($metodoPago->tipo)) {
            $caja = Caja::where('user_id', Auth::id())
                        ->where('estado', 'abierta')
                        ->first();
            
            if (!$caja) {
                throw new \Exception('No hay caja abierta para registrar el pago');
            }
            $cajaId = $caja->id;
        }

        return [
            'factura_id' => $factura->id,
            'metodo_pago_id' => $metodoPago->id,
            'caja_id' => $cajaId,
            'user_id' => Auth::id(),
            'monto' => $monto,
            'comision' => $comision,
            'neto' => $neto,
            'estado' => 'completado', // Se sobreescribe en algunos métodos
            'fecha_pago' => now(),
            'fecha_confirmacion' => now(), // Se sobreescribe en algunos métodos
            'referencia' => $datosAdicionales['referencia'] ?? null,
            'codigo_autorizacion' => $datosAdicionales['codigo_autorizacion'] ?? null,
            'observaciones' => $datosAdicionales['observaciones'] ?? null,
        ];
    }

    private function registrarEnCaja($pago, $tipo, $descripcion)
    {
        if (!$pago->caja_id) {
            return;
        }

        return MovimientoCaja::create([
            'caja_id' => $pago->caja_id,
            'pago_id' => $pago->id,
            'user_id' => Auth::id(),
            'tipo' => $tipo,
            'monto' => $pago->monto,
            'descripcion' => $descripcion,
            'metodo_pago_id' => $pago->metodo_pago_id,
            'fecha_movimiento' => now(),
        ]);
    }

    private function registrarEnCuentaCorriente($cliente, $factura, $pago, $monto, $saldoActual)
    {
        return CuentaCorriente::create([
            'cliente_id' => $cliente->id,
            'factura_id' => $factura->id,
            'pago_id' => $pago->id,
            'tipo_movimiento' => 'debe',
            'monto' => $monto,
            'saldo_actual' => $saldoActual + $monto,
            'descripcion' => 'Venta #' . $factura->id,
            'fecha_vencimiento' => now()->addDays(30),
            'estado' => 'pendiente',
        ]);
    }

    private function actualizarEstadoFactura($factura, $montoPagado, $estado)
    {
        $nuevoSaldo = max(0, $factura->saldo_pendiente - $montoPagado);
        
        $factura->update([
            'saldo_pendiente' => $nuevoSaldo,
        ]);

        $factura->actualizarEstadoPago();
    }

    private function metodoRequiereCaja($tipoMetodo)
    {
        return in_array($tipoMetodo, [
            'efectivo',
            'tarjeta_credito', 
            'tarjeta_debito',
            'mercadopago'
        ]);
    }

    private function obtenerSaldoCuentaCorriente($clienteId)
    {
        $ultimoMovimiento = CuentaCorriente::where('cliente_id', $clienteId)
            ->latest('fecha_movimiento')
            ->first();
            
        return $ultimoMovimiento ? $ultimoMovimiento->saldo_actual : 0;
    }

    private function generarCodigoAutorizacion()
    {
        return 'AUTH-' . strtoupper(uniqid());
    }

    // Método para confirmar pagos pendientes (transferencias, cheques)
    public function confirmarPago($pagoId, $datosConfirmacion = [])
    {
        return DB::transaction(function () use ($pagoId, $datosConfirmacion) {
            $pago = Pago::findOrFail($pagoId);
            
            $pago->update([
                'estado' => 'completado',
                'fecha_confirmacion' => now(),
                'referencia' => $datosConfirmacion['referencia'] ?? $pago->referencia,
            ]);

            // Si es cheque o transferencia, ahora se puede registrar en caja
            if (in_array($pago->metodoPago->tipo, ['transferencia', 'cheque'])) {
                $this->registrarEnCaja($pago, 'ingreso', 
                    'Confirmación de pago - ' . $pago->metodoPago->nombre . ' - Factura #' . $pago->factura_id
                );
            }

            // Actualizar factura
            $this->actualizarEstadoFactura($pago->factura, $pago->monto, 'pagado');

            return $pago;
        });
    }
}