@extends('master')
@section('roles', 'active')
@section('content')
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Forms/</span> Assign Permissions to Roles</h4>

        <!-- Permissions Assignment Form -->
        <div class="row">
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Assign Permissions to Role</h5>
                        <small class="text-muted float-end">Select multiple permissions for a role</small>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('roles.assignPermissions') }}" method="POST">
                            @csrf
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="role-select">Role</label>
                                <div class="col-sm-10">
                                    <select id="role-select" name="role_id" class="form-control">
                                        <option value="">Select a role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="permissions-select">Permissions</label>
                                <div class="col-sm-10">
                                    <!-- Loop through all permissions and create checkboxes -->
                                    @foreach ($permissions as $permission)
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   name="permissions[]" 
                                                   class="form-check-input" 
                                                   value="{{ $permission->id }}" 
                                                   id="permission-{{ $permission->id }}">
                                            <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @if (auth()->user() && auth()->user()->can('manage_permissions'))
                                <div class="row justify-content-end">
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-primary">Save Permissions</button>
                                    </div>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->

    <!-- Footer -->
    <footer class="content-footer footer bg-footer-theme">
        <!-- Footer content -->
    </footer>
    <!-- / Footer -->

    <div class="content-backdrop fade"></div>
</div>

<!-- Add the AJAX script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // When the role is selected
        $('#role-select').change(function() {
            var roleId = $(this).val();

            // If a role is selected, send the AJAX request to get permissions
            if (roleId) {
                $.ajax({
                    url: '/roles/' + roleId + '/permissions',  // Make sure this URL matches your route
                    method: 'GET',
                    success: function(response) {
                        // Uncheck all checkboxes
                        $('input[name="permissions[]"]').prop('checked', false);

                        // Check the permissions assigned to the selected role
                        response.permissions.forEach(function(permissionId) {
                            $('#permission-' + permissionId).prop('checked', true);
                        });
                    },
                    error: function() {
                        alert('Error loading permissions.');
                    }
                });
            }
        });
    });
</script>

@endsection
