<style>
    body {
        font-family: "Segoe UI", Arial, sans-serif;
        background: #f8f9fa;
        margin: 0;
        padding: 0;
    }
    body .container {
        height: 83%;
        width: 95%;
        max-width: none;
        margin: auto;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        padding: 32px;
    }
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        margin-top: 24px;
    }
    thead th {
        background: #f1f3f6;
        color: #333;
        font-weight: 600;
        padding: 14px 12px;
        border-bottom: 2px solid #e3e6ea;
        text-align: left;
    }
    th:last-child,
    td[data-label="Ações"] {
        width: 1%;
        white-space: nowrap;
        text-align: right;
    }
    tbody tr {
        transition: background 0.2s;
    }
    tbody tr:hover {
        background: #f6f8fa;
    }
    tbody td {
        padding: 12px 12px;
        border-bottom: 1px solid #f1f3f6;
        color: #444;
        font-size: 15px;
    }
    td[data-label="Ações"] {
        text-align: right;
        white-space: nowrap;
    }
    tbody tr:last-child td {
        border-bottom: none;
    }
    h2.title {
        font-size: 2.2rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 12px;
        letter-spacing: 0.5px;
        text-align: center;
        position: relative;
        display: inline-block;
    }
    h2.title::after {
        content: '';
        display: block;
        margin: 10px auto 0 auto;
        width: 60%;
        height: 4px;
        border-radius: 2px;
        background: linear-gradient(90deg, #4f8cff 0%, #38b2ac 100%);
        opacity: 0.18;
    }
    @media (max-width: 700px) {
        table, thead, tbody, th, td, tr {
            display: block;
        }
        thead {
            display: none;
        }
        tbody td {
            position: relative;
            padding-left: 50%;
            min-height: 40px;
        }
        tbody td:before {
            position: absolute;
            left: 16px;
            top: 12px;
            width: 45%;
            white-space: nowrap;
            font-weight: 600;
            color: #888;
            content: attr(data-label);
        }
    }

    .admin-row {
        background: #d6e7fa !important;
    }
    .verified-row {
        background: #d8ede0 !important;
    }
    .pending-row {
        background: #ffeaea !important;
    }
    .blocked-row {
        background: #fffad2 !important; /* amarelo bem suave */
        color: #7c5e00 !important;
    }
</style>

<body style="min-height: calc(100vh - 20vh);">
    <div class="container d-flex flex-column align-items-center mt-5">
        <h2 class="title">
            User Management
        </h2>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Ações</th>
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
                            <button type="submit" class="btn btn-secondary btn-sm me-1" title="@if(isset($user->is_admin) && $user->is_admin) Remover Administrador @else Tornar Administrador @endif" @if($user->id == $loggedId) disabled @endif>
                                👑
                            </button>
                        </form>
                        @if(isset($user->is_blocked) && $user->is_blocked)
                            <form action="{{ route('users.unblock', $user->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm me-1" title="Desbloquear Conta" @if($user->id == $loggedId) disabled @endif>🔓</button>
                            </form>
                        @else
                            <form action="{{ route('users.block', $user->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm me-1" title="Bloquear Conta" @if($user->id == $loggedId) disabled @endif>🔒</button>
                            </form>
                        @endif
                        <a href="#" class="btn btn-secondary btn-sm me-1 btn-edit-user-modal @if($user->id == $loggedId) disabled @endif" title="Editar"
                            data-user='{{ json_encode(["id"=>$user->id,"name"=>$user->name,"email"=>$user->email,"is_admin"=>$user->is_admin,"is_validated"=>$user->is_validated,"is_blocked"=>$user->is_blocked]) }}'
                            @if($user->id == $loggedId) tabindex="-1" aria-disabled="true" onclick="return false;" @endif>
                            ✏️
                        </a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-secondary btn-sm" title="Excluir" @if($user->id == $loggedId) disabled @endif>🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal de Edição de Usuário -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form id="editUserForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="modal-header">
              <h5 class="modal-title" id="editUserModalLabel">Editar Usuário</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="user_id" id="editUserId">
              <div class="mb-3">
                <label for="editUserName" class="form-label">Nome</label>
                <input type="text" class="form-control" id="editUserName" name="name" required>
              </div>
              <div class="mb-3">
                <label for="editUserEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="editUserEmail" name="email" required>
              </div>
              <div class="mb-3">
                <label for="editUserPassword" class="form-label">Nova Senha</label>
                <input type="password" class="form-control" id="editUserPassword" name="password" autocomplete="new-password">
              </div>
              <div class="mb-3">
                <label for="editUserPasswordConfirmation" class="form-label">Confirmar Nova Senha</label>
                <input type="password" class="form-control" id="editUserPasswordConfirmation" name="password_confirmation" autocomplete="new-password">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script>
    // Função para abrir a modal e preencher os dados do usuário
    function openEditUserModal(user) {
        document.getElementById('editUserId').value = user.id;
        document.getElementById('editUserName').value = user.name;
        document.getElementById('editUserEmail').value = user.email;
        document.getElementById('editUserPassword').value = '';
        document.getElementById('editUserPasswordConfirmation').value = '';
        document.getElementById('editUserForm').action = '/admin/users/' + user.id;
        var modal = new bootstrap.Modal(document.getElementById('editUserModal'));
        modal.show();
    }

    // Adiciona evento aos botões de editar
    window.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-edit-user-modal').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const user = JSON.parse(this.getAttribute('data-user'));
                openEditUserModal(user);
            });
        });
    });

    document.getElementById('editUserForm').onsubmit = function(e) {
        this.action = '/admin/users/' + document.getElementById('editUserId').value;
    };
    </script>
</body>
