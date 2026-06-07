document.addEventListener('DOMContentLoaded', () => {
    
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

    const fileImportInput = document.getElementById('file-import');
    const fileNameDisplay = document.getElementById('file-name-display');
    const btnImportExecuta = document.getElementById('btn-import-executa');

    if(fileImportInput && fileNameDisplay && btnImportExecuta) {
        fileImportInput.addEventListener('change', () => {
            if (fileImportInput.files.length > 0) {
                fileNameDisplay.textContent = fileImportInput.files[0].name;
            } else {
                fileNameDisplay.textContent = "Niciun fișier selectat";
            }
        });

        btnImportExecuta.addEventListener('click', () => {
            if (fileImportInput.files.length === 0) {
                alert("Te rog selectează mai întâi un fișier JSON sau XML!");
                return;
            }

            const fisier = fileImportInput.files[0];
            const extensie = fisier.name.split('.').pop().toLowerCase();

            const formData = new FormData();
            formData.append('fisier_import', fisier);

            let action = '';
            if (extensie === 'json') action = 'import_json';
            else if (extensie === 'xml') action = 'import_xml';
            else {
                alert("Format neacceptat! Încarcă doar .json sau .xml");
                return;
            }

            const token = localStorage.getItem('token_zoo');
            if (!token) { alert("Acces respins! Trebuie să fii logat pentru a importa date."); return; }

            fetch(`${API_URL}?action=${action}`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                body: formData 
            })
            .then(response =>{
                if (response.status === 401) {
                    localStorage.removeItem('token_zoo');
                    window.location.href = 'login.html';
                    throw new Error("Sesiune invalidă sau expirată");
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    alert(`Succes! Au fost importate ${data.numar_animale} animale în baza de date.`);
                    location.reload(); 
                } else {
                    alert("Eroare la import: " + data.message);
                }
            })
            .catch(error => {
                console.error("Eroare server:", error);
                alert("A apărut o eroare la comunicarea cu serverul.");
            });
        });
    }
});