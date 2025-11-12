<?php

namespace App\Livewire;

use Livewire\Attributes\Modelable;
use Livewire\Component;

class Searchableselect extends Component
{

    public bool $searchable = true;
    public string $placeholder = 'Selecciona un valor...';
    public string $searchPlaceholder = 'Buscar...';
    public string $searchterm = '';
    public array $currentoptions = []; // ['label' => 'value']
    public array $options = []; // ['label' => 'value']
    public bool $expanded = false;
    #[Modelable]
    public $value;
    public $icon = ''; // fa-search, fa-user, fa-envelope, etc.

    public function render()
    {
        return view('livewire.searchableselect');
    }

    public function mount()
    {
        $this->currentoptions = $this->options;
    }

    public function openOrClose()
    {
        $this->expanded = !$this->expanded;
    }

    public function search()
    {
        $term = strtolower($this->searchterm);

        $this->currentoptions = collect($this->options)
            ->filter(fn($label) => str_contains(strtolower($label), $term))
            ->toArray();
        $this->value = array_key_first($this->currentoptions);
    }

    public function selectOption($option)
    {
        $this->value = $option;
        $this->searchterm = '';
        $this->currentoptions = $this->options;
        $this->expanded = false;

        if (isset($this->options[$option])) {
            $selected = [$option => $this->options[$option]];
            $rest = collect($this->options)->except($option);
            $this->currentoptions = $selected + $rest->toArray();
        }
    }

    public function setValueFirstItem()
    {
        if (count($this->currentoptions) === 0) return;
        $value = array_key_first($this->currentoptions);
        $this->selectOption($value);
    }
}
