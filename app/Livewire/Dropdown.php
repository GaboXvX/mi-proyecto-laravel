<?php

namespace App\Livewire;

use App\Models\Comunidad;
use App\Models\Parroquia;
use App\Models\Sector;
use App\Models\Urbanizacion;
use Livewire\Component;

class Dropdown extends Component
{
    public $parroquias;
    public $urbanizaciones = [];
    public $sectores=[];
    public $comunidades=[];
    public $parroquiasId;  
    public $urbanizacionesId;  
    public $sectoresId;
    public $comunidadesId;
    public function mount()
    {
        $this->parroquias = Parroquia::all();
        $this->urbanizaciones = collect();  
        $this->sectores= collect();
        $this->comunidades=collect();
    }

   
    public function updatedParroquiasId($value)
    {
       
        $this->urbanizaciones = Urbanizacion::where('id_parroquia', $value)->get();
    }
    public function updatedUrbanizacionesId($value)
    {
       
        $this->sectores = Sector::where('id_urbanizacion', $value)->get();
    }
    public function updatedSectoresid($value){
        $this->comunidades=Comunidad::where('id_sector',$value)->get();
    }
    public function render()
    {
        return view('livewire.dropdown');
    }
}
