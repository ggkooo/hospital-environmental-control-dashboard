<body class="users-page" style="min-height: calc(100vh - 20vh);">
    <link rel="stylesheet" href="{{ mix('css/pages/admin/users.css') }}">
    <div class="container d-flex flex-column align-items-center mt-5">
        <h2 class="title">
            {{ __('user-management.title') }}
        </h2>
        <table>
            <thead>
            <tr>
                <th>{{ __('user-management.id') }}</th>
                <th>{{ __('user-management.name') }}</th>
                <th>{{ __('user-management.email') }}</th>
                <th>{{ __('user-management.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @php $loggedId = auth()->id(); @endphp
            @foreach($users as $user)
                <tr class="
                    @if(isset($user->is_blocked) && $user->is_blocked)
                        blocked-row
                    @elseif(isset($user->is_admin) && $user->is_admin)
                        admin-row
                    @elseif(isset($user->is_validated) && $user->is_validated)
                        verified-row
                    @else
                        pending-row
                    @endif
                ">
                    <td data-label="ID">{{ $user->id }}</td>
                    <td data-label="Name">{{ $user->name }}</td>
                    <td data-label="Email">{{ $user->email }}</td>
                    <td data-label="Ações">
                        <form action="{{ route('users.verify', $user->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm me-1" title="Verificar" @if(isset($user->is_validated) && (int)$user->is_validated === 1 || $user->id == $loggedId) disabled @endif>✔️</button>
                        </form>
                        <form action="{{ route('users.toggleAdmin', $user->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm me-1" title="@if(isset($user->is_admin) && $user->is_admin) {{ __('user-management.remove_admin') }} @else {{ __('user-management.add_admin') }} @endif" @if($user->id == $loggedId) disabled @endif>
                                👑
                            </button>
                        </form>
                        @if(isset($user->is_blocked) && $user->is_blocked)
                            <form action="{{ route('users.unblock', $user->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm me-1" title="{{ __('user-management.unblock') }}" @if($user->id == $loggedId) disabled @endif>🔓</button>
                            </form>
                        @else
                            <form action="{{ route('users.block', $user->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm me-1" title="{{ __('user-management.block') }}" @if($user->id == $loggedId) disabled @endif>🔒</button>
                            </form>
                        @endif
                        <a href="#" class="btn btn-secondary btn-sm me-1 btn-edit-user-modal @if($user->id == $loggedId) disabled @endif" title="{{ __('user-management.edit') }}"
                            data-user='{{ json_encode(["id"=>$user->id,"name"=>$user->name,"email"=>$user->email,"is_admin"=>$user->is_admin,"is_validated"=>$user->is_validated,"is_blocked"=>$user->is_blocked]) }}'
                            @if($user->id == $loggedId) tabindex="-1" aria-disabled="true" onclick="return false;" @endif>
                            ✏️
                        </a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline-block;" class="form-delete-user">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-secondary btn-sm btn-delete-user" title="{{ __('user-management.delete') }}" @if($user->id == $loggedId) disabled @endif>🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form id="editUserForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="modal-header">
              <h5 class="modal-title" id="editUserModalLabel">{{ __('user-management.edit_user_modal') }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('user-management.close') }}"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="user_id" id="editUserId">
              <div class="mb-3">
                <label for="editUserName" class="form-label">{{ __('user-management.user_name_modal') }}</label>
                <input type="text" class="form-control" id="editUserName" name="name" required>
              </div>
              <div class="mb-3">
                <label for="editUserEmail" class="form-label">{{ __('user-management.user_email_modal') }}</label>
                <input type="email" class="form-control" id="editUserEmail" name="email" required>
              </div>
              <div class="mb-3">
                <label for="editUserPassword" class="form-label">{{ __('user-management.user_new_password_modal') }}</label>
                <input type="password" class="form-control" id="editUserPassword" name="password" autocomplete="new-password">
              </div>
              <div class="mb-3">
                <label for="editUserPasswordConfirmation" class="form-label">{{ __('user-management.user_confirm_new_password_modal') }}</label>
                <input type="password" class="form-control" id="editUserPasswordConfirmation" name="password_confirmation" autocomplete="new-password">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('user-management.cancel_modal') }}</button>
              <button type="submit" class="btn btn-primary">{{ __('user-management.save_modal') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmDeleteModalLabel">{{ __('user-management.delete_modal') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('user-management.close') }}"></button>
          </div>
          <div class="modal-body">
              {{ __('user-management.delete_modal_contet') }}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('user-management.cancel') }}</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">{{ __('user-management.delete') }}</button>
          </div>
        </div>
      </div>
    </div>

    <script src="{{ mix('js/pages/admin/users.js') }}"></script>
</body>
