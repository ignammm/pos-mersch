<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\Reactive;

class SuggestionInput extends Component
{
    /** @var string Fully qualified Eloquent model class name */
    public string $model;

    /** @var string Column used for display/search */
    public string $displayColumn = 'id';

    /** @var string The bound value (what parent sees) */
    #[Modelable]
    public $value = '';

    /** @var bool Disable input */
    public bool $disabled = false;

    /** @var string|null Optional icon name (e.g., 'search') */
    public ?string $icon = null;

    /** @var string|null Placeholder text */
    public ?string $placeholder = 'Buscar...';

    /** @var \Illuminate\Database\Eloquent\Collection */
    public $results;

    /** @var bool Whether data is being loaded */
    public bool $loading = false;

    public $iconPosition = 'left';

    #[Reactive]
    public ?string $error = '';

    public string $name;

    public string $label = '';

    public function mount(
        string $model,
        string $displayColumn = 'name',
        $value = '',
        ?string $placeholder = 'Buscar...',
        ?string $icon = null,
        bool $disabled = false,
        ?string $name = null,
        ?string $error = '',
        ?string $label = ''
    ) {
        $this->model = $model;
        $this->displayColumn = $displayColumn;
        $this->value = $value;
        $this->placeholder = $placeholder;
        $this->icon = $icon;
        $this->disabled = $disabled;
        $this->results = collect();
        $this->name = $name;
        $this->error = $error;
        $this->label = $label;
    }

    public function updatedValue()
    {
        $this->fetchSuggestions();
    }

    public function fetchSuggestions()
    {
        if ($this->disabled || strlen($this->value) < 2) {
            $this->results = collect();
            return;
        }

        $this->loading = true;

        $modelClass = $this->model;
        $this->results = $modelClass::query()
            ->where($this->displayColumn, 'like', "%{$this->value}%")
            ->limit(6)
            ->get();

        $this->loading = false;
    }

    public function selectSuggestion($id)
    {
        $modelClass = $this->model;
        $record = $modelClass::find($id);

        if ($record) {
            $this->value = $record->{$this->displayColumn};
            $this->results = collect();
            $this->dispatch('selectSuggestion', value: $this->value);
        }
    }

    public function clear()
    {
        $this->value = '';
        $this->results = collect();
    }

    public function render()
    {
        return view('livewire.suggestion-input');
    }
}
