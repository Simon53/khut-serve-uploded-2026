@extends('layout.app')
@section('title', 'Khut::User')

@section('content')
<div class="page-header">
    <h3 class="page-title"> Create User </h3>
</div>

<div class="row">
    <div class="col-md-12 mb-4">
        <button type="button" class="btn btn-info btn-fw" data-toggle="modal" data-target="#addUserModal">+ Add User</button>
    </div>
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody id="userTable">
                            @foreach($users as $key => $user)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->role }}</td>
                                    <td><button class="btn btn-warning btn-sm editBtn" data-id="{{ $user->id }}">Edit</button></td>
                                    <td><button class="btn btn-danger btn-sm deleteBtn" data-id="{{ $user->id }}">Delete</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add/Edit User Modal --}}
<!-- SINGLE MODAL (ADD + EDIT) -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title text-black">Add New User</h5>
                <button type="button" class="btn-danger" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <form id="addUserForm">
                    @csrf

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control">
                        <small>Leave blank for edit</small>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                            <option value="Administrator">Administrator</option>
                            <option value="Moderator">Moderator</option>
                            <option value="Editor">Editor</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success">Save</button>
                </form>

            </div>

        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){

    // =========================
    // ADD + UPDATE USER
    // =========================
    $(document).on('submit', '#addUserForm', function(e){
        e.preventDefault();

        let form = $(this);
        let id = form.attr('data-id');
        let btn = form.find('button[type="submit"]');

        btn.prop('disabled', true);

        let url = id 
            ? "/bd-admin/user/update/" + id 
            : "{{ route('user.store') }}";

        $.ajax({
            url: url,
            method: "POST",
            data: form.serialize(),
            success: function(res){
                btn.prop('disabled', false);

                if(res.success){
                    $('#addUserModal').modal('hide');
                    toastr.success(res.message);

                    form[0].reset();
                    form.removeAttr('data-id');

                    setTimeout(() => location.reload(), 500);
                }
            },
            error: function(xhr){
                btn.prop('disabled', false);

                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(k, v){
                        toastr.error(v[0]);
                    });
                } else {
                    toastr.error('Something went wrong!');
                }
            }
        });
    });


    // =========================
    // EDIT USER (OPEN MODAL FIXED)
    // =========================
   $(document).on('click', '.editBtn', function(){

    let id = $(this).data('id');

    $.ajax({
        url: "/bd-admin/user/edit/" + id,
        type: "GET",
        success: function(user){

            let form = $('#addUserForm');

            // reset + set id
            form[0].reset();
            form.attr('data-id', id);

            form.find('input[name="name"]').val(user.name);
            form.find('input[name="username"]').val(user.username);
            form.find('input[name="email"]').val(user.email);
            form.find('input[name="password"]').val('');
            form.find('select[name="role"]').val(user.role);

            // ❌ REMOVE appendTo (এটাই bug)
            // $('#addUserModal').appendTo("body").modal('show');

            // ✔ SIMPLE AND SAFE WAY
            $('#addUserModal').modal('show');

            $('#addUserModal .modal-title').text('Edit User');
        },
        error: function(){
            toastr.error('User load failed');
        }
    });

});


    // =========================
    // RESET MODAL
    // =========================
    $('#addUserModal').on('hidden.bs.modal', function () {
        let form = $('#addUserForm');

        form[0].reset();
        form.removeAttr('data-id');

        $('#addUserModal .modal-title').text('Add New User');
    });


    // =========================
    // DELETE USER
    // =========================
    $(document).on('click', '.deleteBtn', function(){

        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {

            if(result.isConfirmed){
                $.ajax({
                    url: "/bd-admin/user/delete/" + id,
                    type: "POST",
                    data: {
                        _method: "DELETE",
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res){
                        if(res.success){
                            toastr.success(res.message);
                            $('button[data-id="'+id+'"]').closest('tr').remove();
                        } else {
                            toastr.error(res.message);
                        }
                    },
                    error: function(){
                        toastr.error('Delete failed!');
                    }
                });
            }

        });

    });

});
</script>

@endsection
