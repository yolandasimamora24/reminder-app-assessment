@extends(backpack_view('blank'))

@php

    if (backpack_theme_config('show_getting_started')) {
        if (backpack_user()->getAttributes()['user_type'] != 'admin') {
            $view_page = 'appointment_calendar';
        } else {
            $view_page = 'dashboard_graphs';
        }

        $widgets['before_content'][] = [
            'type' => 'view',
            'view' => $view_page,
        ];
    } else {
        $widgets['before_content'][] = [
            'type'        => 'jumbotron',
            'heading'     => trans('backpack::base.welcome'),
            'content'     => trans('backpack::base.use_sidebar'),
            'button_link' => backpack_url('logout'),
            'button_text' => trans('backpack::base.logout'),
        ];
    }
@endphp

@section('content')
    {{-- Your content goes here --}}
@endsection
