<style>
    .sidebar-nav {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .sidebar-nav .nav-link {
        display: block;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        color: #333;
        background: none;
        transition: background 0.2s, color 0.2s;
        font-weight: 500;
        text-decoration: none;
        font-size: 14px;
    }
    .sidebar-nav .nav-link:hover,
    .sidebar-nav .nav-link.dropdown-toggle:hover {
        background: #e9ecef;
        color: #007bff;
        font-weight: 500;
    }
    .sidebar-nav .nav-link.active {
        background: #e9ecef;
        color: #007bff;
        font-weight: 600;
    }
    .sidebar-nav .dropdown-menu {
        background-color: transparent;
        font-size: 12px;
        border: none;
        padding-left: 0;
    }
    .sidebar-nav .dropdown-item {
        border-radius: 8px;
        margin-left: 16px;
        margin-right: -24px;
        display: block;
        padding: 0.75rem 1rem;
        color: #333;
        background: none;
        transition: background 0.2s, color 0.2s;
        font-weight: 500;
        text-decoration: none;
        font-size: 14px;
        border: none;
    }
    .sidebar-nav .dropdown-item:hover {
        background: #e9ecef;
        color: #007bff;
    }
    .sidebar-user {
        padding: 1rem 0;
        border-top: 1px solid #eee;
        margin-top: 1rem;
        font-size: 14px;
    }
</style>
<aside class="d-flex flex-column flex-shrink-0 p-3 bg-light border-end" style="width: 225px; height: calc(100vh - 10vh);">
    <nav class="flex-grow-1">
        <ul class="sidebar-nav">
            <li class="nav-item"><a href="/" class="nav-link{{ request()->is('/') ? ' active' : '' }}">{{ __('sidebar.home') }}</a></li>
            <li><a href="/temperature" class="nav-link{{ request()->is('temperature') ? ' active' : '' }}">{{ __('sidebar.temperature') }}</a></li>
            <li><a href="/humity" class="nav-link{{ request()->is('humity') ? ' active' : '' }}">{{ __('sidebar.humidity') }}</a></li>
            <li><a href="/noise" class="nav-link{{ request()->is('noise') ? ' active' : '' }}">{{ __('sidebar.noise') }}</a></li>
            <li><a href="/relatorio" class="nav-link{{ request()->is('relatorio') ? ' active' : '' }}">{{ __('sidebar.report') }}</a></li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" aria-expanded="false">
                    {{ __('sidebar.administration') }}
                </a>
                <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                    <li><a class="dropdown-item" href="/admin/users">{{ __('sidebar.user_management') }}</a></li>
                    <li><a class="dropdown-item" href="/admin/reports">{{ __('sidebar.report_generation') }}</a></li>
                    <li><a class="dropdown-item" href="/admin/access">{{ __('sidebar.access_control') }}</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div class="sidebar-user d-flex align-items-center justify-content-between mb-2">
        <div class="d-flex align-items-center">
            <img src="https://placehold.co/32x32" alt="User" class="rounded-circle" width="32" height="32">
            <span class="ms-2">{{ __('sidebar.user_name') }}</span>
        </div>
        <a href="#">
            <i class="bi bi-gear text-dark"></i>
        </a>
    </div>
    <div class="sidebar-leave text-center">
        <a href="#" class="btn btn-danger text-light w-100">{{ __('sidebar.leave') }}</a>
    </div>
</aside>
<script>
    const dropdownToggle = document.getElementById('adminDropdown');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    const DROPDOWN_KEY = 'sidebarDropdownOpen';

    function restoreDropdownState() {
        const isOpen = localStorage.getItem(DROPDOWN_KEY) === 'true';
        if (isOpen) {
            dropdownMenu.classList.add('show');
            dropdownToggle.setAttribute('aria-expanded', 'true');
        } else {
            dropdownMenu.classList.remove('show');
            dropdownToggle.setAttribute('aria-expanded', 'false');
        }
    }

    dropdownToggle.addEventListener('click', function(e) {
        e.preventDefault();
        const isOpen = dropdownMenu.classList.toggle('show');
        dropdownToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        localStorage.setItem(DROPDOWN_KEY, isOpen);
    });

    restoreDropdownState();
</script>
