document.addEventListener('DOMContentLoaded', () => {
    // --- CONFIGURACIÓN Y DATOS ---
    
    const USERS = {
        'Benjamín Ramírez (El Canche)': {
            password: 'pianoCanche2024',
            progress: {
                course: {
                    module1: { completed: false, lessons: { lesson1: false, lesson2: false, lesson3: false } },
                    module2: { completed: false, lessons: { lesson1: false, lesson2: false, lesson3: false } },
                    module3: { completed: false, lessons: { lesson1: false, lesson2: false, lesson3: false } },
                    module4: { completed: false, lessons: { lesson1: false, lesson2: false, lesson3: false } },
                    module5: { completed: false, lessons: { lesson1: false, lesson2: false, lesson3: false } },
                    module6: { completed: false, lessons: { lesson1: false, lesson2: false } },
                    module7: { completed: false, lessons: { lesson1: false, lesson2: false } },
                    module8: { completed: false, lessons: { lesson1: false, lesson2: false } },
                }
            },
            stats: { lessonsCompleted: 0, gamesPlayed: 0 }
        },
        'Alcides Ramírez (El Gordo)': {
            password: 'pianoGordo2024',
            progress: {
                course: {
                    module1: { completed: false, lessons: { lesson1: false, lesson2: false, lesson3: false } },
                    module2: { completed: false, lessons: { lesson1: false, lesson2: false, lesson3: false } },
                    module3: { completed: false, lessons: { lesson1: false, lesson2: false, lesson3: false } },
                    module4: { completed: false, lessons: { lesson1: false, lesson2: false, lesson3: false } },
                    module5: { completed: false, lessons: { lesson1: false, lesson2: false, lesson3: false } },
                    module6: { completed: false, lessons: { lesson1: false, lesson2: false } },
                    module7: { completed: false, lessons: { lesson1: false, lesson2: false } },
                    module8: { completed: false, lessons: { lesson1: false, lesson2: false } },
                }
            },
            stats: { lessonsCompleted: 0, gamesPlayed: 0 }
        },
        'Admin': {
            password: 'admin',
            isAdmin: true,
            progress: {
                course: {
                    module1: { completed: true, lessons: { lesson1: true, lesson2: true, lesson3: true } },
                    module2: { completed: true, lessons: { lesson1: true, lesson2: true, lesson3: true } },
                    module3: { completed: true, lessons: { lesson1: true, lesson2: true, lesson3: true } },
                    module4: { completed: true, lessons: { lesson1: true, lesson2: true, lesson3: true } },
                    module5: { completed: true, lessons: { lesson1: true, lesson2: true, lesson3: true } },
                    module6: { completed: true, lessons: { lesson1: true, lesson2: true } },
                    module7: { completed: true, lessons: { lesson1: true, lesson2: true } },
                    module8: { completed: true, lessons: { lesson1: true, lesson2: true } },
                }
            },
            stats: { lessonsCompleted: 22, gamesPlayed: 10 }
        }
    };

    const COURSE_DATA = {
        modules: [
            {
                id: 'module1', title: 'Módulo 1: Fundamentos del Piano',
                lessons: [
                    { id: 'lesson1', title: 'Lección 1: Conociendo tu instrumento', content: '<h2>El Teclado</h2><p>El piano está compuesto por teclas blancas y negras. Las teclas blancas producen las notas naturales (Do, Re, Mi, Fa, Sol, La, Si) y las negras producen los sostenidos/bemoles.</p><img src="https://via.placeholder.com/600x200/333/fff?text=Pentagrama+de+Ejemplo" alt="Teclado de piano">' },
                    { id: 'lesson2', title: 'Lección 2: Postura y manos', content: '<h2>Postura Correcta</h2><p>Siéntate en el borde del banco con la espalda recta. Tus brazos deben formar un ángulo de 90 grados con el teclado. Mantén las muñecas relajadas y curvadas.</p>' },
                    { id: 'lesson3', title: 'Lección 3: Numeración de dedos', content: '<h2>La Numeración</h2><p>Para facilitar la lectura de partituras, los dedos se numeran: Pulgar=1, Índice=2, Corazón=3, Anular=4, Meñique=5. Esto se aplica tanto a la mano derecha como a la izquierda.</p>' }
                ]
            },
            {
                id: 'module2', title: 'Módulo 2: Tus Primeras Notas',
                lessons: [
                    { id: 'lesson1', title: 'Lección 1: Notas blancas', content: '<h2>Do, Re, Mi, Fa, Sol, La, Si</h2><p>Estas son las siete notas principales. Las puedes encontrar fácilmente en las teclas blancas. Los grupos de dos teclas negras te ayudan a encontrar el Do y el Re. Los grupos de tres teclas negras te ayudan a encontrar el Fa, Sol y La.</p>' },
                    { id: 'lesson2', title: 'Lección 2: El Do central', content: '<h2>Tu Punto de Referencia</h2><p>El Do central es la tecla Do más cercana al centro del piano. Es tu punto de anclaje para leer partituras y encontrar otras notas.</p>' },
                    { id: 'lesson3', title: 'Lección 3: Introducción al ritmo', content: '<h2>Figuras Musicales</h2><p>La música tiene un pulso. Las figuras musicales nos dicen cuánto tiempo dura cada nota. Las más comunes son: <strong>Redonda</strong> (4 pulsos), <strong>Blanca</strong> (2 pulsos), <strong>Negra</strong> (1 pulso).</p>' }
                ]
            },
            {
                id: 'module3', title: 'Módulo 3: Acordes Básicos',
                lessons: [
                    { id: 'lesson1', title: 'Lección 1: ¿Qué es un acorde?', content: '<h2>Armonía</h2><p>Un acorde es un grupo de tres o más notas tocadas simultáneamente. El acorde más básico es el "tríada", formado por la tónica, la tercera y la quinta de una escala.</p>' },
                    { id: 'lesson2', title: 'Lección 2: Do Mayor (C)', content: '<h2>Tu Primer Acorde</h2><p>El acorde de Do Mayor se forma con las notas Do, Mi y Sol. En la mano derecha, puedes tocarlo con los dedos 1, 3 y 5 (Do-Mi-Sol).</p>' },
                    { id: 'lesson3', title: 'Lección 3: Sol Mayor (G) y Fa Mayor (F)', content: '<h2>Más Acordes</h2><p>El acorde de Sol Mayor (G) está formado por Sol, Si y Re. El acorde de Fa Mayor (F) está formado por Fa, La y Do. Practica cambiar entre estos tres acordes.</p>' }
                ]
            },
            {
                id: 'module4', title: 'Módulo 4: Leyendo Música Fácil',
                lessons: [
                    { id: 'lesson1', title: 'Lección 1: El Pentagrama', content: '<h2>La Casa de las Notas</h2><p>El pentagrama son cinco líneas y cuatro espacios donde se escriben las notas. Cada línea y espacio corresponde a una nota musical.</p>' },
                    { id: 'lesson2', title: 'Lección 2: La Clave de Sol', content: '<h2>La Guía</h2><p>La clave de Sol se coloca al inicio del pentagrama y nos dice que la segunda línea es la nota Sol. A partir de ahí, podemos deducir el resto de las notas.</p>' },
                    { id: 'lesson3', title: 'Lección 3: Leyendo tu primera melodía', content: '<h2>Uniendo Puntos</h2><p>Ahora que conoces las notas en el pentagrama, intenta leer una melodía sencilla. Identifica cada nota y tócala en el piano.</p>' }
                ]
            },
            {
                id: 'module5', title: 'Módulo 5: Tu Primera Canción',
                lessons: [
                    { id: 'lesson1', title: 'Lección 1: Uniendo todo', content: '<h2>Síntesis</h2><p>Es hora de combinar todo lo que has aprendido: notas, ritmo y acordes. La práctica de una canción es la mejor manera de solidificar tus conocimientos.</p>' },
                    { id: 'lesson2', title: 'Lección 2: Brilla, Brilla, Estrellita', content: '<h2>Una Melodía Clásica</h2><p>Esta canción utiliza solo las primeras seis notas (Do, Re, Mi, Fa, Sol, La). Es perfecta para principiantes. La partitura es simple y te ayudará a practicar la lectura.</p>' },
                    { id: 'lesson3', title: 'Lección 3: Tocando con ambas manos', content: '<h2>El Siguiente Nivel</h2><p>Intenta tocar la melodía con la mano derecha mientras tocas los acordes de Do Mayor con la mano izquierda. Al principio será lento, pero con la práctica, ganarás coordinación.</p>' }
                ]
            },
            {
                id: 'module6', title: 'Módulo 6: Escalas y Arpegios',
                lessons: [
                    { id: 'lesson1', title: 'Lección 1: La Escala de Do Mayor', content: '<h2>La Escala Madre</h2><p>La escala de Do Mayor es Do-Re-Mi-Fa-Sol-La-Si-Do. Es la base de la música tonal y no tiene sostenidos ni bemoles. Practícala lentamente con ambas manos.</p>' },
                    { id: 'lesson2', title: 'Lección 2: Arpegios de Do Mayor', content: '<h2>Descomponiendo el Acorde</h2><p>Un arpegio es tocar las notas de un acorde una tras otra, en lugar de simultáneamente. El arpegio de Do Mayor es Do-Mi-Sol-Do. Practica arpegios para mejorar la agilidad de tus dedos.</p>' }
                ]
            },
            {
                id: 'module7', title: 'Módulo 7: Independencia de Manos',
                lessons: [
                    { id: 'lesson1', title: 'Lección 1: Patrones Sencillos', content: '<h2>Separando Cerebros</h2><p>Comienza con patrones muy simples. Por ejemplo, toca un Do constante cada cuatro tiempos con la mano izquierda, mientras intentas tocar una melodía simple con la derecha.</p>' },
                    { id: 'lesson2', title: 'Lección 2: Bajo Ostinato', content: '<h2>Creando una Base</h2><p>Un "ostinato" es un patrón que se repite. Toca un patrón de bajo de Do-Sol-Do-Sol (1-5-1-5) con la izquierda, mientras improvisas o tocas una melodía con la derecha.</p>' }
                ]
            },
            {
                id: 'module8', title: 'Módulo 8: Introducción a la Improvisación',
                lessons: [
                    { id: 'lesson1', title: 'Lección 1: La Escala de Blues', content: '<h2>Un Sonido Especial</h2><p>La escala de blues de Do es: Do-Mib-F-Solb-Sol-Bb. Estas notas (llamadas "blue notes") le dan un sonido melancólico y característico. ¡Experimenta con ellas!</p>' },
                    { id: 'lesson2', title: 'Lección 2: Improvisando sobre 12 compases', content: '<h2>La Estructura del Blues</h2><p>La progresión de 12 compases es la base de mucho blues, jazz y rock. Una progresión común en Do es: (C | C | C | C | F | F | C | C | G | F | C | G). Toca acordes con la izquierda y usa la escala de blues con la derecha para crear tu propia melodía.</p>' }
                ]
            },
        ]
    };
    
    // --- ESTADO GLOBAL ---
    let currentUser = null;
    let audioContext = null;
    let metronomeInterval = null;
    let noteIdentificationExercise = null;
    let pianoRunnerGame = null;

    // --- REFERENCIAS AL DOM ---
    const views = document.querySelectorAll('.view');
    const navList = document.getElementById('nav-list');
    const menuToggle = document.getElementById('menu-toggle');
    const mainNav = document.getElementById('main-nav');
    const loginView = document.getElementById('login-view');
    const dashboardView = document.getElementById('dashboard-view');
    const courseView = document.getElementById('course-view');
    const lessonView = document.getElementById('lesson-view');
    const exercisesView = document.getElementById('exercises-view');
    const gamesView = document.getElementById('games-view');
    const toolsView = document.getElementById('tools-view');
    const passwordModal = document.getElementById('password-modal');
    const modalUsername = document.getElementById('modal-username');
    const passwordInput = document.getElementById('password-input');
    const loginSubmitBtn = document.getElementById('login-submit-btn');
    const loginError = document.getElementById('login-error');
    const virtualPiano = document.getElementById('virtual-piano');
    const loadingSpinner = document.getElementById('loading-spinner');

    // --- CANCIONERO ---
    const SONGBOOK = {
        songs: [
            {
                id: 'song1',
                title: 'Brilla, Brilla, Estrellita',
                difficulty: 'Principiante',
                notation: `
                    <div class="song-notation">
                        <h3>Partitura</h3>
                        <img src="assets/images/twinkle_star_sheet.png" alt="Partitura de Brilla, Brilla, Estrellita" class="sheet-music">
                        <h3>Tablatura</h3>
                        <div class="tablature">
                            <p>Do Do Sol Sol La La Sol</p>
                            <p>Fa Fa Mi Mi Re Re Do</p>
                            <p>Sol Sol Fa Fa Mi Mi Re</p>
                            <p>Sol Sol Fa Fa Mi Mi Re</p>
                            <p>Do Do Sol Sol La La Sol</p>
                            <p>Fa Fa Mi Mi Re Re Do</p>
                        </div>
                        <div class="chord-chart">
                            <h4>Acordes:</h4>
                            <p>Do Mayor (C): Do-Mi-Sol</p>
                            <p>Fa Mayor (F): Fa-La-Do</p>
                            <p>Sol Mayor (G): Sol-Si-Re</p>
                        </div>
                    </div>
                `
            },
            {
                id: 'song2',
                title: 'Cumpleaños Feliz',
                difficulty: 'Principiante',
                notation: `
                    <div class="song-notation">
                        <h3>Partitura</h3>
                        <img src="assets/images/happy_birthday_sheet.png" alt="Partitura de Cumpleaños Feliz" class="sheet-music">
                        <h3>Tablatura</h3>
                        <div class="tablature">
                            <p>Do Do Re Do Fa Mi</p>
                            <p>Do Do Re Do Sol Fa</p>
                            <p>Do Do Do' La Fa Mi Re</p>
                            <p>La# La# La Fa Sol Fa</p>
                        </div>
                        <div class="chord-chart">
                            <h4>Acordes:</h4>
                            <p>Do Mayor (C): Do-Mi-Sol</p>
                            <p>Fa Mayor (F): Fa-La-Do</p>
                            <p>Sol Mayor (G): Sol-Si-Re</p>
                        </div>
                    </div>
                `
            },
            {
                id: 'song3',
                title: 'Oh, Susana',
                difficulty: 'Intermedio',
                notation: `
                    <div class="song-notation">
                        <h3>Partitura</h3>
                        <img src="assets/images/oh_susana_sheet.png" alt="Partitura de Oh, Susana" class="sheet-music">
                        <h3>Tablatura</h3>
                        <div class="tablature">
                            <p>Do Re Mi Mi Mi Re Mi Re</p>
                            <p>Do Mi Sol</p>
                            <p>Do Re Mi Mi Mi Re Mi Re</p>
                            <p>Do Mi Do</p>
                            <p>Re Mi Fa Fa Fa Mi Fa Mi</p>
                            <p>Re Fa La</p>
                            <p>Do Re Mi Mi Mi Re Mi Re</p>
                            <p>Do Mi Do</p>
                        </div>
                        <div class="chord-chart">
                            <h4>Acordes:</h4>
                            <p>Do Mayor (C): Do-Mi-Sol</p>
                            <p>Fa Mayor (F): Fa-La-Do</p>
                            <p>Sol Mayor (G): Sol-Si-Re</p>
                        </div>
                    </div>
                `
            },
            {
                id: 'song4',
                title: 'Para Elisa (Simplificada)',
                difficulty: 'Intermedio',
                notation: `
                    <div class="song-notation">
                        <h3>Partitura</h3>
                        <img src="assets/images/fur_elise_sheet.png" alt="Partitura de Para Elisa" class="sheet-music">
                        <h3>Tablatura</h3>
                        <div class="tablature">
                            <p>Mi Re# Mi Re# Mi Si Re Do La</p>
                            <p>Do Mi La Si Mi La Si Do</p>
                            <p>Mi Re# Mi Re# Mi Si Re Do La</p>
                            <p>Do Mi La Si Mi Do Si La</p>
                        </div>
                        <div class="chord-chart">
                            <h4>Acordes:</h4>
                            <p>La menor (Am): La-Do-Mi</p>
                            <p>Mi Mayor (E): Mi-Sol#-Si</p>
                        </div>
                    </div>
                `
            },
            {
                id: 'song5',
                title: 'Himno a la Alegría',
                difficulty: 'Intermedio',
                notation: `
                    <div class="song-notation">
                        <h3>Partitura</h3>
                        <img src="assets/images/ode_to_joy_sheet.png" alt="Partitura del Himno a la Alegría" class="sheet-music">
                        <h3>Tablatura</h3>
                        <div class="tablature">
                            <p>Mi Mi Fa Sol Sol Fa Mi Re</p>
                            <p>Do Do Re Mi Mi Re Re</p>
                            <p>Mi Mi Fa Sol Sol Fa Mi Re</p>
                            <p>Do Do Re Mi Re Do Do</p>
                            <p>Re Re Mi Do Re Mi Fa Mi Do</p>
                            <p>Re Mi Fa Mi Re Do Re Sol</p>
                            <p>Mi Mi Fa Sol Sol Fa Mi Re</p>
                            <p>Do Do Re Mi Re Do Do</p>
                        </div>
                        <div class="chord-chart">
                            <h4>Acordes:</h4>
                            <p>Do Mayor (C): Do-Mi-Sol</p>
                            <p>Sol Mayor (G): Sol-Si-Re</p>
                        </div>
                    </div>
                `
            }
        ]
    };

    // Función para renderizar el cancionero
    function renderSongbook() {
        const songbookContainer = document.querySelector('.tool-card:nth-child(2)');
        if (!songbookContainer) return;
        
        songbookContainer.innerHTML = `
            <h3>📖 Cancionero Completo</h3>
            <p>Selecciona una canción para ver su partitura y tablatura:</p>
            <div class="songbook-list"></div>
            <div class="song-details"></div>
        `;
        
        const songbookList = songbookContainer.querySelector('.songbook-list');
        
        SONGBOOK.songs.forEach(song => {
            const songItem = document.createElement('div');
            songItem.className = 'songbook-item';
            songItem.innerHTML = `
                <span class="song-title">${song.title}</span>
                <span class="song-difficulty ${song.difficulty.toLowerCase()}">${song.difficulty}</span>
            `;
            
            songItem.addEventListener('click', () => {
                document.querySelectorAll('.songbook-item').forEach(item => item.classList.remove('active'));
                songItem.classList.add('active');
                
                const songDetails = songbookContainer.querySelector('.song-details');
                songDetails.innerHTML = `
                    <h4>${song.title}</h4>
                    ${song.notation}
                `;
            });
            
            songbookList.appendChild(songItem);
        });
    }
    
    // --- FUNCIONES DE NAVEGACIÓN Y VISTAS ---
    function showView(viewId, data = {}) {
        views.forEach(view => view.classList.remove('active'));
        const targetView = document.getElementById(`${viewId}-view`);
        if (targetView) {
            showLoading();
            setTimeout(() => { // Simular carga de contenido
                targetView.classList.add('active');
                hideLoading();
                if (viewId === 'lesson') {
                    renderLesson(data.moduleId, data.lessonId);
                }
            }, 300);
        }
        updateActiveNavLink(viewId);
        closeMobileMenu();
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
    
    function toggleMobileMenu() {
        mainNav.classList.toggle('open');
    }

    function closeMobileMenu() {
        mainNav.classList.remove('open');
    }

    // --- FUNCIONES DE AUTENTICACIÓN ---
    function login(username, password) {
        const user = USERS[username];
        if (user && user.password === password) {
            currentUser = username;
            closeModal();
            renderNav();
            showView('dashboard');
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
        if (noteIdentificationExercise) noteIdentificationExercise.stop();
        if (pianoRunnerGame) pianoRunnerGame.stop();
    }

    // --- FUNCIONES DE RENDERIZADO DE CONTENIDO ---
    function renderDashboard() {
        document.getElementById('current-user-name').textContent = currentUser.split(' ')[0];
        const progress = calculateOverallProgress();
        document.getElementById('general-progress-bar').style.width = `${progress}%`;
        document.getElementById('progress-percentage').textContent = `${progress}% Completado`;
        document.getElementById('lessons-completed-count').textContent = USERS[currentUser].stats.lessonsCompleted;
        document.getElementById('games-played-count').textContent = USERS[currentUser].stats.gamesPlayed;
        
        // Lógica para próxima lección
        const nextLesson = findNextLesson();
        if (nextLesson) {
            document.getElementById('next-lesson').textContent = `Continúa con ${nextLesson.module.title.split(':')[1]} - ${nextLesson.lesson.title}`;
        } else {
            document.getElementById('next-lesson').textContent = '¡Has completado todo el curso!';
        }
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
    
    function findNextLesson() {
        for (const module of COURSE_DATA.modules) {
            if (!USERS[currentUser].progress.course[module.id].completed) {
                for (const lesson of module.lessons) {
                    if (!USERS[currentUser].progress.course[module.id].lessons[lesson.id]) {
                        return { module, lesson };
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
                lessonItem.addEventListener('click', () => {
                    showView('lesson', { moduleId: module.id, lessonId: lesson.id });
                });
                lessonsList.appendChild(lessonItem);
            });

            moduleHeader.addEventListener('click', () => {
                lessonsList.classList.toggle('open');
            });

            moduleCard.appendChild(moduleHeader);
            moduleCard.appendChild(lessonsList);
            container.appendChild(moduleCard);
        });
    }

    function renderLesson(moduleId, lessonId) {
        const module = COURSE_DATA.modules.find(m => m.id === moduleId);
        const lesson = module.lessons.find(l => l.id === lessonId);
        
        document.getElementById('lesson-title').textContent = lesson.title;
        document.getElementById('lesson-content').innerHTML = lesson.content;
        
        document.getElementById('back-to-course').onclick = () => showView('course');
        
        // Añadir botón de completar lección
        const lessonActions = document.createElement('div');
        lessonActions.className = 'lesson-actions';
        
        const completeButton = document.createElement('button');
        completeButton.className = 'btn btn-primary complete-lesson-btn';
        
        // Verificar si la lección ya está completada
        const isCompleted = USERS[currentUser].progress.course[moduleId].lessons[lessonId];
        
        completeButton.textContent = isCompleted ? '✅ Lección Completada' : 'Marcar como Completada';
        completeButton.disabled = isCompleted;
        
        completeButton.addEventListener('click', () => {
            if (!USERS[currentUser].progress.course[moduleId].lessons[lessonId]) {
                USERS[currentUser].progress.course[moduleId].lessons[lessonId] = true;
                USERS[currentUser].stats.lessonsCompleted++;
                checkModuleCompletion(moduleId);
                completeButton.textContent = '✅ Lección Completada';
                completeButton.disabled = true;
                
                // Mostrar mensaje de felicitación
                const congratsMessage = document.createElement('div');
                congratsMessage.className = 'congrats-message';
                congratsMessage.textContent = '¡Felicidades! Has completado esta lección.';
                lessonActions.appendChild(congratsMessage);
                
                // Hacer que desaparezca después de 3 segundos
                setTimeout(() => {
                    congratsMessage.style.opacity = '0';
                    setTimeout(() => congratsMessage.remove(), 500);
                }, 3000);
            }
        });
        
        lessonActions.appendChild(completeButton);
        document.getElementById('lesson-content').appendChild(lessonActions);
        
        virtualPiano.classList.add('visible');
    }
    
    function checkModuleCompletion(moduleId) {
        const module = COURSE_DATA.modules.find(m => m.id === moduleId);
        const allLessonsCompleted = module.lessons.every(lesson => 
            USERS[currentUser].progress.course[moduleId].lessons[lesson.id]
        );
        if (allLessonsCompleted) {
            USERS[currentUser].progress.course[moduleId].completed = true;
        }
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

        const pianoContainer = document.querySelector('.piano-keys');

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
                const leftPosition = (index * 50) + 35;
                blackKey.style.left = `${leftPosition}px`;
                blackKey.addEventListener('mousedown', () => playNote(blackKeys[index], pianoKeys[blackKeys[index]]));
                pianoContainer.appendChild(blackKey);
            }
        });

        window.addEventListener('keydown', (e) => {
            if (keyboardMap[e.key.toLowerCase()] && virtualPiano.classList.contains('visible')) {
                const note = keyboardMap[e.key.toLowerCase()];
                const keyElement = virtualPiano.querySelector(`[data-note="${note}"]`);
                if (keyElement && !e.repeat) {
                    keyElement.classList.add('active');
                    playNote(note, pianoKeys[note]);
                    highlightLabel(note);
                }
            }
        });
        window.addEventListener('keyup', (e) => {
            if (keyboardMap[e.key.toLowerCase()] && virtualPiano.classList.contains('visible')) {
                const note = keyboardMap[e.key.toLowerCase()];
                const keyElement = virtualPiano.querySelector(`[data-note="${note}"]`);
                if (keyElement) {
                    keyElement.classList.remove('active');
                    unhighlightLabel(note);
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
        
        // Notificar a los juegos/ejercicios si están activos
        if (noteIdentificationExercise) noteIdentificationExercise.checkAnswer(note);
        if (pianoRunnerGame) pianoRunnerGame.checkHit(note);
    }
    
    function highlightLabel(note) {
        const label = document.querySelector(`.piano-label[data-note="${note}"]`);
        if(label) label.style.color = 'var(--primary-color)';
    }
    function unhighlightLabel(note) {
        const label = document.querySelector(`.piano-label[data-note="${note}"]`);
        if(label) label.style.color = 'var(--text-muted)';
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
        if (!audioContext) audioContext = new (window.AudioContext || window.webkitAudioContext)();
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

    // --- EJERCICIO: IDENTIFICACIÓN DE NOTAS ---
    class NoteIdentificationExercise {
        constructor() {
            this.canvas = document.getElementById('exercise-canvas');
            this.ctx = this.canvas.getContext('2d');
            this.feedback = document.getElementById('exercise-feedback');
            this.notes = ['C', 'D', 'E', 'F', 'G', 'A', 'B'];
            this.currentNote = null;
            this.isRunning = false;
        }

        start() {
            this.isRunning = true;
            this.canvas.style.display = 'block';
            this.feedback.innerHTML = '';
            this.generateNewNote();
        }

        stop() {
            this.isRunning = false;
            this.canvas.style.display = 'none';
            this.feedback.innerHTML = '';
        }

        generateNewNote() {
            if (!this.isRunning) return;
            this.currentNote = this.notes[Math.floor(Math.random() * this.notes.length)];
            this.drawStaff();
            this.drawNote();
        }

        drawStaff() {
            const ctx = this.ctx;
            const w = this.canvas.width;
            const h = this.canvas.height;
            ctx.clearRect(0, 0, w, h);
            ctx.strokeStyle = '#f0f0f0';
            ctx.lineWidth = 2;
            
            const lineSpacing = h / 5;
            for (let i = 1; i <= 5; i++) {
                ctx.beginPath();
                ctx.moveTo(20, i * lineSpacing);
                ctx.lineTo(w - 20, i * lineSpacing);
                ctx.stroke();
            }
        }

        drawNote() {
            const ctx = this.ctx;
            const notePositions = { 'E': 1, 'F': 2, 'G': 3, 'A': 4, 'B': 5, 'C': 6, 'D': 7 };
            const lineSpacing = this.canvas.height / 5;
            const y = notePositions[this.currentNote] * (lineSpacing / 2);
            
            ctx.fillStyle = '#f4c430';
            ctx.beginPath();
            ctx.arc(this.canvas.width / 2, y, 10, 0, 2 * Math.PI);
            ctx.fill();
        }

        checkAnswer(note) {
            if (!this.isRunning || !this.currentNote) return;
            
            if (note === this.currentNote) {
                this.feedback.innerHTML = `<p style="color: #4caf50;">¡Correcto! Era la nota ${this.currentNote}.</p>`;
                setTimeout(() => this.generateNewNote(), 1500);
            } else {
                this.feedback.innerHTML = `<p style="color: #e74c3c;">Incorrecto. Intenta de nuevo.</p>`;
            }
        }
    }

    // --- JUEGO: PIANO RUNNER ---
    class PianoRunnerGame {
        constructor() {
            this.canvas = document.getElementById('game-canvas');
            this.ctx = this.canvas.getContext('2d');
            this.scoreEl = document.getElementById('game-score');
            this.livesEl = document.getElementById('game-lives');
            this.startBtn = document.getElementById('start-game-btn');
            
            this.notes = ['C', 'D', 'E', 'F', 'G', 'A', 'B'];
            this.fallingNotes = [];
            this.score = 0;
            this.lives = 3;
            this.gameSpeed = 2;
            this.noteInterval = 1000;
            this.lastNoteTime = 0;
            this.isRunning = false;
            this.animationId = null;
            this.noteSpawnInterval = null;
        }

        start() {
            this.isRunning = true;
            this.score = 0;
            this.lives = 3;
            this.fallingNotes = [];
            this.gameSpeed = 2;
            this.updateUI();
            this.canvas.style.display = 'block';
            this.startBtn.textContent = 'Detener Juego';
            this.startBtn.onclick = () => this.stop();
            
            this.noteSpawnInterval = setInterval(() => this.spawnNote(), this.noteInterval);
            this.gameLoop();
        }

        stop() {
            this.isRunning = false;
            cancelAnimationFrame(this.animationId);
            clearInterval(this.noteSpawnInterval);
            this.canvas.style.display = 'none';
            this.startBtn.textContent = 'Iniciar Juego';
            this.startBtn.onclick = () => this.start();
            USERS[currentUser].stats.gamesPlayed++;
        }

        spawnNote() {
            const note = this.notes[Math.floor(Math.random() * this.notes.length)];
            const keyWidth = this.canvas.width / this.notes.length;
            const x = this.notes.indexOf(note) * keyWidth + keyWidth / 2;
            this.fallingNotes.push({ note, x, y: -20 });
        }

        gameLoop() {
            if (!this.isRunning) return;
            
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
            
            // Dibujar líneas de meta
            this.ctx.strokeStyle = '#f4c430';
            this.ctx.lineWidth = 3;
            this.ctx.beginPath();
            this.ctx.moveTo(0, this.canvas.height - 50);
            this.ctx.lineTo(this.canvas.width, this.canvas.height - 50);
            this.ctx.stroke();

            // Actualizar y dibujar notas
            this.fallingNotes = this.fallingNotes.filter(note => {
                note.y += this.gameSpeed;
                
                this.ctx.fillStyle = '#f0f0f0';
                this.ctx.fillRect(note.x - 20, note.y - 10, 40, 20);
                this.ctx.fillStyle = '#1a1a1a';
                this.ctx.font = '16px Roboto';
                this.ctx.textAlign = 'center';
                this.ctx.fillText(note.note, note.x, note.y + 5);
                
                // Comprobar si se perdió la nota
                if (note.y > this.canvas.height) {
                    this.loseLife();
                    return false;
                }
                return true;
            });

            this.animationId = requestAnimationFrame(() => this.gameLoop());
        }

        checkHit(pressedNote) {
            if (!this.isRunning) return;
            
            const hitZoneTop = this.canvas.height - 70;
            const hitZoneBottom = this.canvas.height - 30;

            for (let i = this.fallingNotes.length - 1; i >= 0; i--) {
                const note = this.fallingNotes[i];
                if (note.note === pressedNote && note.y > hitZoneTop && note.y < hitZoneBottom) {
                    this.fallingNotes.splice(i, 1);
                    this.score += 10;
                    this.updateUI();
                    // Aumentar dificultad
                    if (this.score % 50 === 0) this.gameSpeed += 0.5;
                    break;
                }
            }
        }

        loseLife() {
            this.lives--;
            this.updateUI();
            if (this.lives <= 0) {
                this.gameOver();
            }
        }

        gameOver() {
            this.stop();
            this.ctx.fillStyle = 'rgba(0,0,0,0.7)';
            this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
            this.ctx.fillStyle = '#f4c430';
            this.ctx.font = 'bold 48px Montserrat';
            this.ctx.textAlign = 'center';
            this.ctx.fillText('GAME OVER', this.canvas.width / 2, this.canvas.height / 2);
            this.ctx.font = '24px Roboto';
            this.ctx.fillText(`Puntuación Final: ${this.score}`, this.canvas.width / 2, this.canvas.height / 2 + 50);
        }

        updateUI() {
            this.scoreEl.textContent = `Puntuación: ${this.score}`;
            this.livesEl.innerHTML = `Vidas: ${'❤️'.repeat(this.lives)}`;
        }
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
        
        // Acceso de administrador (5 clics rápidos)
        const adminTrigger = document.getElementById('admin-trigger');
        let clickCount = 0;
        let clickTimer;
        
        adminTrigger.addEventListener('click', () => {
            clickCount++;
            
            clearTimeout(clickTimer);
            clickTimer = setTimeout(() => {
                if (clickCount >= 5) {
                    modalUsername.textContent = 'Admin';
                    passwordModal.dataset.user = 'Admin';
                    openModal();
                }
                clickCount = 0;
            }, 1000);
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

        // Navegación móvil
        menuToggle.addEventListener('click', toggleMobileMenu);
        
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

        // Inicialización de ejercicios y juegos
        document.querySelector('[data-exercise="note-identification"]').addEventListener('click', () => {
            if (!noteIdentificationExercise) noteIdentificationExercise = new NoteIdentificationExercise();
            noteIdentificationExercise.start();
            virtualPiano.classList.add('visible');
        });

        document.querySelector('[data-game="piano-runner"]').addEventListener('click', () => {
            if (!pianoRunnerGame) pianoRunnerGame = new PianoRunnerGame();
            virtualPiano.classList.add('visible');
        });
        
        initMetronome();
    }

    // --- FUNCIONES DE MODAL Y LOADING ---
    function openModal() {
        passwordModal.style.display = 'block';
        passwordInput.value = '';
        loginError.textContent = '';
        passwordInput.focus();
    }

    function closeModal() {
        passwordModal.style.display = 'none';
    }
    
    function showLoading() {
        loadingSpinner.style.display = 'block';
    }

    function hideLoading() {
        loadingSpinner.style.display = 'none';
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