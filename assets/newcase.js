document.addEventListener('DOMContentLoaded', function () {
    initAddOtherPersons();
    initRemoveOtherPersons();
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