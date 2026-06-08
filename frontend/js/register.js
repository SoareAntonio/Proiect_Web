const API_URL = '../backend/index.php';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-register-modern');
    const messageBox = document.getElementById('register-message');

    if (!form) {
        return;
    }

    function showMessage(message, type) {
        messageBox.textContent = message;
        messageBox.className = `auth-message ${type}`;
        messageBox.style.display = 'block';
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const username = document.getElementById('register-username').value.trim();
        const email = document.getElementById('register-email').value.trim();
        const password = document.getElementById('register-password').value;
        const confirmPassword = document.getElementById('register-confirm-password').value;

        if (password !== confirmPassword) {
            showMessage('Parolele nu coincid.', 'error');
            return;
        }

        try {
            const response = await fetch(`${API_URL}?action=register`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    username,
                    email,
                    password,
                    confirmPassword
                })
            });

            const data = await response.json();

            if (data.status === 'success') {
                showMessage(data.message, 'success');
                form.reset();

                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 1200);

                return;
            }

            showMessage(data.message || 'Nu s-a putut crea contul.', 'error');
        } catch (error) {
            console.error('Eroare la înregistrare:', error);
            showMessage('Eroare de conexiune la server.', 'error');
        }
    });
});
