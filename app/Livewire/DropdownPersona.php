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
    public function mount()
    {
        $this->estados = Estado::all(); // Cargar todos los estados
        $this->municipios = collect(); // Inicializar municipios como colección vacía
        $this->parroquias = collect(); // Inicializar parroquias como colección vacía
        $this->urbanizaciones = collect(); // Inicializar urbanizaciones como colección vacía
        $this->sectores = collect(); // Inicializar sectores como colección vacía
        $this->comunidades = collect(); // Inicializar comunidades como colección vacía
    }

    // Método que se ejecuta cuando se actualiza el estado seleccionado
    public function updatedEstadoId($value)
    {
        $this->municipios = Municipio::where('id_estado', $value)->get();
        $this->reset(['municipioId', 'parroquiaId', 'urbanizacionId', 'sectorId', 'comunidadId']); // Resetear valores dependientes
    }

    // Método que se ejecuta cuando se actualiza el municipio seleccionado
    public function updatedMunicipioId($value)
    {
        $this->parroquias = Parroquia::where('id_municipio', $value)->get();
        $this->reset(['parroquiaId', 'urbanizacionId', 'sectorId', 'comunidadId']); // Resetear valores dependientes
    }

    // Método que se ejecuta cuando se actualiza la parroquia seleccionada
    public function updatedParroquiaId($value)
    {
        $this->urbanizaciones = Urbanizacion::where('id_parroquia', $value)->get();
        $this->reset(['urbanizacionId', 'sectorId', 'comunidadId']); // Resetear valores dependientes
    }

    // Método que se ejecuta cuando se actualiza la urbanización seleccionada
    public function updatedUrbanizacionId($value)
    {
        $this->sectores = Sector::where('id_urbanizacion', $value)->get();
        $this->reset(['sectorId', 'comunidadId']); // Resetear valores dependientes
    }

    // Método que se ejecuta cuando se actualiza el sector seleccionado
    public function updatedSectorId($value)
    {
        $this->comunidades = Comunidad::where('id_sector', $value)->get();
        $this->reset(['comunidadId']); // Resetear valores dependientes
    }

    // Método que renderiza la vista
    public function render()
    {
        return view('livewire.dropdown-persona');
    }
}