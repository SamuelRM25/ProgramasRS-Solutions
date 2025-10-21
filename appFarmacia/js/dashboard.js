document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle
    document.getElementById('sidebarCollapse').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });

    // Module loading
    document.querySelectorAll('[data-module]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const module = this.getAttribute('data-module');
            loadModule(module);
            
            // Add active class to current link
            document.querySelectorAll('[data-module]').forEach(el => {
                el.classList.remove('active');
            });
            this.classList.add('active');
        });
    });

    // Load empty dashboard
    document.getElementById('main-content').innerHTML = `
        <div class="container mt-4">
            <h2>Bienvenido al Sistema de Farmacia</h2>
            <p>Seleccione una opción del menú para comenzar.</p>
        </div>
    `;
});

function loadModule(module) {
    fetch(`modules/${module}.php`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('main-content').innerHTML = data;
            // Execute any scripts in the loaded content
            const scripts = document.getElementById('main-content').getElementsByTagName('script');
            Array.from(scripts).forEach(script => {
                eval(script.innerHTML);
            });
        })
        .catch(error => {
            console.error('Error loading module:', error);
        });
}