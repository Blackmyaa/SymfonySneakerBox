/* public/js/dark-mode.js */
document.addEventListener("DOMContentLoaded", function() {
    const toggleSwitch = document.getElementById('dark-mode-toggle');
    const darkModeCss = document.getElementById('dark-mode-css');

    // Charger l'état initial du mode sombre
    if (localStorage.getItem('darkMode') === 'enabled') {
        darkModeCss.removeAttribute('disabled');
        toggleSwitch.checked = true;
    }

    // Ajouter un écouteur d'événement au switch
    toggleSwitch.addEventListener('change', function() {
        if (toggleSwitch.checked) {
            darkModeCss.removeAttribute('disabled');
            localStorage.setItem('darkMode', 'enabled');
        } else {
            darkModeCss.setAttribute('disabled', 'true');
            localStorage.setItem('darkMode', 'disabled');
        }
    });
});
