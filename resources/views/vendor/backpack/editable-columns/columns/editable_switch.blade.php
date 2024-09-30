{{-- custom editable column --}}
@php
    $column['value'] = $column['value'] ?? data_get($entry, $column['name']);

    if(is_callable($column['value'])) {
        $column['value'] = $column['value']($entry);
    }
    $column['underlined'] = $column['underlined'] ?? false;
    $column['onLabel'] = $column['onLabel'] ?? $column['options'][0] ?? '';
    $column['offLabel'] = $column['offLabel'] ?? $column['options'][1] ?? '';
    $column['color'] = $column['color'] ?? 'primary';
    $column['fake'] = $column['fake'] ?? false;
    $column['store_in'] = $column['store_in'] ?? 'extras';
    $column['confirm'] = $column['confirm'] ?? false;
    if(isset($column['metadata']) && is_array($column['metadata'])) {
        foreach ($column['metadata'] as $key => $value) {
            $column[$key] = $value ? $entry->$key : null;
        }
    } else {
        $column['metadata'] = [];
    }
@endphp

<span
    data-column-type="checkbox"
    data-column-editable="true"
    data-column-initial-value="{{ $column['value'] ? 1 : 0 }}"
    data-column-events-registered="false"
    data-column-name="{{ $column['name'] }}"
    data-entry-id="{{ $entry->getKey() }}"
    data-route="{{ $column['route'] ?? url($crud->getRoute().'/minor-update') }}"
    data-on-error-status-color="{{ $column['on_error']['status_color'] ?? '#df4759' }}"
    data-on-error-status-color-duration="{{ $column['on_error']['status_color_duration'] ?? 0 }}"
    data-on-error-switch-value-undo="{{ $column['on_error']['switch_value_undo'] ?? true }}"
    data-on-success-status-color="{{ $column['on_success']['status_color'] ?? '#42ba96' }}"
    data-on-success-status-color-duration="{{ $column['on_success']['status_color_duration'] ?? 3000 }}"
    data-auto-update-row="{{ $column['auto_update_row'] ?? true }}"
    data-fake="{{ $column['fake'] ? $column['store_in'] : false }}"
    title="{{ __('backpack.editable-columns::minor_update.tooltip') }}"
    class="d-flex flex-column">

    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_start')
        <div style="display: grid; grid-template-columns: fit-content(0%) 1fr;">
            {{-- Switch --}}
            <label class="form-switch switch switch-sm switch-label switch-pill switch-{{ $column['color'] }}" style="margin-bottom: 0;">
                <input
                    type="checkbox"
                    name="{{ $column['name'] }}"
                    @foreach ($column['metadata'] as $key => $value)
                        {{ $column[$key] ? 'data-' . str_replace('_', '-', $key) . '=' . $column[$key] : null }}
                    @endforeach
                    {{ $column['value'] ? 'checked' : '' }}
                    class="switch-input form-check-input"
                    onchange="registerMinorEditInputEvents(this, event, '{{ $column['confirm'] }}')"/>
                <span
                    class="switch-slider"
                    data-focus="{{ $column['name'] }}"
                    data-checked="{{ $column['onLabel'] }}"
                    data-unchecked="{{ $column['offLabel'] }}"
                    tabindex="0"
                    onfocus="registerMinorEditInputEvents(this.previousElementSibling, event, '{{ $column['confirm'] }}')"></span>
            </label>
            {{-- Status --}}
            <span class="ec-status" style="width: 10px; height: 10px; display: inline-block; border-radius: 50%; opacity: 0.8; margin: 6px;"></span>
            {{-- Underline --}}
            @if ($column['underlined'])
                <span style="width: calc(100% - 4px); margin: 2px 2px 0; display: block; border-top: 1px dashed #abbcd5"></span>
            @endif
        </div>
    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_end')
</span>
