@if ($crud->hasAccess('update'))
<a href="{{ url($crud->route.'/'.$entry->getKey().'/change-password') }} " data-toggle="tooltip" title="Just a demo custom button." class="btn btn-sm btn-link"><i class="la la-key"></i> Change Password</a>
@endif