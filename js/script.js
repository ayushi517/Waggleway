document.addEventListener('DOMContentLoaded', function() {
    var menuBtn = document.getElementById('menu-btn');
    var dropdown = document.getElementById('accountDropdown');

    menuBtn.addEventListener('click', function() {
        dropdown.classList.toggle('show');
    });

    window.addEventListener('click', function(event) {
        if (!event.target.matches('#menu-btn')) {
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }
    });
});
