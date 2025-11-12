<?php

namespace App\Livewire\Concerns;

trait WithValidator
{
    public function setErrors(array $errors, callable $errorSetter)
    {
        foreach ($errors as $key => $message) {
            $errorSetter($key, $message);
        }
    }
}
