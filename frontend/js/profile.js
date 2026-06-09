const API_URL = '../backend/index.php';

document.addEventListener('DOMContentLoaded', async () => {
    const token = localStorage.getItem('token_zoo');
    
    if (!token) {
        alert("Sesiune expirată! Te rugăm să te loghezi.");
        window.location.href = 'login.html';
        return;
    }

    try {
        const response = await fetch(`${API_URL}?action=get_profile`, {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json'
            }
        });

        const res = await response.json();

        if (res.status === 'success') {
            
            document.getElementById('prof-username').textContent = res.data.username;
            document.getElementById('prof-email').textContent = res.data.email;
            document.getElementById('prof-date').textContent = res.data.data_creare;
            document.getElementById('prof-id').textContent = '#' + res.data.id;

            const btnBack = document.getElementById('btn-back');
            const roleText = document.getElementById('prof-role');
            
            if (res.data.role === 'admin') {
                roleText.textContent = 'Administrator C.R.U.D.';
                btnBack.textContent = 'Înapoi la Panou Admin';
                btnBack.href = 'admin.html';
            } else {
                roleText.textContent = 'Vizitator Zoo';
                btnBack.textContent = 'Înapoi la Site';
                btnBack.href = 'index.html';
            }

        } else {
            console.error("Eroare de la server:", res.message);
            localStorage.removeItem('token_zoo');
            window.location.href = 'login.html';
        }
    } catch (error) {
        console.error('Eroare de conexiune la server pentru profil:', error);
    }

    const btnLogout = document.getElementById('btn-logout-profile');
    if (btnLogout) {
        btnLogout.addEventListener('click', () => {
            localStorage.removeItem('token_zoo');
            window.location.href = 'login.html';
        });
    }
});