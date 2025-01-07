<?php

namespace App\Livewire;

use App\Models\Parroquia;
use App\Models\Sector;
use App\Models\Urbanizacion;
use Livewire\Component;

class DropdownPersona extends Component
    {
        public $parroquias;
        public $urbanizaciones = [];
        public $sectores=[];
        public $parroquiasId;  
        public $urbanizacionesId;  
        public $sectoresId;
        public function mount()
        {
            $this->parroquias = Parroquia::all();
            $this->urbanizaciones = collect();  
            $this->sectores= collect();
        }
    
       
        public function updatedParroquiasId($value)
        {
           
            $this->urbanizaciones = Urbanizacion::where('id_parroquia', $value)->get();
        }
        public function updatedUrbanizacionesId($value)
        {
           
            $this->sectores = Sector::where('id_urbanizacion', $value)->get();
        }  
    public function render()
    {
        return view('livewire.dropdown-persona');
    }
}
