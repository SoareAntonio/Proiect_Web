const API_URL = '../backend/index.php';

document.addEventListener('DOMContentLoaded',  () => {

    const formLoginModern = document.getElementById('form-login-modern');
    const inputUser = document.getElementById('username');
    const inputPass = document.getElementById('password');

    if (!formLoginModern) return;

    formLoginModern.addEventListener('submit', async (e) => {
        e.preventDefault();

        const user = inputUser.value;
        const pass = inputPass.value;

        try {
            const response = await fetch(`${API_URL}?action=login`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username: user, password: pass })
            });
            
            const data = await response.json();

            if (data.status === 'success') {
                
                localStorage.setItem('token_zoo', data.token);

                if (data.role === 'admin') {
                    window.location.href = 'admin.html'; 
                } else if (data.role === 'user') {
                    window.location.href = 'index.html'; 
                }
            } else {
                alert("Eroare: " + data.message); 
            }
        } catch (error) {
            console.error('Eroare conexiune API la Login:', error);
            alert('Eroare de conexiune la server.');
        }
    });

    
    const togglePassword = document.getElementById('toggle-password');
    if (togglePassword) {
        togglePassword.addEventListener('click', function (e) {
            e.preventDefault();
            
            if (inputPass.type === 'password') {
                inputPass.type = 'text'; 
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash'); 
            } else {
                inputPass.type = 'password'; 
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye'); 
            }
        });
    }

});