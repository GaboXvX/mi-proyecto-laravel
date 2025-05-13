<div class="container mt-4">

    <h2 class="text-center mb-3">Configuración de Cuenta</h2>

    <ul class="nav nav-pills nav-fill gap-2 mb-3 rounded-pill" style="--bs-nav-link-color: #6c757d; --bs-nav-pills-link-active-color: #fff; --bs-nav-pills-link-active-bg: #007bff;">
        <li class="nav-item">
            <a href="#" wire:click.prevent="setSection('profile')" class="nav-link rounded-pill {{ $section === 'profile' ? 'active' : '' }}">Perfil</a>
        </li>
        <li class="nav-item">
            <a href="#" wire:click.prevent="setSection('security')" class="nav-link rounded-pill {{ $section === 'security' ? 'active' : '' }}">Seguridad</a>
        </li>
    </ul>

    @if ($section === 'profile')
        @include('livewire.sections.profile')
    @elseif ($section === 'security')
        @include('livewire.sections.seguridad')
    @endif

</div>