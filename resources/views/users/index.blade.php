<x-layout>
  <x-slot name="title">
    <h5 class="card-title mb-0">Users</h5>

    <button type="button" id="btnAddUser" class="btn btn-sm btn-primary rounded">Add User</button>
  </x-slot>

  <table id="dataTable" class="display nowrap">
    <thead>
      <tr>
        <th>Photo</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Role</th>
        <th>Address</th>
        <th>&nbsp;</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>

  <!-- Modal Form -->
  <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="userForm" enctype="multipart/form-data">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="userModalLabel">Add User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="userId">
            <div class="mb-3">
              <label for="name" class="form-label">Name *</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
              <label for="role_id" class="form-label">Role *</label>
              <select class="form-control" id="role_id" name="role_id" required>
                <option value="">Select Role</option>
                @foreach ($roles as $role)
          <option value="{{ $role->id }}">{{ $role->name }}</option>
        @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email *</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
              <label for="phone" class="form-label">Phone *</label>
              <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="mb-3">
              <label for="address" class="form-label">Address *</label>
              <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
            </div>
            <div class="mb-3">
              <label for="photo" class="form-label">Photo</label>
              <img id="photo-preview" src="" alt="Preview" class="img-thumbnail mb-2 d-none" style="max-width: 150px;">
              <input type="file" class="form-control" id="photo" name="photo">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <!-- Confirm Delete Modal -->
  <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmDeleteLabel">Confirm Delete</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this user?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button id="btnConfirmDelete" class="btn btn-danger">Delete</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View User Detail Modal  -->
  <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="detailModalLabel">User Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Detail user akan diisi dengan AJAX -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>


  <x-slot name="scripts">
    <script>
      $(document).ready(function () {
        const table = $('#dataTable').DataTable({
          scrollX: true,
          ajax: {
            url: '/users/fetch', 
            type: 'GET'          
          },
          columns: [
            {
              data: 'photo_url', render: function (data) {
                return `<img src="${data ?? '/user.png'}" alt="Photo" width="50">`;
              }
            },
            { data: 'name' },
            { data: 'email' },
            { data: 'phone' },
            { data: 'role.name'},
            { data: 'address' },
            {
              data: null, render: function (data) {
                const activeButton = data.is_active
                  ? `<button class="btn btn-sm btn-danger btn-toggle-active" data-id="${data.id}">Deactivate</button>`
                  : `<button class="btn btn-sm btn-success btn-toggle-active" data-id="${data.id}">Activate</button>`;

                return `
                ${activeButton}
                <button class="btn btn-sm btn-info btn-view-detail" data-id="${data.id}">View</button>
                <button class="btn btn-sm btn-warning btn-edit" data-id="${data.id}">Edit</button>
                <button class="btn btn-sm btn-danger btn-delete" data-id="${data.id}">Delete</button>
            `;
              }
            }
          ]
        });

         // Open Modal for Add User
        $('#btnAddUser').click(function () {
          $('#userModalLabel').text('Add User');
          $('#userForm')[0].reset();
          $('#userId').val('');
          $('#photo-preview').addClass('d-none');
          $('#userModal').modal('show');
          // console.log($('#userId').val());
        });

        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

        // Submit Form for Add/Edit
        $('#userForm').submit(function (e) {
          e.preventDefault();

          const formData = new FormData(this);
          const url = $('#userId').val() ? `/users/${$('#userId').val()}` : '/users';
          const method = $('#userId').val() ? 'PUT' : 'POST'; 
          // console.log($('#userId').val()); return;

          formData.append('_method', method);
          formData.append('name', $('#name').val());
          formData.append('role_id', $('#role_id').val());
          formData.append('email', $('#email').val());
          formData.append('phone', $('#phone').val());
          formData.append('address', $('#address').val());
          
          $.ajax({
            url: url,
            method: 'POST',
            data: formData, 
            processData: false, 
            contentType: false, 
            success: function () {
              $('#userModal').modal('hide'); 
              table.ajax.reload(); 
              alert('User saved successfully.');
            },
            error: function (xhr) {
              const errors = xhr.responseJSON.errors;
              let errorMessages = '';
              for (const [field, messages] of Object.entries(errors)) {
                errorMessages += `${field}: ${messages.join(', ')}\n`;
              }
              alert('Error:\n' + errorMessages);
            }
          });
        });


        // Open Modal for Edit
        $('#dataTable').on('click', '.btn-edit', function () {
          const id = $(this).data('id');
          $.get(`/users/${id}`, function (user) {
            $('#userId').val(user.id);
            $('#name').val(user.name);
            $('#role_id').val(user.role_id);
            $('#email').val(user.email);
            $('#phone').val(user.phone);
            $('#address').val(user.address);

            // Photo preview
            if (user.photo_url) {
              $('#photo-preview').attr('src', user.photo_url).removeClass('d-none');
            } else {
              $('#photo-preview').addClass('d-none');
            }

            $('#userModalLabel').text('Edit User');
            $('#userModal').modal('show');
          });
        });

        // Open Modal for Delete
        $('#dataTable').on('click', '.btn-delete', function () {
          selectedUserId = $(this).data('id');
          $('#confirmDeleteModal').modal('show');
        });

        // Confirm Delete
        $('#btnConfirmDelete').click(function () {
          $.ajax({
            url: `/users/${selectedUserId}`,
            method: 'DELETE',
            success: function () {
              $('#confirmDeleteModal').modal('hide');
              table.ajax.reload();
              alert('User deleted successfully.');
            }
          });
        });

        // View User Details
        $('#dataTable').on('click', '.btn-view-detail', function () {
          const id = $(this).data('id');
          $.get(`/users/${id}`, function (user) {
            console.log(user)
            $('#detailModal .modal-body').html(`
              <p><strong>Name:</strong> ${user.name}</p>
              <p><strong>Email:</strong> ${user.email}</p>
              <p><strong>Phone:</strong> ${user.phone}</p>
              <p><strong>Role:</strong> ${user.role.name}</p>
              <p><strong>Address:</strong> ${user.address}</p>
              <p><strong>Status:</strong> ${user.is_active ? 'Active' : 'Inactive'}</p>
            `);

            $('#detailModal').modal('show');
          });
        });


        // Activate/Deactivate user
        $('#dataTable').on('click', '.btn-toggle-active', function () {
          const id = $(this).data('id');
          $.ajax({
            url: `/users/${id}/toggle-active`,
            method: 'POST', 
            data: {
              _method: 'PUT', 
              _token: $('meta[name="csrf-token"]').attr('content'), 
            },
            success: function (response) {
              table.ajax.reload();
              alert(response.message);
            },
            error: function () {
              alert('Failed to update user status.');
            }
          });
        });

        // Photo preview
        $('#photo').on('change', function () {
          const file = this.files[0];
          if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
              $('#photo-preview').attr('src', e.target.result).removeClass('d-none');
            };
            reader.readAsDataURL(file);
          } else {
            $('#photo-preview').addClass('d-none');
          }
        });
      });
    </script>
  </x-slot>
</x-layout>