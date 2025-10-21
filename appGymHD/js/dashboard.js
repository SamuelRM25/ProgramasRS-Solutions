document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', async function(e) {
        e.preventDefault();
        const module = this.getAttribute('href');
        
        // Update active state
        document.querySelectorAll('.nav-links li').forEach(li => li.classList.remove('active'));
        this.parentElement.classList.add('active');
        
        try {
            const response = await fetch(`views/${module}.php`);
            const content = await response.text();
            document.getElementById('main-content').innerHTML = content;

            // Fix CSS path
            const cssLink = document.createElement('link');
            cssLink.rel = 'stylesheet';
            cssLink.href = `/appGym/css/modules/${module}.css`;
            
            // Remove any previously loaded module CSS
            document.querySelectorAll('link[href*="/appGym/css/modules/"]').forEach(oldLink => oldLink.remove());
            document.head.appendChild(cssLink);
        } catch (error) {
            console.error('Error loading module:', error);
        }
    });
});

// Toggle sidebar
document.getElementById('sidebar-toggle').addEventListener('click', () => {
    document.querySelector('.sidebar').classList.toggle('collapsed');
    document.querySelector('.main-content').classList.toggle('expanded');
});

// Load default module on page load
document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.nav-links a').click();
});