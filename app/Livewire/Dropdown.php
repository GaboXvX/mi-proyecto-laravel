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
    public $sectores = [];
    public $comunidades = [];

    public $parroquiasId;  
    public $urbanizacionesId;  
    public $sectoresId;
    public $comunidadesId;

    protected $listeners = ['editarIncidencia' => 'cargarIncidencia'];

    public function mount($parroquiaId = null, $urbanizacionId = null, $sectorId = null, $comunidadId = null)
    {
        $this->parroquias = Parroquia::all();
        $this->urbanizaciones = collect();
        $this->sectores = collect();
        $this->comunidades = collect();

        $this->parroquiasId = $parroquiaId;
        if ($this->parroquiasId) {
            $this->urbanizaciones = Urbanizacion::where('id_parroquia', $this->parroquiasId)->get();
        }

        $this->urbanizacionesId = $urbanizacionId;
        if ($this->urbanizacionesId) {
            $this->sectores = Sector::where('id_urbanizacion', $this->urbanizacionesId)->get();
        }

        $this->sectoresId = $sectorId;
        if ($this->sectoresId) {
            $this->comunidades = Comunidad::where('id_sector', $this->sectoresId)->get();
        }

        $this->comunidadesId = $comunidadId;
    }


    public function updatedParroquiasId($value)
    {
        $this->urbanizaciones = Urbanizacion::where('id_parroquia', $value)->get();

        $this->urbanizacionesId = null;
        $this->sectores = collect();
        $this->sectoresId = null;
        $this->comunidades = collect();
        $this->comunidadesId = null;
    }

    public function updatedUrbanizacionesId($value)
    {
        $this->sectores = Sector::where('id_urbanizacion', $value)->get();

        $this->sectoresId = null;
        $this->comunidades = collect();
        $this->comunidadesId = null;
    }

    public function updatedSectoresId($value)
    {
        $this->comunidades = Comunidad::where('id_sector', $value)->get();
        $this->comunidadesId = null;
    }

    public function cargarIncidencia($incidencia)
    {
        // Puedes pasar el modelo completo o un array con los IDs
        $this->parroquiasId = $incidencia['id_parroquia'];
        $this->urbanizaciones = Urbanizacion::where('id_parroquia', $this->parroquiasId)->get();

        $this->urbanizacionesId = $incidencia['id_urbanizacion'];
        $this->sectores = Sector::where('id_urbanizacion', $this->urbanizacionesId)->get();

        $this->sectoresId = $incidencia['id_sector'];
        $this->comunidades = Comunidad::where('id_sector', $this->sectoresId)->get();

        $this->comunidadesId = $incidencia['id_comunidad'];
    }

    public function render()
    {
        return view('livewire.dropdown');
    }
}
