<?php

namespace App\Livewire;

use Livewire\Component;
// app/Http/Livewire/Sidebar.php
class Sidebar extends Component
{
    public $isOpen = true;

    public function toggleSidebar()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function render()
    {
        return view('livewire.sidebar');
    }
}

?>