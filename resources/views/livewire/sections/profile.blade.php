<style>
    .profile-table {
        width: 100%;
        margin-bottom: 1.5rem;
    }
    
    .profile-table th {
        width: 30%;
        padding: 0.75rem;
    }
    
    .profile-table td {
        padding: 0.75rem;
    }
</style>

<div class="table-container">
    <!-- Mostrar datos del usuario -->
    <table class="profile-table table table-bordered">
                <tr>
                    <th>Nombre:</th>
                    <td>{{ auth()->user()->empleadoAutorizado->nombre }}</td>
                </tr>
                <tr>
                    <th>Apellido:</th>
                    <td>{{ auth()->user()->empleadoAutorizado->apellido }}</td>
                </tr>
                <tr>
                    <th>Cédula:</th>
                    <td>{{ auth()->user()->empleadoAutorizado->cedula }}</td>
                </tr>
                <tr>
                    <th>Género:</th>
                    <td>{{ auth()->user()->empleadoAutorizado->genero == 'M' ? 'Masculino' : 'Femenino' }}</td>
                </tr>
                <tr>
                    <th>Fecha de Nacimiento:</th>
                    <td>{{ auth()->user()->empleadoAutorizado->fecha_nacimiento }}</td>
                </tr>
                <tr>
                    <th>Altura:</th>
                    <td>{{ auth()->user()->empleadoAutorizado->altura }} cm</td>
                </tr>
                <tr>
                    <th>Teléfono:</th>
                    <td>{{ auth()->user()->empleadoAutorizado->telefono }}</td>
                </tr>
                <tr>
                    <th>Cargo:</th>
                    <td>{{ auth()->user()->empleadoAutorizado->cargo->nombre_cargo }}</td>
                </tr>
            </table>
</div>