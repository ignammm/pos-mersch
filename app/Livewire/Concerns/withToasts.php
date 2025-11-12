<?php

namespace App\Livewire\Concerns;

trait WithToasts
{
    public function toastSuccess($message, $duration = 3000)
    {
        session()->flash('ok', $message);
    }

    public function toastError($message, $duration = 4000)
    {
        session()->flash('bad', $message);
    }

    public function toastWarning($message, $duration = 3000)
    {
        session()->flash('warn', $message);
    }

    public function toastInfo($message, $duration = 3000)
    {
        session()->flash('info', $message);
    }

    public function clearMessages()
    {
        session()->forget('ok');
        session()->forget('bad');
        session()->forget('warn');
        session()->forget('info');
    }
}
