const API_URL = 'http://localhost/Proiect_web/backend/index.php?action=get_animals';

document.addEventListener('DOMContentLoaded', () => {
    
    fetchAnimale();

    document.getElementById('btn-filtreaza').addEventListener('click', fetchAnimale);
});

function fetchAnimale() {
    let url = API_URL;
    
    const areBlana = document.getElementById('are_blana').checked;
    const poateFiDresat = document.getElementById('poate_fi_dresat').checked;

    if (areBlana) url += '&are_blana=1';
    if (poateFiDresat) url += '&poate_fi_dresat=1';

    fetch(url)
        .then(response => response.json()) 
        .then(data => {
            if (data.status === 'success') {
                renderAnimale(data.data, data.rezultate);
            } else {
                console.error("Eroare de la server:", data.message);
            }
        })
        .catch(error => {
            console.error('Eroare la fetch:', error);
            document.getElementById('animale-grid').innerHTML = '<p>Eroare la conectarea cu serverul.</p>';
        });
}

function renderAnimale(animale, numarRezultate) {
    const grid = document.getElementById('animale-grid');
    grid.innerHTML = ''; 

    if (numarRezultate === 0) {
        grid.innerHTML = '<p>Nu s-au găsit animale cu aceste filtre.</p>';
        return;
    }

    animale.forEach(animal => {
    
      let imagine = animal.URL_IMAGINE || animal.url_imagine;

      if (!imagine) {
          imagine = ''; 
      }
        
        const nume = animal.NUME_POPULAR || animal.nume_popular;
        const numeStiintific = animal.NUME_STIINTIFIC || animal.nume_stiintific;
        const clasa = animal.CLASA_ANIMAL || animal.clasa_animal;
        const descriere = animal.DESCRIERE_RO || animal.descriere_ro || 'Fără descriere.';

    
        const cardHTML = `
            <div class="card">
                <img src="${imagine}" alt="${nume}">
                <div class="card-content">
                    <h2>${nume}</h2>
                    <p class="stiintific"><i>${numeStiintific}</i></p>
                    <span class="badge">${clasa}</span>
                    <p class="descriere">${descriere.substring(0, 100)}...</p>
                </div>
            </div>
        `;
        
    
        grid.innerHTML += cardHTML;
    });
}