// Twig Routing
import Routing from '../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
const routes = require('../public/js/fos_js_routes.json');
Routing.setRoutingData(routes);

import $ from 'jquery';
import React from 'react';
import ReactDOM from 'react-dom';
import CaseSearch from './CaseSearch';

document.addEventListener('DOMContentLoaded', function () {
    initAddOtherPersons();
    initAddRelatedCases();
    initRelatedCaseInputs();
    initRemoveEmbedForms();
    loadPrimaryPersons();
    loadRelatedCases();
});

function initAddOtherPersons() {
    // Add event listener to add persons button to add new embedded person forms in new case view
    const addPersonBtn = document.getElementById("addPersonBtn");

    let assocPersonsContainer = document.getElementById("assocPersonsContainer");
    assocPersonsContainer.dataset.index = assocPersonsContainer.querySelectorAll('.embed-person-form,.card,.card-grid').length;

    addPersonBtn.addEventListener('click', (e) => {
        // Pass a unique index to the embedded form creator
        // Number for inputs + 1 seems like a good idea
        let collectionContainerClass = e.target.dataset.collectionHolderClass;
        addFormToCollection(collectionContainerClass, 'embed-person-form');
    });
}

// TODO: Make this work for both other persons and related cases
function initRemoveEmbedForms() {
    let removeBtns = document.getElementsByClassName('remove-case-btn');

    if (removeBtns.length == 0) {
        return;
    }

    for (let btn of removeBtns) {
        btn.addEventListener('click', (e) => {
            // Remove btn could refer belong to an associated person form or a related case form
            // Check if it belongs to an associated person first and if not, assume it's a related case
            let subForm = e.target.closest('.embed-person-form.card.card-grid');
            if (!subForm) {
                subForm = e.target.closest('.embed-case-form.card.card-grid');
            }
            removeFormFromCollection(subForm);
        })
    }
}

function addFormToCollection(collectionContainerClass, formClass) {
    let collectionContainer = document.getElementsByClassName(collectionContainerClass)[0];
    let newForm = collectionContainer.dataset.prototype;
    let index = parseInt(collectionContainer.dataset.index);
    
    newForm = newForm.replace(/__name__/g, index);
    collectionContainer.dataset.index = index + 1;
    let newFormContainer = document.createElement('div');
    newFormContainer.classList.add(formClass, 'card', 'card-grid');
    newFormContainer.insertAdjacentHTML('beforeend', newForm);

    collectionContainer.appendChild(newFormContainer);

    // Add case search modal trigger to input on embedded case forms
    if (formClass == 'embed-case-form') {
        setCaseSearchModalListener(newFormContainer.getElementsByTagName('input')[0]);
    }

    // Add even listener to remove btn
    let removeBtn = newFormContainer.querySelector('.remove-case-btn');
    removeBtn.addEventListener('click', (e) => {
        let subForm = e.target.closest('.' + formClass + '.card.card-grid');
        removeFormFromCollection(subForm);
    })
}

function removeFormFromCollection(subForm) {
    while (subForm.firstChild) {
        subForm.removeChild(subForm.lastChild);
    }
    subForm.remove();
}

// *** Related Cases Embed Form ***
function initAddRelatedCases() {
    // Add event listener to add cases button to add new embedded cases forms in new case view
    const addCaseBtn = document.getElementById("addCaseBtn");

    let relatedCasesContainer = document.getElementById("relatedCasesContainer");
    relatedCasesContainer.dataset.index = relatedCasesContainer.querySelectorAll('.embed-case-form,.card,.card-grid').length;

    addCaseBtn.addEventListener('click', (e) => {
        // Pass a unique index to the embedded form creator
        let collectionContainerClass = e.target.dataset.collectionHolderClass;
        addFormToCollection(collectionContainerClass, 'embed-case-form');
    });
}

// Add event listeners to existing related case inputs to trigger case search modal
function initRelatedCaseInputs() {
    const embedCaseForms = document.getElementsByClassName('embed-case-form');
    for (let caseForm of embedCaseForms) {
        let caseInput = caseForm.getElementsByTagName('input')[0];
        setCaseSearchModalListener(caseInput);
    }
}

// Init event listeners for related case inputs
function setCaseSearchModalListener(trigEl) {
    trigEl.addEventListener('click', (e) => {
        let inputId = e.target.id;
        const searchModal = $('#caseSearchModal');
        searchModal.attr('data-selected', inputId);
        searchModal.modal('show');
    });
}

function loadRelatedCases() {
    const collectionContainerClass = 'related-cases';
    const caseId = getCaseId();

    if (!caseId) {
        return;
    }

    const cases = fetchRelatedCases(caseId);
    
    cases.then((data) => {
        for (let caseFile of data) {
            addFormToCollection(collectionContainerClass, 'embed-case-form');
            let caseForms = document.getElementsByClassName('embed-case-form');
            let caseForm = caseForms[caseForms.length - 1];
            let inputs = caseForm.querySelectorAll('input');
            inputs[0].value = caseFile.description;
            inputs[1].value = caseFile.id;
        }
    });
}

