<?php

namespace App\Livewire;

use App\Livewire\Concerns\WithToasts;
use App\Livewire\Concerns\WithValidator;
use App\Services\ArticulosService;
use App\Support\RepositoryData;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class CrudTable extends Component
{

    use WithToasts, WithValidator;

    public string $title = 'Mostrando registros';
    public string $subtitle = 'Ingrese un termino para realizar busqueda o presione una columna para ordenar';
    public string $serviceName = '';

    public array $rows = [];
    public array $columns = []; //[['name' => 'columnname', 'label' => 'label', 'view' => 'viewname']]
    public array $actions = ['view', 'edit', 'delete'];
    public array $filters = []; // ['name' => 'filtername', 'label' => 'value', 'type' => 'text|select|date', 'rules' => ['required', 'minlength:5', min="2021-01-01", max="2022-01-01"], 'options' => ['value' => 'label']]
    public array $activefilters = [];
    public array $invisibleColumns = []; // ['columnname']
    public array $request = [];

    public bool $showPagination = false;
    public bool $show_actions = true;

    public string $route_name = '';
    public string $sort_order = 'desc';
    public ?string $sortby = null;
    public array $sort_columns = [];
    public array $searchable_columns = [];
    public ?string $search_term = '';
    public ?string $search_column = '';
    public ?int $currentPage = 1;
    public ?int $totalPages = 1;
    public $selectedItem = null;
    public ?string $deleteModalView = null;

    public int $itemsPerPage = 10;
    public array $itemsPerPageOptions = ['5' => 5, '10' => 10, '20' => 20, '50' => 50, '100' => 100];
    public int $maxVisiblePages = 5;

    public ?string $search_placeholder = 'Buscar...';
    public ?string $item_name = 'item';
    public ?string $item_plural_name = 'items';

    protected $listeners = ['closing-modal' => 'setSelectedItem'];

    protected $ServicesPath = 'App\\Services\\';
    public string $serviceClass;

    public function mount($totalPages, $serviceName)
    {
        $this->serviceClass = $this->ServicesPath . $serviceName;
        $this->totalPages = $totalPages;
        $this->showPagination = $totalPages > 1;
    }

    public function updated($property)
    {

        $this->resetErrorBag($property);
    }

    public function render()
    {
        return view('livewire.crud-table', [
            'deleteModalContent' => $this->getDeleteModalContent(),
        ]);
    }

    public function getDeleteModalContent()
    {

        if (!$this->selectedItem) {
            return view('components.crud-table.delete-modals.loading')->render();
        }

        $viewPath = "components.crud-table.delete-modals." . ($this->deleteModalView ?? 'default');

        if (view()->exists($viewPath)) {
            return view($viewPath, [
                'item' => $this->selectedItem
            ])->render();
        }
        return view('components.delete-modals.default', [
            'item' => $this->itemToDelete
        ])->render();
    }

    public function updatedRows(): void
    {
        try {
            if (!$this->fetchData()) {
                return;
            }
        } catch (\Throwable $e) {
            $this->toastError('Error al actualizar los datos: ' . $e->getMessage());
        }
    }

    private function fetchData(): bool
    {
        $payload = $this->buildRequestData();

        $this->validateRequest();
        $service = $this->getService();
        $response = $service->get($payload);

        if (!$response->successful) {
            $this->toastError($response->message ?? 'Error al cargar los datos.');
            return false;
        }

        $this->applyResponseData($response->data);
        return true;
    }

    private function buildRequestData(): array
    {
        $this->request = array_merge([
            'sortby' => $this->sortby,
            'order' => $this->sort_order,
            'searchterm' => trim((string)$this->search_term),
            'searchcolumn' => $this->search_column,
            'page' => $this->currentPage,
            'per_page' => $this->itemsPerPage,
        ], $this->activefilters);
        return $this->request;
    }

    private function applyResponseData(RepositoryData $data): void
    {
        $this->totalPages = $data->totalPages ?? 1;
        $this->currentPage = $data->page ?? 1;
        $this->rows = $data->items ?? [];
        $this->showPagination = $this->totalPages > 1;
    }

    public function getRules()
    {
        $rules = [];
        if (count($this->activefilters) > 0) {
            foreach ($this->filters as $filter) {
                if (isset($filter['rules']) && isset($this->activefilters[$filter['name']])) {
                    $rules['activefilters.' . $filter['name']] = $filter['rules'];
                }
            }
        }
        return $rules;
    }

    public function setSortby(string $sortby)
    {
        if ($this->sortby === $sortby) {
            $this->sort_order = $this->sort_order === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort_order = 'desc';
        }
        $this->sortby = $sortby;
        $this->updatedRows();
    }

    public function callSearch()
    {
        $this->updatedRows();
    }

    public function getClearButtonDisabledProperty()
    {
        return empty(trim($this->search_term));
    }

    public function clearSearch()
    {
        $this->search_term = '';
        $this->updatedRows();
    }

    public function changePage(int $page)
    {
        $this->currentPage = $page;
        $this->updatedRows();
    }

    public function setItemsPerPage()
    {
        $this->currentPage = 1;
        $this->updatedRows();
    }

    public function filterRows()
    {
        $errors = $this->validateRequest();

        if (count($errors) > 0) {
            foreach ($this->activefilters as $key => $value) {
                if (isset($errors["activefilters.$key"])) {
                    unset($this->activefilters[$key]);
                }
            }
            return;
        }

        $this->updatedRows();
    }

    public function clearFilter($name)
    {
        if (array_key_exists($name, $this->activefilters)) {
            unset($this->activefilters[$name]);
        }
        if (count($this->activefilters) === 0) {
            $this->updatedRows();
        }
    }

    public function clearAllFilters()
    {
        $this->activefilters = [];
        $this->updatedRows();
    }

    public function setSelectedItem(?int $id)
    {
        if (!$id) {
            $this->reset('selectedItem');
            return;
        }
        $this->selectedItem = collect($this->rows)
            ->firstWhere('id', $id);

        $this->dispatchBrowserEvent('open-modal', 'confirm-deletion');
    }

    public function deleteSelected()
    {
        if (!$this->selectedItem) {
            $this->toastError('No se ha seleccionado ningun item');
            return;
        }
        $result = $this->service->deleteone($this->selectedItem['id']);

        if ($result['statusCode'] !== 204) {
            $this->toastError($result['message']);
            $this->dispatchBrowserEvent('close-modal', 'confirm-deletion');
            $this->reset('selectedItem');
            return;
        }
        $this->reset('selectedItem');
        $this->updatedRows();
        $this->dispatchBrowserEvent('close-modal', 'confirm-deletion');
        $this->toastSuccess('Se ha eliminado el registro');
    }

    public function toggleInvisibleColumn($columnName)
    {
        if (in_array($columnName, $this->invisibleColumns)) {
            $this->invisibleColumns = array_diff($this->invisibleColumns, [$columnName]);
        } else {
            $this->invisibleColumns[] = $columnName;
        }
    }

    public function toggleAllColumns()
    {
        $this->invisibleColumns = [];
    }

    public function getService()
    {
        return new $this->serviceClass();
    }

    public function validateRequest(): array
    {
        try {
            $rules = $this->getRules();
            if (!empty($rules)) {
                $this->validate($rules);
            }
            return [];
        } catch (ValidationException $e) {
            $this->setErrors($e->errors());
            return $e->errors();
        }
    }

    public function setErrors(array $errors)
    {
        foreach ($errors as $key => $message) {
            $this->addError($key, $message);
        }
    }
}
