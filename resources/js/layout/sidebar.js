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

const userToggle = document.getElementById('sidebarUserToggle');
const userDropdown = document.getElementById('sidebarUserDropdown');
let userDropdownOpen = false;
if (userDropdown) {
    userDropdown.style.display = 'none';
    userDropdownOpen = false;
}
userToggle.addEventListener('click', function(e) {
    userDropdownOpen = !userDropdownOpen;
    userDropdown.style.display = userDropdownOpen ? 'block' : 'none';
});
document.addEventListener('click', function(e) {
    if (!userToggle.contains(e.target) && !userDropdown.contains(e.target)) {
        userDropdown.style.display = 'none';
        userDropdownOpen = false;
    }
});

const sidebar = document.getElementById('sidebar');
const hamburger = document.getElementById('sidebarHamburger');

function toggleSidebarMobile() {
    sidebar.classList.toggle('sidebar-open');
}

if (hamburger) {
    hamburger.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleSidebarMobile();
    });
    document.addEventListener('click', function(e) {
        if (window.innerWidth < 768 && sidebar.classList.contains('sidebar-open')) {
            if (!sidebar.contains(e.target) && e.target !== hamburger) {
                sidebar.classList.remove('sidebar-open');
            }
        }
    });
}
