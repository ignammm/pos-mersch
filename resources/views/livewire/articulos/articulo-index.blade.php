@php
    $proveedoresOptions = [];
    if (isset($proveedores) && count($proveedores->items) > 0) {
        foreach ($proveedores->items as $proveedor) {
            $proveedoresOptions[$proveedor['id']] = $proveedor['nombre'];
        }
    }
@endphp


<div class="max-w-screen">
    <livewire:crud-table title="Articulos"
    subtitle="Mostrando registros"
    :serviceName="'ArticulosService'"
    :rows="$articulos->items ?? []"
    :columns="[
            [
                'name' => 'id',
                'label' => 'ID',
            ],
            [
                'name' => 'articulo',
                'label' => 'Articulo',
            ],
            [
                'name' => 'codigo_interno',
                'label' => 'Código interno',
            ],
            [
                'name' => 'codigo_fabricante',
                'label' => 'Código fabricante',
            ],
            [
                'name' => 'rubro',
                'label' => 'Rubro',
            ],
            [
                'name' => 'marca',
                'label' => 'Marca',
            ],
            [
                'name' => 'precio',
                'label' => 'Precio',
            ],
            [
                'name' => 'unidad',
                'label' => 'Unidad',
            ],
        ]"
        :actions="['edit']"
        :sort_columns="['id', 'articulo', 'codigo_interno', 'codigo_fabricante', 'rubro', 'marca', 'precio']"
        :searchable_columns="[
            'articulo' => 'Articulo',
            'codigo_interno' => 'Código interno',
            'codigo_fabricante' => 'Código fabricante',
            'codigo_proveedor' => 'Código proveedor',
            'rubro' => 'Rubro',
            'marca' => 'Marca',
            'proveedor.id' => 'Proveedor',
        ]"
        :search_column="'articulo'"
        :show_actions="true"
        :route_name="'articulos'"
        :currentPage="1"
        :totalPages="1"
        :search_placeholder="'Buscar articulo...'"
        :item_name="'articulo'"
        :item_plural_name="'articulos'"
        :filters="[
            [
                'name' => 'articulo',
                'label' => 'Articulo',
                'type' => 'text',
                'rules' => ['required', 'min:3', 'max:100'],
            ],
            [
                'name' => 'codigo_interno',
                'label' => 'Código interno',
                'type' => 'text',
                'rules' => ['required', 'min:3', 'max:100'],
            ],
            [
                'name' => 'proveedor.id',
                'label' => 'Proveedor',
                'type' => 'select',
                'options' => $proveedoresOptions,
            ],
        ]" />
</div>
