{{-- This file is used for menu items by any Backpack v6 theme --}}



<x-backpack::menu-separator title="Menu" />

<x-backpack::menu-item title="Dashboard" icon="la la-dashboard" :link="backpack_url('dashboard')" />

<x-backpack::menu-item title="Reminder" icon="la la-user" :link="backpack_url('reminder')" />
<x-backpack::menu-item title="User" icon="la la-user" :link="backpack_url('user')" />