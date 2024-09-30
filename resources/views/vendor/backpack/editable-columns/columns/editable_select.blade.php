{{-- custom editable column --}}
@php
    $column['value'] = $column['value'] ?? data_get($entry, $column['name']);

    if(is_callable($column['value'])) {
        $column['value'] = $column['value']($entry);
    }

    if(is_callable($column['options'])) {
        $column['options'] = $column['options']($entry);
    }

    if($column['init'] ?? false) {
        $column['options'] = ['' => ''] + [$column['value'] => $column['value']] + $column['options'];
    }

    $column['fake'] = $column['fake'] ?? false;
    $column['store_in'] = $column['store_in'] ?? 'extras';

    $column['min_width'] = $column['min_width'] ?? "120px";
    $column['id'] = Str::random(30);
    $column['select2'] = (isset($column['select2']) && $column['select2']) ? 'true' : 'false';
    $column['confirm'] = $column['confirm'] ?? false;
@endphp

<span
    data-column-type="text"
    data-column-editable="true"
    data-column-initial-value="{{ $column['value'] }}"
    data-column-events-registered="false"
    data-column-name="{{ $column['name'] }}"
    data-column-save-on-focusout="{{ $column['save_on_focusout'] ?? true }}"
    data-column-save-on-change="{{ $column['save_on_change'] ?? true }}"
    data-entry-id="{{ $entry->getKey() }}"
    data-route="{{ $column['route'] ?? url($crud->getRoute().'/minor-update') }}"
    data-text-color-unsaved="{{ $column['text_color_unsaved'] ?? '#869ab8' }}"
    data-on-error-text-color="{{ $column['on_error']['text_color'] ?? '#df4759' }}"
    data-on-error-text-color-duration="{{ $column['on_error']['text_color_duration'] ?? 0 }}"
    data-on-error-text-value-undo="{{ $column['on_error']['text_value_undo'] ?? false }}"
    data-on-success-text-color="{{ $column['on_success']['text_color'] ?? '#42ba96' }}"
    data-on-success-text-color-duration="{{ $column['on_success']['text_color_duration'] ?? 3000 }}"
    data-auto-update-row="{{ $column['auto_update_row'] ?? true }}"
    data-fake="{{ $column['fake'] ? $column['store_in'] : false }}"
    title="{{ __('backpack.editable-columns::minor_update.tooltip') }}">

    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_start')
        <select
            name="{{ $column['name'] }}"
            data-focus="{{ $column['name'] }}"
            id="{{ $column['id'] }}"
            style="width: 100%; min-width: {{ $column['min_width'] }}; border: none; {{ ($column['underlined'] ?? true) ? "border-bottom: 1px dashed #abbcd5;" : "" }} background: none; text-overflow: ellipsis;"
            onfocus="registerMinorEditInputEvents(this, event)">
            @foreach($column['options'] as $value => $option)
                <option value="{{ $value }}" {{ $column['value'] == $value ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_end')
</span>


<script>
    function getRandomInt(min, max) {
        min = Math.ceil(min);
        max = Math.floor(max);
        return Math.floor(Math.random() * (max - min) + min);
    }
    function init{{$column['id']}}() {
        $('#{{$column['id']}}+.select2').remove()
        $('#{{$column['id']}}').data('init', getRandomInt(10, 1000000));
        if($('#{{$column['id']}}').data('select2-id')) {
            $('#{{$column['id']}}').select2('destroy')
        }
        $('#{{$column['id']}}').select2({
            width: '{{$column['min_width']}}',
        })
        @if ($column['confirm'])
            .on('select2:selecting', function (e) {
                if(!confirm("{{$column['confirm']}}")) {
                    e.preventDefault();
                }
            });
        @endif
        $('body').on('change', '#{{$column['id']}}', function() {
            $(this).focus();
            const intval = setInterval(function() {
                if(!$('#{{$column['id']}}').data('init')) {
                    init{{$column['id']}}();
                    if (window["{{$column['id']}}"] !== 'reset') {
                        clearInterval(intval);
                    }
                }
            }, 1000);
        });
    }
    if({{$column['select2']}}) {
        if ($.fn.select2) {
            init{{$column['id']}}();
        }
    }
</script>
