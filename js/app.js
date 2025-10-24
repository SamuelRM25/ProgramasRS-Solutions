document.addEventListener('DOMContentLoaded', () => {
    // --- CONFIGURACIÓN Y DATOS ---
    
    const USERS = {
        'Benjamín Ramírez (El Canche)': {
            password: '1107',
            progress: {
                course: {
                    module1: { completed: true, lessons: { lesson1: true, lesson2: true, lesson3: false } },
                    module2: { completed: false, lessons: { lesson1: false, lesson2: false, lesson3: false } },
                    module3: { completed: false, lessons: { lesson1: false, lesson2: false, lesson3: false } },
                    module4: { completed: false, lessons: { lesson1: false, lesson2: false, lesson3: false } },
                    module5: { completed: false, lessons: { lesson1: false, lesson2: false, lesson3: false } },
                }
            }
        },
        'Alcides Ramírez (El Gordo)': {
            password: '1210',
            progress: {
                course: {
                    module1: { completed: false, lessons: { lesson1: true, lesson2: false, lesson3: false } },
                    module2: { completed: false, lessons: { lesson1: false, lesson2: false, lesson3: false } },
                    module3: { completed: false, lessons: { lesson1: false, lesson2: false, lesson3: false } },
                    module4: { completed: false, lessons: { lesson1: false, lesson2: false, lesson3: false } },
                    module5: { completed: false, lessons: { lesson1: false, lesson2: false, lesson3: false } },
                }
            }
        }
    };

    const COURSE_DATA = {
        modules: [
            {
                id: 'module1',
                title: 'Módulo 1: Fundamentos del Piano',
                lessons: [
                    { id: 'lesson1', title: 'Lección 1: Conociendo tu instrumento', content: 'El piano es un instrumento de teclado... (Contenido detallado aquí).' },
                    { id: 'lesson2', title: 'Lección 2: Postura y posición de las manos', content: 'Una postura correcta es clave... (Contenido detallado aquí).' },
                    { id: 'lesson3', title: 'Lección 3: Los números de los dedos', content: 'Asignar un número a cada dedo... (Contenido detallado aquí).' }
                ]
            },
            {
                id: 'module2',
                title: 'Módulo 2: Tus Primeras Notas',
                lessons: [
                    { id: 'lesson1', title: 'Lección 1: Notas blancas: Do, Re, Mi...', content: 'Las siete notas naturales son... (Contenido detallado aquí).' },
                    { id: 'lesson2', title: 'Lección 2: Ubicando el Do central', content: 'El Do central es tu punto de referencia... (Contenido detallado aquí).' },
                    { id: 'lesson3', title: 'Lección 3: Introducción al ritmo', content: 'El ritmo es el pulso de la música... (Contenido detallado aquí).' }
                ]
            },
            // ... Añadir aquí los demás módulos (3, 4, 5) según la propuesta
        ]
    };
    
    // --- ESTADO GLOBAL ---
    let currentUser = null;
    let audioContext = null;
    let metronomeInterval = null;

    // --- REFERENCIAS AL DOM ---
    const views = document.querySelectorAll('.view');
    const navList = document.getElementById('nav-list');
    const loginView = document.getElementById('login-view');
    const dashboardView = document.getElementById('dashboard-view');
    const courseView = document.getElementById('course-view');
    const exercisesView = document.getElementById('exercises-view');
    const gamesView = document.getElementById('games-view');
    const toolsView = document.getElementById('tools-view');
    const passwordModal = document.getElementById('password-modal');
    const modalUsername = document.getElementById('modal-username');
    const passwordInput = document.getElementById('password-input');
    const loginSubmitBtn = document.getElementById('login-submit-btn');
    const loginError = document.getElementById('login-error');
    const virtualPiano = document.getElementById('virtual-piano');

    // --- FUNCIONES DE NAVEGACIÓN Y VISTAS ---
    function showView(viewId) {
        views.forEach(view => view.classList.remove('active'));
        const targetView = document.getElementById(`${viewId}-view`);
        if (targetView) {
            targetView.classList.add('active');
        }
        updateActiveNavLink(viewId);
    }

    function updateActiveNavLink(viewId) {
        const navLinks = navList.querySelectorAll('a');
        navLinks.forEach(link => {
            if (link.getAttribute('data-target') === viewId) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }

    function renderNav() {
        navList.innerHTML = '';
        if (currentUser) {
            const navItems = [
                { text: 'Dashboard', target: 'dashboard' },
                { text: 'Curso', target: 'course' },
                { text: 'Ejercicios', target: 'exercises' },
                { text: 'Juegos', target: 'games' },
                { text: 'Herramientas', target: 'tools' },
                { text: 'Cerrar Sesión', target: 'logout' }
            ];
            navItems.forEach(item => {
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.href = '#';
                a.textContent = item.text;
                a.setAttribute('data-target', item.target);
                a.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (item.target === 'logout') {
                        logout();
                    } else {
                        showView(item.target);
                    }
                });
                li.appendChild(a);
                navList.appendChild(li);
            });
        }
    }

    // --- FUNCIONES DE AUTENTICACIÓN ---
    function login(username, password) {
        const user = USERS[username];
        if (user && user.password === password) {
            currentUser = username;
            closeModal();
            renderNav();
            showView('dashboard');
            renderDashboard();
            return true;
        } else {
            loginError.textContent = 'Contraseña incorrecta. Inténtalo de nuevo.';
            return false;
        }
    }

    function logout() {
        currentUser = null;
        renderNav();
        showView('login');
        virtualPiano.classList.remove('visible');
    }

    // --- FUNCIONES DE RENDERIZADO DE CONTENIDO ---
    function renderDashboard() {
        document.getElementById('current-user-name').textContent = currentUser.split(' ')[0];
        const progress = calculateOverallProgress();
        document.getElementById('general-progress-bar').style.width = `${progress}%`;
        document.getElementById('progress-percentage').textContent = `${progress}% Completado`;
        // Lógica para próxima lección y última actividad...
    }

    function calculateOverallProgress() {
        const userProgress = USERS[currentUser].progress.course;
        let totalLessons = 0;
        let completedLessons = 0;
        for (const module in userProgress) {
            for (const lesson in userProgress[module].lessons) {
                totalLessons++;
                if (userProgress[module].lessons[lesson]) {
                    completedLessons++;
                }
            }
        }
        return totalLessons > 0 ? Math.round((completedLessons / totalLessons) * 100) : 0;
    }

    function renderCourse() {
        const container = document.getElementById('modules-container');
        container.innerHTML = '';
        const userProgress = USERS[currentUser].progress.course;

        COURSE_DATA.modules.forEach(module => {
            const moduleCard = document.createElement('div');
            moduleCard.className = 'module-card';
            
            const moduleHeader = document.createElement('div');
            moduleHeader.className = 'module-header';
            moduleHeader.innerHTML = `
                <h2>${module.title}</h2>
                <span class="status-icon">${userProgress[module.id].completed ? '✅' : '📚'}</span>
            `;
            
            const lessonsList = document.createElement('div');
            lessonsList.className = 'lessons-list';
            lessonsList.style.display = 'none'; // Oculto por defecto

            module.lessons.forEach(lesson => {
                const lessonItem = document.createElement('div');
                lessonItem.className = 'lesson-item';
                if (userProgress[module.id].lessons[lesson.id]) {
                    lessonItem.classList.add('completed');
                }
                lessonItem.innerHTML = `
                    <span class="lesson-title">${lesson.title}</span>
                    <span class="status-icon">${userProgress[module.id].lessons[lesson.id] ? '✅' : '⭕'}</span>
                `;
                lessonItem.addEventListener('click', () => showLessonContent(module.id, lesson.id, lesson.content));
                lessonsList.appendChild(lessonItem);
            });

            moduleHeader.addEventListener('click', () => {
                lessonsList.style.display = lessonsList.style.display === 'none' ? 'block' : 'none';
            });

            moduleCard.appendChild(moduleHeader);
            moduleCard.appendChild(lessonsList);
            container.appendChild(moduleCard);
        });
    }

    function showLessonContent(moduleId, lessonId, content) {
        alert(`Contenido de la Lección:\n\n${content}\n\n(En una versión más avanzada, esto abriría una nueva vista o modal con el contenido y el piano virtual).`);
        // Marcar lección como completada
        USERS[currentUser].progress.course[moduleId].lessons[lessonId] = true;
        renderCourse(); // Re-renderizar para actualizar el estado
        renderDashboard(); // Actualizar el dashboard
    }

    // --- LÓGICA DEL PIANO VIRTUAL ---
    function initPiano() {
        const pianoKeys = {
            'C': 261.63, 'C#': 277.18, 'D': 293.66, 'D#': 311.13,
            'E': 329.63, 'F': 349.23, 'F#': 369.99, 'G': 392.00,
            'G#': 415.30, 'A': 440.00, 'A#': 466.16, 'B': 493.88,
            'C2': 523.25
        };
        const whiteKeys = ['C', 'D', 'E', 'F', 'G', 'A', 'B', 'C2'];
        const blackKeys = ['C#', 'D#', null, 'F#', 'G#', 'A#'];
        const keyboardMap = { 'a': 'C', 'w': 'C#', 's': 'D', 'e': 'D#', 'd': 'E', 'f': 'F', 't': 'F#', 'g': 'G', 'y': 'G#', 'h': 'A', 'u': 'A#', 'j': 'B', 'k': 'C2'};

        const pianoContainer = document.createElement('div');
        pianoContainer.className = 'piano-keys';

        let blackKeyPosition = 0;
        whiteKeys.forEach((note, index) => {
            const key = document.createElement('div');
            key.className = 'piano-key piano-key--white';
            key.dataset.note = note;
            key.addEventListener('mousedown', () => playNote(note, pianoKeys[note]));
            pianoContainer.appendChild(key);

            if (blackKeys[index]) {
                const blackKey = document.createElement('div');
                blackKey.className = 'piano-key piano-key--black';
                blackKey.dataset.note = blackKeys[index];
                blackKey.style.left = `${(index * 50) + 35}px`; // Posicionamiento relativo
                blackKey.addEventListener('mousedown', () => playNote(blackKeys[index], pianoKeys[blackKeys[index]]));
                pianoContainer.appendChild(blackKey);
            }
        });
        virtualPiano.appendChild(pianoContainer);

        // Eventos de teclado
        window.addEventListener('keydown', (e) => {
            if (keyboardMap[e.key.toLowerCase()] && virtualPiano.classList.contains('visible')) {
                const note = keyboardMap[e.key.toLowerCase()];
                const keyElement = virtualPiano.querySelector(`[data-note="${note}"]`);
                if (keyElement && !e.repeat) {
                    keyElement.classList.add('active');
                    playNote(note, pianoKeys[note]);
                }
            }
        });
        window.addEventListener('keyup', (e) => {
            if (keyboardMap[e.key.toLowerCase()] && virtualPiano.classList.contains('visible')) {
                const note = keyboardMap[e.key.toLowerCase()];
                const keyElement = virtualPiano.querySelector(`[data-note="${note}"]`);
                if (keyElement) {
                    keyElement.classList.remove('active');
                }
            }
        });
    }

    function playNote(note, frequency) {
        if (!audioContext) {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
        }
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        oscillator.frequency.value = frequency;
        oscillator.type = 'sine';

        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);

        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.5);
    }

    // --- LÓGICA DE HERRAMIENTAS (METRÓNOMO) ---
    function initMetronome() {
        const bpmInput = document.getElementById('bpm-input');
        const toggleBtn = document.getElementById('metronome-toggle');
        const indicator = document.querySelector('.metronome-indicator');

        toggleBtn.addEventListener('click', () => {
            if (metronomeInterval) {
                clearInterval(metronomeInterval);
                metronomeInterval = null;
                toggleBtn.textContent = 'Iniciar';
                indicator.classList.remove('active');
            } else {
                const bpm = parseInt(bpmInput.value, 10);
                const interval = 60000 / bpm;
                metronomeInterval = setInterval(() => {
                    indicator.classList.add('active');
                    playClick();
                    setTimeout(() => indicator.classList.remove('active'), 100);
                }, interval);
                toggleBtn.textContent = 'Detener';
            }
        });
    }

    function playClick() {
        if (!audioContext) {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
        }
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 1000;
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.5, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.05);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.05);
    }

    // --- LÓGICA DE JUEGOS (Ejemplo: Piano Runner) ---
    function initPianoRunner() {
        const gameContent = document.getElementById('game-content');
        gameContent.innerHTML = `
            <h3>Piano Runner</h3>
            <p>¡Presiona las teclas correctas!</p>
            <div id="game-area" style="position:relative; height: 300px; background: #333; border-radius: 8px; overflow:hidden;">
                <!-- Las notas caerán aquí -->
            </div>
            <button id="start-runner" class="btn btn-primary">Iniciar Juego</button>
        `;
        // Lógica del juego aquí (más compleja, requiere un game loop)
        document.getElementById('start-runner').addEventListener('click', () => {
            alert('¡Juego iniciado! (La lógica completa del juego se implementaría aquí).');
            virtualPiano.classList.add('visible');
        });
    }
    
    // --- MANEJO DE EVENTOS ---
    function setupEventListeners() {
        // Login
        document.querySelectorAll('.user-card').forEach(card => {
            card.addEventListener('click', () => {
                const username = card.dataset.user;
                modalUsername.textContent = username;
                passwordModal.dataset.user = username;
                openModal();
            });
        });

        loginSubmitBtn.addEventListener('click', () => {
            const username = passwordModal.dataset.user;
            const password = passwordInput.value;
            login(username, password);
        });

        passwordInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                loginSubmitBtn.click();
            }
        });

        // Modal
        document.querySelector('.close-btn').addEventListener('click', closeModal);
        window.addEventListener('click', (e) => {
            if (e.target === passwordModal) {
                closeModal();
            }
        });

        // Navegación a vistas que necesitan renderizado
        navList.addEventListener('click', (e) => {
            if (e.target.tagName === 'A' && e.target.dataset.target) {
                const target = e.target.dataset.target;
                if (target === 'course') {
                    renderCourse();
                } else if (target === 'exercises' || target === 'games') {
                    virtualPiano.classList.add('visible');
                } else if (target !== 'tools') {
                     virtualPiano.classList.remove('visible');
                }
            }
        });

        // Inicialización de juegos y herramientas
        document.querySelector('[data-game="piano-runner"]').addEventListener('click', initPianoRunner);
        initMetronome();
    }

    // --- FUNCIONES DE MODAL ---
    function openModal() {
        passwordModal.style.display = 'block';
        passwordInput.value = '';
        loginError.textContent = '';
        passwordInput.focus();
    }

    function closeModal() {
        passwordModal.style.display = 'none';
    }

    // --- INICIALIZACIÓN DE LA APLICACIÓN ---
    function init() {
        renderNav();
        setupEventListeners();
        initPiano();
        showView('login');
    }

    init();
});