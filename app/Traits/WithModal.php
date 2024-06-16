<?php

namespace App\Traits;

trait WithModal
{
    public bool $openModal = false;

    public function openDialogModal(): void
    {
        $this->resetErrorBag();
        $this->openModal = true;
    }
}