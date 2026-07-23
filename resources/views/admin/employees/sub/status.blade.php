<label class="form-check form-switch form-check-custom form-check-solid">
    <input class="form-check-input btn active_operation" style="margin: 0 auto;" type="checkbox" name="active"
           value="1" data-url="{{ route('admin.employees.active', $instance->id) }}"
           {{ $instance->is_active ? 'checked' : '' }} data-title="{{ $instance->name }}" />
    <span class="form-check-label fw-bold text-muted"></span>
</label>
