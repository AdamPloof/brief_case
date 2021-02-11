// Twig Routing
import Routing from '../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
const routes = require('../public/js/fos_js_routes.json');
Routing.setRoutingData(routes);

document.addEventListener('DOMContentLoaded', function () {
    initAddOtherPersons();
    initRemoveOtherPersons();
    loadPrimaryPersons();
});

function initAddOtherPersons() {
    // Add event listener to add persons button to add new embedded person forms in new case view
    const addPersonBtn = document.getElementById("addPersonBtn");

    let assocPersonsContainer = document.getElementById("assocPersonsContainer");
    assocPersonsContainer.dataset.index = assocPersonsContainer.getElementsByTagName('li').length + 1;

    addPersonBtn.addEventListener('click', (e) => {
        // Pass a unique index to the embedded form creator
        // Number for inputs + 1 seems like a good idea
        let collectionContainerClass = e.target.dataset.collectionHolderClass;
        addFormToCollection(collectionContainerClass);
    });
}

function initRemoveOtherPersons() {
    let removeBtns = document.getElementsByClassName('remove-case-btn');

    if (removeBtns.length == 0) {
        return;
    }

    for (let btn of removeBtns) {
        btn.addEventListener('click', (e) => {
            let subForm = e.target.closest('.person-form.card.card-grid');
            removeFormFromCollection(subForm);
        })
    }
}

function addFormToCollection(collectionContainerClass) {
    let collectionContainer = document.getElementsByClassName(collectionContainerClass)[0];
    let prototype = collectionContainer.dataset.prototype;
    let index = parseInt(collectionContainer.dataset.index);
    let newForm = prototype;
    
    newForm = newForm.replace(/__name__/g, index);
    collectionContainer.dataset.index = index + 1;
    newFormContainer = document.createElement('div');
    newFormContainer.classList.add('person-form', 'card', 'card-grid');
    newFormContainer.insertAdjacentHTML('beforeend', newForm);

    collectionContainer.appendChild(newFormContainer);

    // Add even listener to remove btn
    let removeBtn = newFormContainer.querySelector('.remove-case-btn');
    removeBtn.addEventListener('click', (e) => {
        let subForm = e.target.closest('.person-form.card.card-grid');
        removeFormFromCollection(subForm);
    })
}

function removeFormFromCollection(subForm) {
    while (subForm.firstChild) {
        subForm.removeChild(subForm.lastChild);
    }
    subForm.remove();
}

// Watch the primary person input and fetch existing person that match input
function loadPrimaryPersons() {
    const primaryPersonInput = document.getElementById('case_primary_person_name');
    let timeout = null;

    primaryPersonInput.addEventListener('input', (e) => {
        let name = e.target.value;
        clearTimeout(timeout);

        if (name.length > 2) {
            timeout = setTimeout(() => {
                fetchPrimaryPersons(name)
                .then(data => {
                    console.log(data);
                });
            }, 600);
        }
    });
}

async function fetchPrimaryPersons(personName = '') {
    let url = Routing.generate('primary_persons', {name: personName});

    let params = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    }
    const response = await fetch(url, params);
    return response.json();
}
