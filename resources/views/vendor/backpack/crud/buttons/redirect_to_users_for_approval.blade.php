@php
    $category = app('request')->input('category');
@endphp

@if($category)
    <h5 class="d-inline">{{ Str::title($category) }}</h5>
@endif
<a href="user-approval" class="font-sm pl-4"><i class="la la-angle-double-left"></i> Back to Users for approval</a>

<style>
    #remove_filters_button, li[filter-name="category"] {
        display: none;
    }
</style>
