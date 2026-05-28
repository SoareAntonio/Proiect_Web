document.addEventListener('DOMContentLoaded', async () => {
    const btnLogin = document.getElementById('btn-login');
    const loginBox = document.getElementById('login-box');
    const dashboard = document.getElementById('dashboard');
    const eroareDiv = document.getElementById('eroare');
    const btnExit = document.querySelector('.btn-exit');

    try {
        const response = await fetch(`${API_URL}?action=verifica_sesiune`);
        const data = await response.json();

        if (data.status === 'success' && data.logat === true) {
        
            loginBox.style.display = 'none';
            dashboard.style.display = 'block';
            
            const userNameDisplay = document.querySelector('.user-name');
            if(userNameDisplay) userNameDisplay.innerText = "Administrator";
            
            if (typeof loadAdminAnimals === "function") loadAdminAnimals();
        } else {
        
            loginBox.style.display = 'block';
            dashboard.style.display = 'none';
        }
    } catch (error) {
        console.error("Eroare la verificarea sesiunii:", error);
    }

    if (btnLogin) {
        btnLogin.addEventListener('click', async () => {
            const user = document.getElementById('username').value;
            const pass = document.getElementById('password').value;

            eroareDiv.style.display = 'none';

            try {
                const response = await fetch(`${API_URL}?action=login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username: user, password: pass })
                });
                
                const data = await response.json();

                if (data.status === 'success') {
                    
                    loginBox.style.display = 'none';
                    dashboard.style.display = 'block';
                    
                    const userNameDisplay = document.querySelector('.user-name');
                    if(userNameDisplay) userNameDisplay.innerText = "Administrator";
                    
                    if (typeof loadAdminAnimals === "function") loadAdminAnimals();
                } else {
                    eroareDiv.style.display = 'block';
                    eroareDiv.innerText = data.message;
                }
            } catch (error) {
                console.error('Eroare conexiune API la Login:', error);
                eroareDiv.style.display = 'block';
                eroareDiv.innerText = 'Eroare de conexiune la server.';
            }
        });
    }

    if (btnExit) {
        btnExit.addEventListener('click',async (e) => {
            e.preventDefault(); 
            
            try {
                await fetch(`${API_URL}?action=logout`);
            } catch (error) {
                console.error('Eroare la logout:', error);
            }

            
            dashboard.style.display = 'none';
            loginBox.style.display = 'block';
            
            document.getElementById('password').value = '';

            const zonaFormular = document.getElementById('zona-formular');
            if (zonaFormular) zonaFormular.style.display = 'none';
            
            const userNameDisplay = document.querySelector('.user-name');
            if(userNameDisplay) userNameDisplay.innerText = "User";
        });
    }
});