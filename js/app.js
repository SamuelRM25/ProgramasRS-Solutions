document.addEventListener('DOMContentLoaded', () => {
    // --- CONFIGURACIÓN Y DATOS ---
    const USERS = {
        'Admin': { password: 'admin', role: 'admin' },
        'Benjamín Ramírez (El Canche)': {
            password: 'pianoCanche2024', role: 'student',
            progress: { course: {} }, stats: { lessonsCompleted: 0, gamesPlayed: 0 }
        },
        'Alcides Ramírez (El Gordo)': {
            password: 'pianoGordo2024', role: 'student',
            progress: { course: {} }, stats: { lessonsCompleted: 0, gamesPlayed: 0 }
        }
    };

    // Inicializar progreso para todos los módulos y lecciones
    const initializeProgress = () => {
        const moduleIds = ['module1', 'module2', 'module3', 'module4', 'module5', 'module6', 'module7', 'module8'];
        const lessonIds = ['lesson1', 'lesson2', 'lesson3'];
        moduleIds.forEach(mId => {
            const lessonData = { completed: false, lessons: {} };
            lessonIds.forEach(lId => lessonData.lessons[lId] = false);
            USERS['Benjamín Ramírez (El Canche)'].progress.course[mId] = { ...lessonData };
            USERS['Alcides Ramírez (El Gordo)'].progress.course[mId] = { ...lessonData };
        });
    };
    initializeProgress();

    const COURSE_DATA = {
        modules: [
            { id: 'module1', title: 'Módulo 1: Fundamentos del Piano', lessons: [{ id: 'lesson1', title: 'Lección 1: Conociendo tu instrumento', content: '<h2>El Teclado</h2><p>El piano está compuesto por teclas blancas y negras...</p>' }, { id: 'lesson2', title: 'Lección 2: Postura y manos', content: '<h2>Postura Correcta</h2><p>Siéntate en el borde del banco con la espalda recta...</p>' }, { id: 'lesson3', title: 'Lección 3: Numeración de dedos', content: '<h2>La Numeración</h2><p>Para facilitar la lectura de partituras, los dedos se numeran...</p>' }] },
            { id: 'module2', title: 'Módulo 2: Tus Primeras Notas', lessons: [{ id: 'lesson1', title: 'Lección 1: Notas blancas', content: '<h2>Do, Re, Mi, Fa, Sol, La, Si</h2><p>Estas son las siete notas principales...</p>' }, { id: 'lesson2', title: 'Lección 2: El Do central', content: '<h2>Tu Punto de Referencia</h2><p>El Do central es la tecla Do más cercana al centro del piano...</p>' }, { id: 'lesson3', title: 'Lección 3: Introducción al ritmo', content: '<h2>Figuras Musicales</h2><p>La música tiene un pulso. Las figuras musicales nos dicen cuánto tiempo dura cada nota...</p>' }] },
            { id: 'module3', title: 'Módulo 3: Acordes Básicos', lessons: [{ id: 'lesson1', title: 'Lección 1: ¿Qué es un acorde?', content: '<h2>Armonía</h2><p>Un acorde es un grupo de tres o más notas tocadas simultáneamente...</p>' }, { id: 'lesson2', title: 'Lección 2: Do Mayor (C)', content: '<h2>Tu Primer Acorde</h2><p>El acorde de Do Mayor se forma con las notas Do, Mi y Sol...</p>' }, { id: 'lesson3', title: 'Lección 3: Sol Mayor (G) y Fa Mayor (F)', content: '<h2>Más Acordes</h2><p>El acorde de Sol Mayor (G) está formado por Sol, Si y Re...</p>' }] },
            { id: 'module4', title: 'Módulo 4: Leyendo Música Fácil', lessons: [{ id: 'lesson1', title: 'Lección 1: El Pentagrama', content: '<h2>La Casa de las Notas</h2><p>El pentagrama son cinco líneas y cuatro espacios donde se escriben las notas...</p>' }, { id: 'lesson2', title: 'Lección 2: La Clave de Sol', content: '<h2>La Guía</h2><p>La clave de Sol se coloca al inicio del pentagrama y nos dice que la segunda línea es la nota Sol...</p>' }, { id: 'lesson3', title: 'Lección 3: Leyendo tu primera melodía', content: '<h2>Uniendo Puntos</h2><p>Ahora que conoces las notas en el pentagrama, intenta leer una melodía sencilla...</p>' }] },
            { id: 'module5', title: 'Módulo 5: Tu Primera Canción', lessons: [{ id: 'lesson1', title: 'Lección 1: Uniendo todo', content: '<h2>Síntesis</h2><p>Es hora de combinar todo lo que has aprendido: notas, ritmo y acordes...</p>' }, { id: 'lesson2', title: 'Lección 2: Brilla, Brilla, Estrellita', content: '<h2>Una Melodía Clásica</h2><p>Esta canción utiliza solo las primeras seis notas...</p>' }, { id: 'lesson3', title: 'Lección 3: Tocando con ambas manos', content: '<h2>El Siguiente Nivel</h2><p>Intenta tocar la melodía con la mano derecha mientras tocas los acordes de Do Mayor con la izquierda...</p>' }] },
            { id: 'module6', title: 'Módulo 6: Escalas y Arpegios', lessons: [{ id: 'lesson1', title: 'Lección 1: La Escala de Do Mayor', content: '<h2>La Escala Madre</h2><p>La escala de Do Mayor es Do-Re-Mi-Fa-Sol-La-Si-Do...</p>' }, { id: 'lesson2', title: 'Lección 2: Arpegios de Do Mayor', content: '<h2>Descomponiendo el Acorde</h2><p>Un arpegio es tocar las notas de un acorde una tras otra...</p>' }] },
            { id: 'module7', title: 'Módulo 7: Independencia de Manos', lessons: [{ id: 'lesson1', title: 'Lección 1: Patrones Sencillos', content: '<h2>Separando Cerebros</h2><p>Comienza con patrones muy simples...</p>' }, { id: 'lesson2', title: 'Lección 2: Bajo Ostinato', content: '<h2>Creando una Base</h2><p>Un "ostinato" es un patrón que se repite...</p>' }] },
            { id: 'module8', title: 'Módulo 8: Introducción a la Improvisación', lessons: [{ id: 'lesson1', title: 'Lección 1: La Escala de Blues', content: '<h2>Un Sonido Especial</h2><p>La escala de blues de Do es: Do-Mib-F-Solb-Sol-Bb...</p>' }, { id: 'lesson2', title: 'Lección 2: Improvisando sobre 12 compases', content: '<h2>La Estructura del Blues</h2><p>La progresión de 12 compases es la base de mucho blues, jazz y rock...</p>' }] },
        ]
    };
    const SONGBOOK_DATA = [
        { title: "Brilla, Brilla, Estrellita", chords: "C | G | Am | F\nC | G | C | C", lyrics: "Brilla, brilla, estrellita,\nqué pequeña eres.\nComo un diamante en el cielo,\nbrilla, brilla, estrellita.", tablature: "Derecha (Mano Derecha):\nC C G G A A G (2x)\nF F E E D D C (2x)\n\nDedos: 5 5 4 4 3 3 4 | 1 1 2 2 3 3 4\n\nIzquierda (Mano Izquierda):\nC - G - Am - F\n(Usa acordes básicos)" },
        { title: "Cumpleaños Feliz", chords: "C | C | G | C\nC | C | G2 | C\nF | C | G | C", lyrics: "Cumpleaños feliz,\nte deseamos a ti.\nCumpleaños feliz,\nte deseamos a ti.", tablature: "Derecha (Mano Derecha):\nC C D C F E (2x)\nC C C2 A F G F\n\nDedos: 1 1 2 1 4 3 | 1 1 5 3 4 3\n\nIzquierda (Mano Izquierda):\nC - G - C - G\nF - C - G - C" },
        { title: "Oh, Susana", chords: "C | F | C | G7\nC | F | C | G7 | C", lyrics: "Oh, Susana, no llores por mí,\nvengo de Luisiana con mi banjo en el frente.\nOh, Susana, no llores por mí,\nporque esta noche voy a cantar por ti.", tablature: "Derecha (Mano Derecha):\nE E F G G F E C D D E E C C\nE E F G G F E C D D E E C C\n\nDedos: 3 3 4 5 5 4 3 1 | 2 2 3 3 1 1\n\nIzquierda (Mano Izquierda):\nC - F - C - G7\n(Practica el cambio suave entre acordes)" }
    ];

    // --- ESTADO GLOBAL ---
    let currentUser = null;
    let audioContext = null;
    let metronomeInterval = null;
    let noteIdentificationExercise = null;
    let pianoRunnerGame = null;
    let adminClickCount = 0;
    let adminClickTimer = null;

    // --- REFERENCIAS AL DOM ---
    const views = document.querySelectorAll('.view');
    const navList = document.getElementById('nav-list');
    const menuToggle = document.getElementById('menu-toggle');
    const mainNav = document.getElementById('main-nav');
    const passwordModal = document.getElementById('password-modal');
    const modalUsername = document.getElementById('modal-username');
    const passwordInput = document.getElementById('password-input');
    const loginSubmitBtn = document.getElementById('login-submit-btn');
    const loginError = document.getElementById('login-error');
    const virtualPiano = document.getElementById('virtual-piano');
    const loadingSpinner = document.getElementById('loading-spinner');
    const adminSecretBtn = document.getElementById('admin-secret-btn');
    const completeLessonBtn = document.getElementById('complete-lesson-btn');
    const completionFeedback = document.getElementById('completion-feedback');

    // --- FUNCIONES DE NAVEGACIÓN Y VISTAS ---
    function showView(viewId, data = {}) {
        views.forEach(view => view.classList.remove('active'));
        const targetView = document.getElementById(`${viewId}-view`);
        if (targetView) {
            showLoading();
            setTimeout(() => {
                targetView.classList.add('active');
                hideLoading();
                if (viewId === 'lesson') renderLesson(data.moduleId, data.lessonId);
                if (viewId === 'songbook') renderSongbook();
            }, 300);
        }
        updateActiveNavLink(viewId);
        closeMobileMenu();
    }

    function updateActiveNavLink(viewId) {
        const navLinks = navList.querySelectorAll('a');
        navLinks.forEach(link => {
            if (link.getAttribute('data-target') === viewId) link.classList.add('active');
            else link.classList.remove('active');
        });
    }

    function renderNav() {
        navList.innerHTML = '';
        if (currentUser) {
            const navItems = USERS[currentUser].role === 'admin' 
                ? [{ text: 'Dashboard', target: 'dashboard' }, { text: 'Herramientas', target: 'tools' }, { text: 'Cerrar Sesión', target: 'logout' }]
                : [
                    { text: 'Dashboard', target: 'dashboard' }, { text: 'Curso', target: 'course' },
                    { text: 'Ejercicios', target: 'exercises' }, { text: 'Juegos', target: 'games' },
                    { text: 'Cancionero', target: 'songbook' }, { text: 'Herramientas', target: 'tools' },
                    { text: 'Cerrar Sesión', target: 'logout' }
                ];
            navItems.forEach(item => {
                const li = document.createElement('li'); const a = document.createElement('a');
                a.href = '#'; a.textContent = item.text; a.setAttribute('data-target', item.target);
                a.addEventListener('click', (e) => { e.preventDefault(); item.target === 'logout' ? logout() : showView(item.target); });
                li.appendChild(a); navList.appendChild(li);
            });
        }
    }
    
    function toggleMobileMenu() { mainNav.classList.toggle('open'); }
    function closeMobileMenu() { mainNav.classList.remove('open'); }

    // --- FUNCIONES DE AUTENTICACIÓN ---
    function login(username, password) {
        const user = USERS[username];
        if (user && user.password === password) {
            currentUser = username; closeModal(); renderNav();
            showView(USERS[currentUser].role === 'admin' ? 'dashboard' : 'dashboard');
            return true;
        } else { loginError.textContent = 'Contraseña incorrecta.'; return false; }
    }

    function logout() { currentUser = null; renderNav(); showView('login'); virtualPiano.classList.remove('visible'); if (noteIdentificationExercise) noteIdentificationExercise.stop(); if (pianoRunnerGame) pianoRunnerGame.stop(); }

    // --- FUNCIONES DE RENDERIZADO DE CONTENIDO ---
    function renderDashboard() {
        const dashboardContent = document.getElementById('dashboard-content');
        const dashboardTitle = document.getElementById('dashboard-title');
        if (USERS[currentUser].role === 'admin') {
            dashboardTitle.textContent = 'Panel de Administración';
            dashboardContent.innerHTML = '';
            const students = Object.keys(USERS).filter(u => USERS[u].role === 'student');
            students.forEach(student => {
                const progress = calculateOverallProgress(student);
                const card = document.createElement('div'); card.className = 'dashboard-card';
                card.innerHTML = `
                    <h3>${student.split(' ')[0]}</h3>
                    <div class="progress-bar-container"><div class="progress-bar" style="width: ${progress}%;"></div></div>
                    <p>${progress}% Completado</p>
                    <p>Lecciones: ${USERS[student].stats.lessonsCompleted} | Juegos: ${USERS[student].stats.gamesPlayed}</p>
                `;
                dashboardContent.appendChild(card);
            });
        } else {
            dashboardTitle.innerHTML = `Bienvenido de nuevo, <span id="current-user-name">${currentUser.split(' ')[0]}</span>`;
            const progress = calculateOverallProgress(currentUser);
            dashboardContent.innerHTML = `
                <div class="dashboard-card"><h3>Progreso General</h3><div class="progress-bar-container"><div class="progress-bar" style="width: ${progress}%;"></div></div><p>${progress}% Completado</p></div>
                <div class="dashboard-card"><h3>Próxima Lección</h3><p>${findNextLesson() || '¡Has completado todo el curso!'}</p></div>
                <div class="dashboard-card"><h3>Estadísticas</h3><p>Lecciones: <strong>${USERS[currentUser].stats.lessonsCompleted}</strong></p><p>Juegos: <strong>${USERS[currentUser].stats.gamesPlayed}</strong></p></div>
            `;
        }
    }

    function calculateOverallProgress(username) {
        const userProgress = USERS[username].progress.course;
        let total = 0, completed = 0;
        for (const m in userProgress) { for (const l in userProgress[m].lessons) { total++; if (userProgress[m].lessons[l]) completed++; } }
        return total > 0 ? Math.round((completed / total) * 100) : 0;
    }
    function findNextLesson() {
        for (const module of COURSE_DATA.modules) {
            if (!USERS[currentUser].progress.course[module.id].completed) {
                for (const lesson of module.lessons) {
                    if (!USERS[currentUser].progress.course[module.id].lessons[lesson.id]) {
                        return `Continúa con ${module.title.split(':')[1]} - ${lesson.title}`;
                    }
                }
            }
        }
        return null;
    }

    function renderCourse() {
        const container = document.getElementById('modules-container');
        container.innerHTML = '';
        const userProgress = USERS[currentUser].progress.course;
        COURSE_DATA.modules.forEach(module => {
            const moduleCard = document.createElement('div'); moduleCard.className = 'module-card';
            const moduleHeader = document.createElement('div'); moduleHeader.className = 'module-header';
            moduleHeader.innerHTML = `<h2>${module.title}</h2><span class="status-icon">${userProgress[module.id].completed ? '✅' : '📚'}</span>`;
            const lessonsList = document.createElement('div'); lessonsList.className = 'lessons-list';
            module.lessons.forEach(lesson => {
                const lessonItem = document.createElement('div'); lessonItem.className = 'lesson-item';
                if (userProgress[module.id].lessons[lesson.id]) lessonItem.classList.add('completed');
                lessonItem.innerHTML = `<span class="lesson-title">${lesson.title}</span><span class="status-icon">${userProgress[module.id].lessons[lesson.id] ? '✅' : '⭕'}</span>`;
                lessonItem.addEventListener('click', () => showView('lesson', { moduleId: module.id, lessonId: lesson.id }));
                lessonsList.appendChild(lessonItem);
            });
            moduleHeader.addEventListener('click', () => lessonsList.classList.toggle('open'));
            moduleCard.appendChild(moduleHeader); moduleCard.appendChild(lessonsList);
            container.appendChild(moduleCard);
        });
    }

    function renderLesson(moduleId, lessonId) {
        const module = COURSE_DATA.modules.find(m => m.id === moduleId);
        const lesson = module.lessons.find(l => l.id === lessonId);
        document.getElementById('lesson-title').textContent = lesson.title;
        document.getElementById('lesson-content').innerHTML = lesson.content;
        document.getElementById('back-to-course').onclick = () => showView('course');
        const isCompleted = USERS[currentUser].progress.course[moduleId].lessons[lessonId];
        completeLessonBtn.style.display = isCompleted ? 'none' : 'inline-block';
        completionFeedback.textContent = '';
        completeLessonBtn.onclick = () => {
            USERS[currentUser].progress.course[moduleId].lessons[lessonId] = true;
            USERS[currentUser].stats.lessonsCompleted++;
            checkModuleCompletion(moduleId);
            completeLessonBtn.style.display = 'none';
            completionFeedback.textContent = '¡Lección completada con éxito!';
        };
        virtualPiano.classList.add('visible');
    }
    function checkModuleCompletion(moduleId) {
        const module = COURSE_DATA.modules.find(m => m.id === moduleId);
        const allLessonsCompleted = module.lessons.every(lesson => USERS[currentUser].progress.course[moduleId].lessons[lesson.id]);
        if (allLessonsCompleted) USERS[currentUser].progress.course[moduleId].completed = true;
    }

    function renderSongbook() {
        const songCards = document.getElementById('song-cards');
        const songDetails = document.getElementById('song-details');
        songCards.innerHTML = '';
        SONGBOOK_DATA.forEach((song, index) => {
            const card = document.createElement('div'); card.className = 'song-card';
            if (index === 0) card.classList.add('active');
            card.textContent = song.title;
            card.addEventListener('click', () => {
                document.querySelectorAll('.song-card').forEach(c => c.classList.remove('active'));
                card.classList.add('active');
                songDetails.innerHTML = `
                    <h2>${song.title}</h2>
                    <div id="song-content">
                        <h3>Acorde</h3><p class="chords">${song.chords.replace(/\n/g, '<br>')}</p>
                        <h3>Letra</h3><p class="lyrics">${song.lyrics}</p>
                        <h3>Tablatura y Dedos</h3><pre class="tablature">${song.tablature}</pre>
                    </div>
                `;
            });
            songCards.appendChild(card);
        });
        if (songCards.firstChild) songCards.firstChild.click();
    }

    // --- LÓGICA DEL PIANO VIRTUAL ---
    function initPiano() {
        const pianoKeys = { 'C': 261.63, 'C#': 277.18, 'D': 293.66, 'D#': 311.13, 'E': 329.63, 'F': 349.23, 'F#': 369.99, 'G': 392.00, 'G#': 415.30, 'A': 440.00, 'A#': 466.16, 'B': 493.88, 'C2': 523.25 };
        const whiteKeys = ['C', 'D', 'E', 'F', 'G', 'A', 'B', 'C2'];
        const blackKeys = ['C#', 'D#', null, 'F#', 'G#', 'A#'];
        const keyboardMap = { 'a': 'C', 'w': 'C#', 's': 'D', 'e': 'D#', 'd': 'E', 'f': 'F', 't': 'F#', 'g': 'G', 'y': 'G#', 'h': 'A', 'u': 'A#', 'j': 'B', 'k': 'C2' };
        const pianoContainer = document.querySelector('.piano-keys');
        whiteKeys.forEach((note, index) => {
            const key = document.createElement('div'); key.className = 'piano-key piano-key--white'; key.dataset.note = note;
            key.addEventListener('mousedown', () => playNote(note, pianoKeys[note])); pianoContainer.appendChild(key);
            if (blackKeys[index]) {
                const blackKey = document.createElement('div'); blackKey.className = 'piano-key piano-key--black'; blackKey.dataset.note = blackKeys[index];
                const leftPosition = (index * 50) + 35; blackKey.style.left = `${leftPosition}px`;
                blackKey.addEventListener('mousedown', () => playNote(blackKeys[index], pianoKeys[blackKeys[index]])); pianoContainer.appendChild(blackKey);
            }
        });
        window.addEventListener('keydown', (e) => {
            if (keyboardMap[e.key.toLowerCase()] && virtualPiano.classList.contains('visible')) {
                const note = keyboardMap[e.key.toLowerCase()]; const keyElement = virtualPiano.querySelector(`[data-note="${note}"]`);
                if (keyElement && !e.repeat) { keyElement.classList.add('active'); playNote(note, pianoKeys[note]); highlightLabel(note); }
            }
        });
        window.addEventListener('keyup', (e) => {
            if (keyboardMap[e.key.toLowerCase()] && virtualPiano.classList.contains('visible')) {
                const note = keyboardMap[e.key.toLowerCase()]; const keyElement = virtualPiano.querySelector(`[data-note="${note}"]`);
                if (keyElement) { keyElement.classList.remove('active'); unhighlightLabel(note); }
            }
        });
    }
    function playNote(note, frequency) {
        if (!audioContext) audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator(); const gainNode = audioContext.createGain();
        oscillator.connect(gainNode); gainNode.connect(audioContext.destination);
        oscillator.frequency.value = frequency; oscillator.type = 'sine';
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
        oscillator.start(audioContext.currentTime); oscillator.stop(audioContext.currentTime + 0.5);
        if (noteIdentificationExercise) noteIdentificationExercise.checkAnswer(note);
        if (pianoRunnerGame) pianoRunnerGame.checkHit(note);
    }
    function highlightLabel(note) { const label = document.querySelector(`.piano-label[data-note="${note}"]`); if(label) label.style.color = 'var(--primary-color)'; }
    function unhighlightLabel(note) { const label = document.querySelector(`.piano-label[data-note="${note}"]`); if(label) label.style.color = 'var(--text-muted)'; }

    // --- LÓGICA DE HERRAMIENTAS (METRÓNOMO) ---
    function initMetronome() {
        const bpmInput = document.getElementById('bpm-input'); const toggleBtn = document.getElementById('metronome-toggle'); const indicator = document.querySelector('.metronome-indicator');
        toggleBtn.addEventListener('click', () => {
            if (metronomeInterval) { clearInterval(metronomeInterval); metronomeInterval = null; toggleBtn.textContent = 'Iniciar'; indicator.classList.remove('active'); }
            else { const bpm = parseInt(bpmInput.value, 10); const interval = 60000 / bpm; metronomeInterval = setInterval(() => { indicator.classList.add('active'); playClick(); setTimeout(() => indicator.classList.remove('active'), 100); }, interval); toggleBtn.textContent = 'Detener'; }
        });
    }
    function playClick() {
        if (!audioContext) audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator(); const gainNode = audioContext.createGain();
        oscillator.connect(gainNode); gainNode.connect(audioContext.destination);
        oscillator.frequency.value = 1000; oscillator.type = 'sine';
        gainNode.gain.setValueAtTime(0.5, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.05);
        oscillator.start(audioContext.currentTime); oscillator.stop(audioContext.currentTime + 0.05);
    }

    // --- EJERCICIO: IDENTIFICACIÓN DE NOTAS (Clave de Sol) ---
    class NoteIdentificationExercise {
        constructor() { this.canvas = document.getElementById('exercise-canvas'); this.ctx = this.canvas.getContext('2d'); this.feedback = document.getElementById('exercise-feedback'); this.notes = ['C', 'D', 'E', 'F', 'G', 'A', 'B']; this.currentNote = null; this.isRunning = false; }
        start() { this.isRunning = true; this.canvas.style.display = 'block'; this.feedback.innerHTML = ''; this.generateNewNote(); }
        stop() { this.isRunning = false; this.canvas.style.display = 'none'; this.feedback.innerHTML = ''; }
        generateNewNote() { if (!this.isRunning) return; this.currentNote = this.notes[Math.floor(Math.random() * this.notes.length)]; this.drawStaff(); this.drawNote(); }
        drawStaff() { const ctx = this.ctx; const w = this.canvas.width; const h = this.canvas.height; ctx.clearRect(0, 0, w, h); ctx.strokeStyle = '#f0f0f0'; ctx.lineWidth = 2; const lineSpacing = h / 5; for (let i = 1; i <= 5; i++) { ctx.beginPath(); ctx.moveTo(60, i * lineSpacing); ctx.lineTo(w - 20, i * lineSpacing); ctx.stroke(); } this.drawTrebleClef(); }
        drawTrebleClef() { const ctx = this.ctx; ctx.font = '80px serif'; ctx.fillStyle = '#f0f0f0'; ctx.fillText('𝄞', 10, this.canvas.height / 2 + 15); }
        drawNote() { const ctx = this.ctx; const notePositions = { 'E': 1, 'F': 2, 'G': 3, 'A': 4, 'B': 5, 'C': 6, 'D': 7 }; const lineSpacing = this.canvas.height / 5; const y = notePositions[this.currentNote] * (lineSpacing / 2); ctx.fillStyle = '#f4c430'; ctx.beginPath(); ctx.arc(this.canvas.width / 2, y, 10, 0, 2 * Math.PI); ctx.fill(); }
        checkAnswer(note) { if (!this.isRunning || !this.currentNote) return; if (note === this.currentNote) { this.feedback.innerHTML = `<p style="color: #4caf50;">¡Correcto! Era la nota ${this.currentNote}.</p>`; setTimeout(() => this.generateNewNote(), 1500); } else { this.feedback.innerHTML = `<p style="color: #e74c3c;">Incorrecto. Intenta de nuevo.</p>`; } }
    }

    // --- EJERCICIO AVANZADO: Clave de Sol y Fa ---
    class AdvancedClefExercise extends NoteIdentificationExercise {
        constructor() { super(); this.notes = { 'C2': 8, 'B': 7, 'A': 6, 'G': 5, 'F': 4, 'E': 3, 'D': 2, 'C': 1 }; this.noteNames = Object.keys(this.notes); }
        drawStaff() {
            const ctx = this.ctx; const w = this.canvas.width; const h = this.canvas.height; ctx.clearRect(0, 0, w, h); ctx.strokeStyle = '#f0f0f0'; ctx.lineWidth = 2;
            const staffHeight = h / 2;
            for (let i = 1; i <= 5; i++) { ctx.beginPath(); ctx.moveTo(60, staffHeight + i * (staffHeight/5)); ctx.lineTo(w - 20, staffHeight + i * (staffHeight/5)); ctx.stroke(); }
            this.drawBassClef(10, staffHeight + staffHeight/2);
            for (let i = 1; i <= 5; i++) { ctx.beginPath(); ctx.moveTo(60, i * (staffHeight/5)); ctx.lineTo(w - 20, i * (staffHeight/5)); ctx.stroke(); }
            this.drawTrebleClef();
        }
        drawBassClef(x, y) { const ctx = this.ctx; ctx.font = '60px serif'; ctx.fillStyle = '#f0f0f0'; ctx.fillText('𝄢', x, y + 20); }
        drawNote() {
            const ctx = this.ctx; const noteValue = this.notes[this.currentNote]; const staffHeight = this.canvas.height / 2; let y;
            if (noteValue > 4) { y = (noteValue - 4) * (staffHeight / 10); }
            else { y = staffHeight + (noteValue * (staffHeight / 10)); }
            ctx.fillStyle = '#f4c430'; ctx.beginPath(); ctx.arc(this.canvas.width / 2, y, 10, 0, 2 * Math.PI); ctx.fill();
        }
    }

    // --- JUEGO: PIANO RUNNER ---
    class PianoRunnerGame {
        constructor() {
            this.canvas = document.getElementById('game-canvas'); this.ctx = this.canvas.getContext('2d');
            this.scoreEl = document.getElementById('game-score'); this.livesEl = document.getElementById('game-lives');
            this.startBtn = document.getElementById('start-game-btn'); this.notes = ['C', 'D', 'E', 'F', 'G', 'A', 'B'];
            this.fallingNotes = []; this.score = 0; this.lives = 3; this.gameSpeed = 2; this.noteInterval = 1000; this.lastNoteTime = 0; this.isRunning = false; this.animationId = null; this.noteSpawnInterval = null;
        }
        start() {
            this.isRunning = true; this.score = 0; this.lives = 3; this.fallingNotes = []; this.gameSpeed = 2; this.updateUI();
            this.canvas.style.display = 'block'; this.startBtn.textContent = 'Detener Juego'; this.startBtn.onclick = () => this.stop();
            this.noteSpawnInterval = setInterval(() => this.spawnNote(), this.noteInterval); this.gameLoop();
        }
        stop() { this.isRunning = false; cancelAnimationFrame(this.animationId); clearInterval(this.noteSpawnInterval); this.canvas.style.display = 'none'; this.startBtn.textContent = 'Iniciar Juego'; this.startBtn.onclick = () => this.start(); USERS[currentUser].stats.gamesPlayed++; }
        spawnNote() { const note = this.notes[Math.floor(Math.random() * this.notes.length)]; const keyWidth = this.canvas.width / this.notes.length; const x = this.notes.indexOf(note) * keyWidth + keyWidth / 2; this.fallingNotes.push({ note, x, y: -20 }); }
        gameLoop() { if (!this.isRunning) return; this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height); this.ctx.strokeStyle = '#f4c430'; this.ctx.lineWidth = 3; this.ctx.beginPath(); this.ctx.moveTo(0, this.canvas.height - 50); this.ctx.lineTo(this.canvas.width, this.canvas.height - 50); this.ctx.stroke();
            this.fallingNotes = this.fallingNotes.filter(note => {
                note.y += this.gameSpeed; this.ctx.fillStyle = '#f0f0f0'; this.ctx.fillRect(note.x - 20, note.y - 10, 40, 20); this.ctx.fillStyle = '#1a1a1a'; this.ctx.font = '16px Roboto'; this.ctx.textAlign = 'center'; this.ctx.fillText(note.note, note.x, note.y + 5);
                if (note.y > this.canvas.height) { this.loseLife(); return false; } return true;
            });
            this.animationId = requestAnimationFrame(() => this.gameLoop());
        }
        checkHit(pressedNote) { if (!this.isRunning) return; const hitZoneTop = this.canvas.height - 70; const hitZoneBottom = this.canvas.height - 30;
            for (let i = this.fallingNotes.length - 1; i >= 0; i--) {
                const note = this.fallingNotes[i];
                if (note.note === pressedNote && note.y > hitZoneTop && note.y < hitZoneBottom) {
                    this.fallingNotes.splice(i, 1); this.score += 10; this.updateUI();
                    if (this.score % 50 === 0) this.gameSpeed += 0.5; break;
                }
            }
        }
        loseLife() { this.lives--; this.updateUI(); if (this.lives <= 0) this.gameOver(); }
        gameOver() { this.stop(); this.ctx.fillStyle = 'rgba(0,0,0,0.7)'; this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height); this.ctx.fillStyle = '#f4c430'; this.ctx.font = 'bold 48px Montserrat'; this.ctx.textAlign = 'center'; this.ctx.fillText('GAME OVER', this.canvas.width / 2, this.canvas.height / 2); this.ctx.font = '24px Roboto'; this.ctx.fillText(`Puntuación Final: ${this.score}`, this.canvas.width / 2, this.canvas.height / 2 + 50); }
        updateUI() { this.scoreEl.textContent = `Puntuación: ${this.score}`; this.livesEl.innerHTML = `Vidas: ${'❤️'.repeat(this.lives)}`; }
    }

    // --- MANEJO DE EVENTOS ---
    function setupEventListeners() {
        document.querySelectorAll('.user-card').forEach(card => {
            card.addEventListener('click', () => { const username = card.dataset.user; modalUsername.textContent = username; passwordModal.dataset.user = username; openModal(); });
        });
        loginSubmitBtn.addEventListener('click', () => { const username = passwordModal.dataset.user; const password = passwordInput.value; login(username, password); });
        passwordInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') loginSubmitBtn.click(); });
        document.querySelector('.close-btn').addEventListener('click', closeModal);
        window.addEventListener('click', (e) => { if (e.target === passwordModal) closeModal(); });
        adminSecretBtn.addEventListener('click', () => {
            adminClickCount++; clearTimeout(adminClickTimer);
            if (adminClickCount >= 5) { modalUsername.textContent = 'Admin'; passwordModal.dataset.user = 'Admin'; openModal(); adminClickCount = 0; }
            else { adminClickTimer = setTimeout(() => { adminClickCount = 0; }, 1000); }
        });
        menuToggle.addEventListener('click', toggleMobileMenu);
        navList.addEventListener('click', (e) => {
            if (e.target.tagName === 'A' && e.target.dataset.target) {
                const target = e.target.dataset.target;
                if (target === 'course') renderCourse();
                else if (target === 'exercises' || target === 'games') virtualPiano.classList.add('visible');
                else if (target !== 'tools') virtualPiano.classList.remove('visible');
            }
        });
        document.querySelector('[data-exercise="note-identification-treble"]').addEventListener('click', () => { if (!noteIdentificationExercise) noteIdentificationExercise = new NoteIdentificationExercise(); noteIdentificationExercise.start(); virtualPiano.classList.add('visible'); });
        document.querySelector('[data-exercise="note-identification-advanced"]').addEventListener('click', () => { if (!noteIdentificationExercise) noteIdentificationExercise = new AdvancedClefExercise(); noteIdentificationExercise.start(); virtualPiano.classList.add('visible'); });
        document.querySelector('[data-game="piano-runner"]').addEventListener('click', () => { if (!pianoRunnerGame) pianoRunnerGame = new PianoRunnerGame(); virtualPiano.classList.add('visible'); });
        initMetronome();
    }

    // --- FUNCIONES DE MODAL Y LOADING ---
    function openModal() { passwordModal.style.display = 'block'; passwordInput.value = ''; loginError.textContent = ''; passwordInput.focus(); }
    function closeModal() { passwordModal.style.display = 'none'; }
    function showLoading() { loadingSpinner.style.display = 'block'; }
    function hideLoading() { loadingSpinner.style.display = 'none'; }

    // --- INICIALIZACIÓN DE LA APLICACIÓN ---
    function init() { renderNav(); setupEventListeners(); initPiano(); showView('login'); }
    init();
});