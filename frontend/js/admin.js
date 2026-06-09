const API_URL = '../backend/index.php';

async function loadAdminAnimals() {
    try {
        const token = localStorage.getItem('token_zoo');

        const response = await fetch(`${API_URL}?action=get_animals`,{
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token, // Aici e cheia succesului!
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        const tbody = document.getElementById('tabel-animale');
        tbody.innerHTML = ''; 

        if (data.status === 'success' && data.data.length > 0) {
            data.data.forEach(animal => {

                const idAnimal = animal.id;
                const numePop = animal.nume_popular;
                const numeSt = animal.nume_stiintific;
                const clasaAnimal = animal.clasa;

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${idAnimal}</td>
                    <td><strong>${numePop}</strong> <br> <span style="font-size:0.85rem; color:#7f8c8d;"><i>${numeSt}</i></span></td>
                    <td>${clasaAnimal}</td>
                    <td>
                        <button onclick="deleteAnimal(${idAnimal})" class="btn-delete">Șterge</button>
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

        const token = localStorage.getItem('token_zoo');

        if (!token) 
            { alert("Sesiune expirată! Te rugăm să te loghezi din nou."); 
            return; }

        try {
            const response = await fetch(`${API_URL}?action=delete_animal&id=${id}`, {
                method: 'GET' ,
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const data = await response.json();

            alert(data.message);
            
            if (data.status === 'success') {
                loadAdminAnimals(); 
            }
            else if (response.status === 401) {
                window.location.href = 'login.html';
            }
        } catch (error) {
            console.error('Eroare la ștergere', error);
        }
    }
}
document.addEventListener('DOMContentLoaded', () => {
    
    const token = localStorage.getItem('token_zoo');
    
    if (!token) {
        alert("Acces interzis! Te rugăm să te loghezi.");
        window.location.href = 'login.html';
        return; 
    }

    if (typeof loadAdminAnimals === "function") {
        loadAdminAnimals();
    }

    const btnExit = document.querySelector('.btn-exit');
    if (btnExit) {
        btnExit.addEventListener('click', (e) => {
            e.preventDefault(); 
            localStorage.removeItem('token_zoo');
            window.location.href = 'login.html';
        });
    }

    const btnStergeTot = document.getElementById('btn-sterge-tot');
    if (btnStergeTot) {
        btnStergeTot.addEventListener('click', async () => {
            const confirmare = confirm("Atenție! Ești  sigur că vrei să ștergi toate animalele din baza de date? ");
            
            if (confirmare) {
                try {
                    const token = localStorage.getItem('token_zoo');

                    const response = await fetch(`${API_URL}?action=delete_all_animals`, {
                        method: 'DELETE',
                        headers: { 'Authorization': 'Bearer ' + token }
                    });
                    
                    const data = await response.json();
                    
                    if (data.status === 'success') {
                        alert(data.message);
                        loadAdminAnimals(); 
                    } else {
                        alert("Eroare: " + data.message);
                    }
                } catch (error) {
                    console.error("Eroare la ștergerea tuturor animalelor:", error);
                    alert("Eroare de conexiune la server.");
                }
            }
        });
    }

    const btnAdaugaNou = document.getElementById('btn-adauga-animal');
    if (btnAdaugaNou) {
        btnAdaugaNou.addEventListener('click', () => {
            document.getElementById('zona-formular').style.display = 'block';
            if (typeof populateDropdowns === "function") populateDropdowns(); 
        });
    }

    const btnAnuleaza = document.getElementById('btn-anuleaza');
    if (btnAnuleaza) {
        btnAnuleaza.addEventListener('click', () => {
            document.getElementById('zona-formular').style.display = 'none';
            document.getElementById('form-animal').reset(); 
        });
    }

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

            const token = localStorage.getItem('token_zoo');
            if (!token) { alert("Nu ești autentificat!"); return; }

            try {
                const response = await fetch(`${API_URL}?action=add_animal`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token 
                    },
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
});