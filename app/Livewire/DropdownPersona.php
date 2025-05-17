<?php

namespace App\Livewire;

use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia;
use App\Models\Urbanizacion;
use App\Models\Sector;
use App\Models\Comunidad;
use Livewire\Component;

class DropdownPersona extends Component
{
    // Propiedades públicas para almacenar las listas de opciones
    public $estados;
    public $municipios = [];
    public $parroquias = [];
    public $urbanizaciones = [];
    public $sectores = [];
    public $comunidades = [];

    // Propiedades públicas para almacenar los valores seleccionados
    public $estadoId;
    public $municipioId;
    public $parroquiaId;
    public $urbanizacionId;
    public $sectorId;
    public $comunidadId;

    // Método que se ejecuta al inicializar el componente
    public function mount(
        $estadoId = null, 
        $municipioId = null, 
        $parroquiaId = null, 
        $urbanizacionId = null, 
        $sectorId = null, 
        $comunidadId = null
    ) {
        // Cargar todos los estados
        $this->estados = Estado::all();
        
        // Establecer valores iniciales si se proporcionan
        $this->estadoId = $estadoId;
        $this->municipioId = $municipioId;
        $this->parroquiaId = $parroquiaId;
        $this->urbanizacionId = $urbanizacionId;
        $this->sectorId = $sectorId;
        $this->comunidadId = $comunidadId;
        
        // Cargar datos dependientes basados en los valores iniciales
        if ($this->estadoId) {
            $this->municipios = Municipio::where('id_estado', $this->estadoId)->get();
        }
        if ($this->municipioId) {
            $this->parroquias = Parroquia::where('id_municipio', $this->municipioId)->get();
        }
        if ($this->parroquiaId) {
            $this->urbanizaciones = Urbanizacion::where('id_parroquia', $this->parroquiaId)->get();
        }
        if ($this->urbanizacionId) {
            $this->sectores = Sector::where('id_urbanizacion', $this->urbanizacionId)->get();
        }
        if ($this->sectorId) {
            $this->comunidades = Comunidad::where('id_sector', $this->sectorId)->get();
        }
    }

    // Método que se ejecuta cuando se actualiza el estado seleccionado
    public function updatedEstadoId($value)
    {
        $this->municipios = Municipio::where('id_estado', $value)->get();
        $this->reset(['municipioId', 'parroquiaId', 'urbanizacionId', 'sectorId', 'comunidadId']);
    }

    // Método que se ejecuta cuando se actualiza el municipio seleccionado
    public function updatedMunicipioId($value)
    {
        $this->parroquias = Parroquia::where('id_municipio', $value)->get();
        $this->reset(['parroquiaId', 'urbanizacionId', 'sectorId', 'comunidadId']);
    }

    // Método que se ejecuta cuando se actualiza la parroquia seleccionada
    public function updatedParroquiaId($value)
    {
        $this->urbanizaciones = Urbanizacion::where('id_parroquia', $value)->get();
        $this->reset(['urbanizacionId', 'sectorId', 'comunidadId']);
    }

    // Método que se ejecuta cuando se actualiza la urbanización seleccionada
    public function updatedUrbanizacionId($value)
    {
        $this->sectores = Sector::where('id_urbanizacion', $value)->get();
        $this->reset(['sectorId', 'comunidadId']);
    }

    // Método que se ejecuta cuando se actualiza el sector seleccionado
    public function updatedSectorId($value)
    {
        $this->comunidades = Comunidad::where('id_sector', $value)->get();
        $this->reset(['comunidadId']);
    }

    // Método que renderiza la vista
    public function render()
    {
        return view('livewire.dropdown-persona');
    }
}