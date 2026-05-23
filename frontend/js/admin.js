const API_URL = 'http://localhost/zoo/backend/index.php';

document.addEventListener('DOMContentLoaded', () => {

    const btnLogin = document.getElementById('btn-login');
    const loginBox = document.getElementById('login-box');
    const dashboard = document.getElementById('dashboard');
    const eroareDiv = document.getElementById('eroare');
    const btnExit = document.querySelector('.btn-exit');

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
                    sessionStorage.setItem('isAdmin', 'true');
                    
                    loginBox.style.display = 'none';
                    dashboard.style.display = 'block';
                    
                    const userNameDisplay = document.querySelector('.user-name');
                    if(userNameDisplay) userNameDisplay.innerText = "Administrator";
                    
                    loadAdminAnimals();
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
        btnExit.addEventListener('click', (e) => {
            e.preventDefault(); 
            
            sessionStorage.removeItem('isAdmin');
            
            dashboard.style.display = 'none';
            loginBox.style.display = 'block';
            
            document.getElementById('password').value = '';
            document.getElementById('zona-formular').style.display = 'none';
            
            const userNameDisplay = document.querySelector('.user-name');
            if(userNameDisplay) userNameDisplay.innerText = "User";
        });
    }

    const btnAdaugaNou = document.querySelector('button[style*="background-color: #2980b9"]');
    if (btnAdaugaNou) {
        btnAdaugaNou.addEventListener('click', () => {
            document.getElementById('zona-formular').style.display = 'block';
            populateDropdowns();
        });
    }

    document.getElementById('btn-anuleaza').addEventListener('click', () => {
        document.getElementById('zona-formular').style.display = 'none';
        document.getElementById('form-animal').reset(); 
    });

    const formAnimal = document.getElementById('form-animal');
    if (formAnimal) {
        formAnimal.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const payload = {
                nume_popular: document.getElementById('add-nume-pop').value,
                nume_stiintific: document.getElementById('add-nume-st').value,
                id_clasa: document.getElementById('add-clasa').value,
                id_origine: document.getElementById('add-origine').value,
                id_regim: document.getElementById('add-regim').value,
                id_statut: document.getElementById('add-statut').value,
                id_clima: document.getElementById('add-clima').value,
                id_inmultire: document.getElementById('add-inmultire').value,
                descriere_ro: document.getElementById('add-desc-ro').value,
                are_blana: document.getElementById('add-blana').checked ? 1 : 0,
                poate_fi_dresat: document.getElementById('add-dresabil').checked ? 1 : 0,
                este_periculos: document.getElementById('add-periculos').checked ? 1 : 0,
                imagine: document.getElementById('add-imagine').value
            };

            try {
                const response = await fetch(`${API_URL}?action=add_animal`, {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });

                const res = await response.json();
                alert(res.message);
                
                if(res.status === 'success') {
                    document.getElementById('zona-formular').style.display = 'none';
                    formAnimal.reset();
                    loadAdminAnimals(); 
                }
            } catch (err) {
                console.error("Eroare la salvare:", err);
                alert("A apărut o eroare la salvarea animalului.");
            }
        });
    }

    const btnExportJson = document.getElementById('btn-export-json');
    if(btnExportJson) {
        btnExportJson.addEventListener('click', () => {
            window.location.href = `${API_URL}?action=export_json`;
        });
    }

    const btnExportXml = document.getElementById('btn-export-xml');
    if(btnExportXml) {
        btnExportXml.addEventListener('click', () => {
            window.location.href = `${API_URL}?action=export_xml`;
        });
    }

}); 

async function loadAdminAnimals() {
    try {
        const response = await fetch(`${API_URL}?action=get_animals`);
        const data = await response.json();

        const tbody = document.getElementById('tabel-animale');
        tbody.innerHTML = ''; // Curățăm tabelul

        if (data.status === 'success' && data.data.length > 0) {
            data.data.forEach(animal => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${animal.ID_ANIMAL}</td>
                    <td><strong>${animal.NUME_POPULAR}</strong> <br> <span style="font-size:0.85rem; color:#7f8c8d;"><i>${animal.NUME_STIINTIFIC}</i></span></td>
                    <td>${animal.CLASA_ANIMAL}</td>
                    <td>
                        <button onclick="deleteAnimal(${animal.ID_ANIMAL})" class="btn-delete">Șterge</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">Nu există animale în baza de date.</td></tr>';
        }
    } catch (error) {
        console.error('Eroare la încărcarea tabelului', error);
    }
}

async function deleteAnimal(id) {
    if (confirm(`Ești sigur că vrei să ștergi animalul cu ID-ul ${id}? Acest proces este ireversibil!`)) {
        try {
            const response = await fetch(`${API_URL}?action=delete_animal&id=${id}`, {
                method: 'GET' 
            });
            const data = await response.json();

            alert(data.message);
            
            if (data.status === 'success') {
                loadAdminAnimals(); 
            }
        } catch (error) {
            console.error('Eroare la ștergere', error);
        }
    }
}