function getCaseId() {
    let path = window.location.pathname;
    let parts = path.split('/');
    let id = parts[parts.length - 1];

    if (id == 'newcase') {
        return false;
    }

    if (id.includes('?')) {
        id = id.split('?')[0];
    }

    return id;
}

async function fetchRelatedCases(caseId) {
    // let url = Routing.generate('fetchrelated', {id: caseId});
    let url = '/fetchrelated/' + caseId;

    let params = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    }
    const response = await fetch(url, params);
    return response.json();
}

// Watch the primary person input and fetch existing person that match input
function loadPrimaryPersons() {
    const primaryPersonInput = document.getElementById('case_primary_person_name');
    primaryPersonInput.parentElement.classList.add('autocomplete');

    let timeout = null;

    primaryPersonInput.addEventListener('input', (e) => {
        let name = e.target.value;
        clearTimeout(timeout);

        if (name.length > 2) {
            timeout = setTimeout(() => {
                fetchPrimaryPersons(name)
                .then(data => {
                    autocompletePersonInput(primaryPersonInput, data);
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

// TODO: Need to resize window/content container height when autocomplete list exceeds window height. -- Important! --
function autocompletePersonInput(inp, personsData) {
    let currentFocus;
    let names = [];

    const roleInput = document.getElementById('case_primary_person_role');
    const traitsInput = document.getElementById('case_primary_person_traits');

    for (let personObj of personsData) {
        names.push(personObj.name);
    }

    let typedVal = inp.value;
    let autoList = document.createElement('div');
    
    closeAllLists();
    if (!typedVal) {
        return false;
    }
    
    currentFocus = -1;

    autoList.setAttribute('id', inp.id + 'autocomplete-list');
    autoList.classList.add('autocomplete-items');
    inp.parentNode.appendChild(autoList);

    for (let name of names) {
        // TODO: rather than using innerHTML, maybe I should create and append new elements/nodes
        let listItem = document.createElement('div');
        listItem.innerHTML = "<strong>" + name.substr(0, typedVal.length) + "</strong>"; // Making matching text bold
        listItem.innerHTML += name.substr(typedVal.length);

        // insert an input field to hold the names item's value
        listItem.innerHTML += "<input type='hidden' value='" + name + "'>";

        listItem.addEventListener('click', function(e) {
            let selectedName = this.getElementsByTagName('input')[0].value;
            let selectedPerson = personsData.find((el) => el.name == selectedName);
            inp.value = selectedName;
            roleInput.value = selectedPerson.role;
            traitsInput.value = transformTraitsToStr(selectedPerson.traits);

            closeAllLists();
        });

        autoList.appendChild(listItem);
    }


    // Track key up/down/enter presses on the keyboard for navigating through autocomplete list
    inp.addEventListener('keydown', function(e) {
        let autoList = document.getElementById(this.id + 'autocomplete-list');
        if (autoList) {
            // Autolist should be the list of all persons instead of their container
            autoList = autoList.getElementsByTagName('div');
        }

        if (e.keyCode == 40) {
            // Down arrow
            currentFocus++;
            addActive(autoList);
        } else if (e.keyCode == 38) {
            // Up arrow
            currentFocus--;
            addActive(autoList);
        } else if (e.keyCode == 13) {
            // Enter key
            e.preventDefault();
            if (currentFocus > -1) {
                if (autoList) {
                    autoList[currentFocus].click();
                }
            }
        }
    });

    function addActive(autoList) {
        if (!autoList) {
            return false;
        }

        // First remove all active persons
        removeActive(autoList);

        if (currentFocus >= autoList.length) {
            currentFocus = 0;
        } else if (currentFocus < 0) {
            // Wrapping around when arrow up below first list item
            currentFocus = autoList.length -1;
        }

        autoList[currentFocus].classList.add('autocomplete-active');
    }

    function removeActive(autoList) {
        for (let person of autoList) {
            person.classList.remove('autocomplete-active');
        }
    }

    /* close all autocomplete lists in the document,
    except the one passed as an argument: */ 
    function closeAllLists(elem) {
        let personsList = document.getElementsByClassName('autocomplete-items');

        for (let person of personsList) {
            if (elem != person && elem != inp) {
                person.parentNode.removeChild(person);
            }
        }
    }

    document.addEventListener('click', (e) => {
        closeAllLists(e.target);
    })
}

function transformTraitsToStr(traits) {
    let traitsStr = '';

    if (traits) {
        for (let key in traits) {
            traitsStr += `${key}:${traits[key]},`;
        }

        // Removing the comma from the last trait
        traitsStr = traitsStr.substr(0, traitsStr.length - 1);
    }

    return traitsStr;
}

// Load CaseSearch Modal (React component)
ReactDOM.render(<CaseSearch />, document.getElementById('case-search-container'));
