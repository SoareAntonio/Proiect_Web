const API_URL = '../backend/index.php';

const elements = {
    grid: document.getElementById('lista-rezultate'),
    btnCauta: document.getElementById('btn-cauta'),
    btnClear: document.getElementById('btn-clear'),
    clasa: document.getElementById('filtru-clasa'),
    origine: document.getElementById('filtru-origine'),
    regim: document.getElementById('filtru-regim'),
    statut: document.getElementById('filtru-statut'), 
    inmultire: document.getElementById('filtru-inmultire'),
    clima: document.getElementById('filtru-clima'),
    blana: document.getElementById('filtru-blana'),
    dresabil: document.getElementById('filtru-dresabil'),
    periculos: document.getElementById('filtru-periculos')
};

let limbaCurenta = 'ro';
let animaleInMemorie = [];

document.addEventListener('DOMContentLoaded', () => {
    fetchAnimale();
    incarcaFiltreDinamice();

    const btnRO = document.getElementById('btn-lang-ro');
    const btnEN = document.getElementById('btn-lang-en');

    if (btnRO && btnEN) {
        btnRO.addEventListener('click', (e) => {
            e.preventDefault();
            if (limbaCurenta === 'ro') return; 
            
            limbaCurenta = 'ro'; 
            btnRO.classList.add('active');
            btnEN.classList.remove('active');
            
            if (animaleInMemorie.length > 0) renderAnimale(animaleInMemorie, animaleInMemorie.length);
        });

        btnEN.addEventListener('click', (e) => {
            e.preventDefault();
            if (limbaCurenta === 'en') return; 
            
            limbaCurenta = 'en'; 
            btnEN.classList.add('active');
            btnRO.classList.remove('active');
            
            if (animaleInMemorie.length > 0) renderAnimale(animaleInMemorie, animaleInMemorie.length);
        });
    }

    elements.btnCauta.addEventListener('click', fetchAnimale);
    elements.btnClear.addEventListener('click', resetFilters);

    [elements.clasa, elements.origine, elements.regim, elements.clima, elements.blana, elements.dresabil, elements.periculos]
        .filter(Boolean)
        .forEach((input) => input.addEventListener('change', fetchAnimale));
});

function buildAnimalsUrl() {
    const params = new URLSearchParams();
    params.append('action', 'get_animals');

    if (elements.clasa.value) params.append('id_clasa', elements.clasa.value);
    if (elements.origine.value) params.append('id_origine', elements.origine.value);
    if (elements.regim && elements.regim.value) params.append('id_regim', elements.regim.value);
    if (elements.clima && elements.clima.value) params.append('id_clima', elements.clima.value);
    if (elements.statut && elements.statut.value) params.append('id_statut', elements.statut.value); 
    if (elements.inmultire && elements.inmultire.value) params.append('id_inmultire', elements.inmultire.value);
    if (elements.blana.checked) params.append('are_blana', '1');
    if (elements.dresabil.checked) params.append('poate_fi_dresat', '1');
    if (elements.periculos && elements.periculos.checked) params.append('este_periculos', '1');

    return `${API_URL}?${params.toString()}`;
}

async function fetchAnimale() {
    showLoadingState();

    try {
        const response = await fetch(buildAnimalsUrl());
        const data = await response.json();

        if (data.status !== 'success') {
            showMessage(data.message || 'A apărut o eroare la încărcarea animalelor.');
            return;
        }

        animaleInMemorie = data.data || [];

        renderAnimale(data.data || [], data.rezultate || 0);
    } catch (error) {
        console.error('Eroare la fetch:', error);
        showMessage('Eroare la conectarea cu serverul. Verifică dacă Apache și Oracle rulează.');
    }
}

async function incarcaFiltreDinamice() {
    try {
        const response = await fetch(`${API_URL}?action=get_categorii`);
        const res = await response.json();

        if (res.status === 'success') {
            const date = res.data;

            const umpleSelect = (idHTML, listaDate) => {
                const selectElement = document.getElementById(idHTML);
                if (!selectElement || !listaDate) return;

                const primaOptiune = selectElement.options[0].outerHTML;
                selectElement.innerHTML = primaOptiune;

                listaDate.forEach(item => {
                    const keys = Object.keys(item);
                    const id = item[keys[0]]; 
                    const denumire = item[keys[1]]; 
                    selectElement.innerHTML += `<option value="${id}">${denumire}</option>`;
                });
            };

            umpleSelect('filtru-clasa', date.clase);
            umpleSelect('filtru-origine', date.origini);
            umpleSelect('filtru-regim', date.regimuri);
            umpleSelect('filtru-clima', date.clime);
            umpleSelect('filtru-statut', date.statute); 
            umpleSelect('filtru-inmultire', date.inmultire); 
            
        }
    } catch (error) {
        console.error("Eroare la încărcarea filtrelor:", error);
    }
}

