{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
<li class="nav-title">Menu</li>
<li class="nav-item">
    <a class="nav-link" href="{{ backpack_url('dashboard') }}">
        <i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}
    </a>
</li>

<li class="nav-title">Admin</li>

<li class="nav-item">
    <a class="nav-link" href="{{ backpack_url('provider') }}">
        <i class="nav-icon la la-user-nurse"></i> 
        Providers
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ backpack_url('billing') }}">
        <i class="nav-icon la la-receipt"></i> 
        Billings
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ backpack_url('appointment') }}">
        <i class="nav-icon la la-user"></i> 
        Appointments
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ backpack_url('allergy') }}">
        <i class="nav-icon la la-user"></i> 
        Allergies
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ backpack_url('diagnostic') }}">
        <i class="nav-icon la la-user"></i> 
        Diagnostic
    </a>
    <a class="nav-link" href="{{ backpack_url('medicine') }}">
        <i class="nav-icon la la-user"></i> 
        Medicines
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ backpack_url('labs') }}">
        <i class="nav-icon la la-clipboard"></i> 
        Laboratories
    </a>
</li>
<!-- Users, Roles, Permissions -->
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-users"></i> Authentication</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>Users</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-id-badge"></i> <span>Roles</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon la la-key"></i> <span>Permissions</span></a></li>
    </ul>
</li>