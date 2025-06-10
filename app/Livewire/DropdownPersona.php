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

    // Propiedades para nueva urbanización/comunidad y mensajes
    public $nuevaUrbanizacionNombre = '';
    public $mensajeUrbanizacion = '';
    public $nuevaComunidadNombre = '';
    public $mensajeComunidad = '';
    public $nuevoSectorNombre = '';
    public $mensajeSector = '';

    protected $rules = [
    'nuevaUrbanizacionNombre' => 'required|string|min:3|max:100',
    'nuevoSectorNombre' => 'required|string|min:3|max:100',
    'nuevaComunidadNombre' => 'required|string|min:3|max:100',
];

// Mensajes de error personalizados en español
protected function messages()
{
    return [
        'nuevaUrbanizacionNombre.required' => 'El nombre de la urbanización es obligatorio.',
        'nuevaUrbanizacionNombre.min' => 'El nombre de la urbanización debe tener al menos 3 caracteres.',
        'nuevaUrbanizacionNombre.max' => 'El nombre de la urbanización no puede superar los 100 caracteres.',

        'nuevoSectorNombre.required' => 'El nombre del sector es obligatorio.',
        'nuevoSectorNombre.min' => 'El nombre del sector debe tener al menos 3 caracteres.',
        'nuevoSectorNombre.max' => 'El nombre del sector no puede superar los 100 caracteres.',

        'nuevaComunidadNombre.required' => 'El nombre de la comunidad es obligatorio.',
        'nuevaComunidadNombre.min' => 'El nombre de la comunidad debe tener al menos 3 caracteres.',
        'nuevaComunidadNombre.max' => 'El nombre de la comunidad no puede superar los 100 caracteres.',
    ];
}

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
        $this->mensajeUrbanizacion = '';
    }

    // Método que se ejecuta cuando se actualiza el sector seleccionado
    public function updatedSectorId($value)
    {
        $this->comunidades = Comunidad::where('id_sector', $value)->get();
        $this->reset(['comunidadId']);
        $this->mensajeSector = '';
    }

    public function crearUrbanizacion()
    {
        $this->validateOnly('nuevaUrbanizacionNombre');
        if (empty($this->parroquiaId)) {
        $this->addError('nuevaUrbanizacionNombre', 'Debe seleccionar una parroquia antes de crear una urbanización.');
        return;
    }
        $nombre = trim($this->nuevaUrbanizacionNombre);
        $nombreLower = mb_strtolower($nombre);
        // Prohibir si existe como parroquia
        if (Parroquia::whereRaw('LOWER(nombre) = ?', [$nombreLower])->exists()) {
            $this->addError('nuevaUrbanizacionNombre', "El nombre ingresado coincide con una parroquia existente. Por favor, elija otro.");
            return;
        }
        // Prohibir si existe como urbanización, sector o comunidad en la misma parroquia
        $existeUrbanizacion = Urbanizacion::whereRaw('LOWER(nombre) = ?', [$nombreLower])
            ->where('id_parroquia', $this->parroquiaId)
            ->exists();
        $existeSector = Sector::whereRaw('LOWER(nombre) = ?', [$nombreLower])
            ->whereHas('urbanizacion', function($q) { $q->where('id_parroquia', $this->parroquiaId); })
            ->exists();
        $sectoresParroquia = Sector::whereHas('urbanizacion', function($q) { $q->where('id_parroquia', $this->parroquiaId); })->pluck('id_sector');
        $existeComunidad = Comunidad::whereRaw('LOWER(nombre) = ?', [$nombreLower])
            ->whereIn('id_sector', $sectoresParroquia)
            ->exists();
        if ($existeUrbanizacion || $existeSector || $existeComunidad) {
            $this->addError('nuevaUrbanizacionNombre', "Ya existe una urbanización, sector o comunidad con este nombre en la parroquia seleccionada.");
            return;
        }
        $urbanizacion = Urbanizacion::create([
            'nombre' => $nombre,
            'id_parroquia' => $this->parroquiaId,
        ]);
        $this->urbanizaciones = Urbanizacion::where('id_parroquia', $this->parroquiaId)->get();
        $this->urbanizacionId = $urbanizacion->id_urbanizacion;
        $this->mensajeUrbanizacion = '¡Urbanización agregada exitosamente!';
        $this->nuevaUrbanizacionNombre = '';
    }

    public function crearSector()
{
    $this->validateOnly('nuevoSectorNombre');
    
    // Si se seleccionó "Agregar nueva urbanización..."
    if ($this->urbanizacionId === 'agregar_nueva_urbanizacion') {
        // Validar primero el nombre de la nueva urbanización
        $this->validate([
    'nuevaUrbanizacionNombre' => 'required|string|min:3|max:100',
], [
    'nuevaUrbanizacionNombre.required' => 'El nombre de la urbanización es obligatorio.',
    'nuevaUrbanizacionNombre.string'   => 'El nombre debe ser un texto válido.',
    'nuevaUrbanizacionNombre.min'      => 'El nombre debe tener al menos 3 caracteres.',
    'nuevaUrbanizacionNombre.max'      => 'El nombre no puede exceder los 100 caracteres.',
]);
        
        // Verificar duplicados (copiado de tu método crearUrbanizacion)
        $nombreLower = mb_strtolower(trim($this->nuevaUrbanizacionNombre));
        if (Parroquia::whereRaw('LOWER(nombre) = ?', [$nombreLower])->exists()) {
            $this->addError('nuevaUrbanizacionNombre', "El nombre ingresado coincide con una parroquia existente. Por favor, elija otro.");
            return;
        }
        
        $existeUrbanizacion = Urbanizacion::whereRaw('LOWER(nombre) = ?', [$nombreLower])
            ->where('id_parroquia', $this->parroquiaId)
            ->exists();
        // ... (otros checks de duplicados como en tu método original)
        
        if ($existeUrbanizacion /* || otros checks */) {
            $this->addError('nuevaUrbanizacionNombre', "Ya existe una urbanización con este nombre en la parroquia seleccionada.");
            return;
        }
        
        // Crear la nueva urbanización primero
        $urbanizacion = Urbanizacion::create([
            'nombre' => trim($this->nuevaUrbanizacionNombre),
            'id_parroquia' => $this->parroquiaId,
        ]);
        
        // Actualizar el ID de urbanización con el nuevo creado
        $this->urbanizacionId = $urbanizacion->id_urbanizacion;
        $this->urbanizaciones = Urbanizacion::where('id_parroquia', $this->parroquiaId)->get();
    }
    
    // Validar que urbanizacionId sea numérico
    if (!is_numeric($this->urbanizacionId)) {
        $this->addError('nuevoSectorNombre', 'Debe seleccionar o crear una urbanización válida.');
        return;
    }
    
    // Resto de validaciones para el sector (como en tu código original)
    $nombre = trim($this->nuevoSectorNombre);
    $nombreLower = mb_strtolower($nombre);
    $urbanizacion = Urbanizacion::find($this->urbanizacionId);
    $parroquiaId = $urbanizacion?->id_parroquia;
    
    // ... (resto de tus validaciones de duplicados)
    
    // Crear el sector
    $sector = Sector::create([
        'nombre' => $nombre,
        'id_urbanizacion' => $this->urbanizacionId,
    ]);
    
    $this->sectores = Sector::where('id_urbanizacion', $this->urbanizacionId)->get();
    $this->sectorId = $sector->id_sector;
    $this->mensajeSector = '¡Sector agregado exitosamente!';
    $this->reset(['nuevoSectorNombre', 'nuevaUrbanizacionNombre']);
}

    public function crearComunidad()
    {
        $this->validateOnly('nuevaComunidadNombre');
        if (!$this->sectorId) {
            $this->addError('nuevaComunidadNombre', 'Debe seleccionar un sector antes de crear una comunidad.');
            return;
        }
        $nombre = trim($this->nuevaComunidadNombre);
        $nombreLower = mb_strtolower($nombre);
        $sector = Sector::find($this->sectorId);
        $urbanizacion = $sector ? Urbanizacion::find($sector->id_urbanizacion) : null;
        $parroquiaId = $urbanizacion?->id_parroquia;
        // Prohibir si existe como parroquia
        if (Parroquia::whereRaw('LOWER(nombre) = ?', [$nombreLower])->exists()) {
            $this->addError('nuevaComunidadNombre', "El nombre ingresado coincide con una parroquia existente. Por favor, elija otro.");
            return;
        }
        // Prohibir si existe como comunidad, sector o urbanización en la misma parroquia
        $sectoresParroquia = $parroquiaId ? Sector::whereHas('urbanizacion', function($q) use ($parroquiaId) { $q->where('id_parroquia', $parroquiaId); })->pluck('id_sector') : [];
        $existeComunidad = Comunidad::whereRaw('LOWER(nombre) = ?', [$nombreLower])
            ->whereIn('id_sector', $sectoresParroquia)
            ->exists();
        $existeSector = Sector::whereRaw('LOWER(nombre) = ?', [$nombreLower])
            ->whereHas('urbanizacion', function($q) use ($parroquiaId) { $q->where('id_parroquia', $parroquiaId); })
            ->exists();
        $existeUrbanizacion = Urbanizacion::whereRaw('LOWER(nombre) = ?', [$nombreLower])
            ->where('id_parroquia', $parroquiaId)
            ->exists();
        if ($existeComunidad || $existeSector || $existeUrbanizacion) {
            $this->addError('nuevaComunidadNombre', "Ya existe una comunidad, sector o urbanización con este nombre en la parroquia seleccionada.");
            return;
        }
        $comunidad = Comunidad::create([
            'nombre' => $nombre,
            'id_sector' => $this->sectorId,
        ]);
        $this->comunidades = Comunidad::where('id_sector', $this->sectorId)->get();
        $this->comunidadId = $comunidad->id_comunidad;
        $this->mensajeComunidad = '¡Comunidad agregada exitosamente!';
        $this->nuevaComunidadNombre = '';
    }

    // Limpiar mensajes al cambiar selección
    public function updatedComunidadId($value)
    {
        $this->mensajeComunidad = '';
    }

    // Método que renderiza la vista
    public function render()
    {
        return view('livewire.dropdown-persona');
    }
}