function renderAnimale(animale, numarRezultate) {
    elements.grid.innerHTML = '';

    if (numarRezultate === 0 || animale.length === 0) {
        showMessage('Nu s-au găsit animale cu aceste filtre.');
        return;
    }

    animale.forEach((animal) => {
        elements.grid.appendChild(createAnimalCard(animal));
    });
}

function createAnimalCard(animal) {
    const card = document.createElement('article');
    card.className = 'card animal-card';

    const image = document.createElement('img');
    image.className = 'card-img';
    image.src = normalizeImagePath(animal.url_imagine);
    image.alt = animal.nume_popular || 'Imagine animal';
    image.onerror = () => {
        image.src = './assets/images/header.png';
    };

    const content = document.createElement('div');
    content.className = 'card-content';

    const title = document.createElement('h3');
    title.className = 'card-title';
    title.textContent = animal.nume_popular || 'Animal fără nume';

    const scientificName = document.createElement('p');
    scientificName.className = 'card-scientific';
    scientificName.textContent = animal.nume_stiintific || 'Denumire științifică indisponibilă';

    const tags = document.createElement('div');
    tags.className = 'card-tags';

    addTag(tags,animal.clasa);
    addTag(tags,animal.origine);
    addTag(tags, animal.regim_alimentar);
    addTag(tags, animal.clima);
    addTag(tags, animal.mod_inmultire);
    addTag(tags, animal.statut); 

    const description = document.createElement('p');
    description.className = 'card-description';
    
    let rawDescription = '';
    if (limbaCurenta === 'en') {
        rawDescription = animal.descriere_en || 'Description not available in English yet.';
    } else {
        rawDescription = animal.descriere_ro || 'Descriere indisponibilă.';
    }

    description.textContent = rawDescription.length > 150 ? `${rawDescription.substring(0, 150)}...` : rawDescription;

    const textDusmani = animal.dusmani_naturali || 'Niciunul cunoscut';
    const textInrudite = animal.specii_inrudite || 'Niciuna cunoscută';

    const relatiiDiv = document.createElement('div');
    relatiiDiv.className = 'card-relatii';
    relatiiDiv.innerHTML = `
        <p><strong>Dușmani naturali:</strong> ${textDusmani}</p>
        <p><strong>Specii înrudite:</strong> ${textInrudite}</p>
    `;

    content.appendChild(title);
    content.appendChild(scientificName);
    content.appendChild(tags);
    content.appendChild(description);
    content.appendChild(relatiiDiv);

    card.appendChild(image);
    card.appendChild(content);

    return card;
}

function addTag(container, value) {
    if (!value) return;

    const tag = document.createElement('span');
    tag.className = 'tag';
    tag.textContent = value;
    container.appendChild(tag);
}

function normalizeImagePath(path) {
    if (!path) return './assets/images/header.png';
    if (path.startsWith('http') || path.startsWith('./')) return path;
    return `./${path}`;
}

function resetFilters() {
    if (elements.clasa) elements.clasa.value = '';
    if (elements.origine) elements.origine.value = '';
    if (elements.regim) elements.regim.value = '';
    if (elements.clima) elements.clima.value = '';
    if (elements.statut) elements.statut.value = '';
    if (elements.inmultire) elements.inmultire.value = '';
    if (elements.blana) elements.blana.checked = false;
    if (elements.dresabil) elements.dresabil.checked = false;
    if (elements.periculos) elements.periculos.checked = false;

    fetchAnimale();
}

function showLoadingState() {
    elements.grid.innerHTML = '<p class="state-message">Se încarcă datele din baza de date Oracle...</p>';
}

function showMessage(message) {
    elements.grid.innerHTML = '';
    const paragraph = document.createElement('p');
    paragraph.className = 'state-message';
    paragraph.textContent = message;
    elements.grid.appendChild(paragraph);
}
