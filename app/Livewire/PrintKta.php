<?php

namespace App\Livewire;

use App\Models\PrintLog;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class PrintKta extends Component
{
    public int $printLogId;
    public bool $loading = false;
    public ?string $errorMessage = null;

    public function mount(int $printLogId): void
    {
        $this->printLogId = $printLogId;
    }

    public function render()
    {
        return view('livewire.print-kta');
    }

    /**
     * Dipanggil dari blade via wire:click — redirect ke URL download PDF
     */
    public function downloadPdf(): void
    {
        $this->loading = true;
        $this->redirect(route('kta.pdf', $this->printLogId));
    }
}
