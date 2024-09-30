@php
    $category = app('request')->input('category');
@endphp

@if($category)
    <h5 class="d-inline">{{ Str::title($category) }}</h5>
@endif
<a href="approved-users" class="btn btn-primary font-sm pl-4">View all Approved Users</a>

<style>
    #remove_filters_button, li[filter-name="category"] {
        display: none;
    }
</style>
