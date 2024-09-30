@php
    $category = app('request')->input('category');
@endphp

@if($category)
    <h5 class="d-inline">{{ Str::title($category) }}</h5>
@endif
<a href="resubmission-categories" class="font-sm pl-4"><i class="la la-angle-double-left"></i> Back to Categories</a>

<style>
    #remove_filters_button, li[filter-name="category"] {
        display: none;
    }
</style>
