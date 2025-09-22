@include('layout.head')

@if(Auth::check() && !Auth::user()->is_validated)
    <style>
        .blur-content {
            filter: blur(6px) !important;
            pointer-events: none;
            user-select: none;
        }
        #accountNotValidatedModal {
            z-index: 1060;
        }
        .modal-backdrop-custom {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1050;
        }
    </style>
    <div class="modal-backdrop-custom"></div>
    <div class="modal fade show" id="accountNotValidatedModal" tabindex="-1" aria-labelledby="accountNotValidatedModalLabel" aria-modal="true" style="display: block;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="accountNotValidatedModalLabel">Account not validated</h5>
                </div>
                <div class="modal-body">
                    Your account has not yet been validated by an administrator. Please wait for validation before proceeding.
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var mainContent = document.querySelector('.main-blur-wrapper');
            if (mainContent) {
                mainContent.classList.add('blur-content');
            }
        });
    </script>
@endif

@if(Auth::check() && Auth::user()->is_blocked)
    <style>
        .blur-content {
            filter: blur(6px) !important;
            pointer-events: none;
            user-select: none;
        }
        #accountBlockedModal {
            z-index: 1060;
        }
        .modal-backdrop-custom {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1050;
        }
    </style>
    <div class="modal-backdrop-custom"></div>
    <div class="modal fade show" id="accountBlockedModal" tabindex="-1" aria-labelledby="accountBlockedModalLabel" aria-modal="true" style="display: block;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="accountBlockedModalLabel">Conta bloqueada</h5>
                </div>
                <div class="modal-body">
                    Sua conta foi <b>bloqueada</b> por um administrador. Entre em contato com o suporte para mais informações.
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var mainContent = document.querySelector('.main-blur-wrapper');
            if (mainContent) {
                mainContent.classList.add('blur-content');
            }
        });
    </script>
@endif

<div class="main-blur-wrapper">
@include('layout.header')
<div class="d-flex" style="min-height: calc(100vh - 10vh);">
    @include('layout.sidebar')
    <div class="flex-grow-1">
        <div class="content-scroll">
            @include('layout.content')
        </div>
    </div>
</div>
@include('layout.footer')
</div>
