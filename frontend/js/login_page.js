const API_URL = '../backend/index.php';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-login-modern');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const rememberInput = document.querySelector('.remember-me input');
    const forgotLink = document.querySelector('.forgot-link');

    if (forgotLink) {
        forgotLink.addEventListener('click', (event) => {
            event.preventDefault();
            alert('Pentru resetarea parolei, te rugăm să contactezi administratorul la admin@zoomanager.ro');
        });
    }

    if (!form) {
        console.error('Formularul de login nu a fost găsit. Verifică id="form-login-modern" în login.html.');
        return;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const username = usernameInput.value.trim();
        const password = passwordInput.value;
        const remember = rememberInput ? rememberInput.checked : false;

        if (!username || !password) {
            alert('Completează username-ul și parola.');
            return;
        }

        try {
            const response = await fetch(`${API_URL}?action=login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify({
                    username: username,
                    password: password,
                    remember: remember
                })
            });

            const data = await response.json();

            console.log('Răspuns login:', data);

            if (data.status === 'success') {
                if (data.role === 'admin') {
                    window.location.href = 'admin.html';
                } else {
                    window.location.href = 'index.html';
                }

                return;
            }

            alert(data.message || 'Autentificare eșuată.');
        } catch (error) {
            console.error('Eroare la login:', error);
            alert('Eroare de conexiune la server.');
        }
    });
});