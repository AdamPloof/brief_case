document.addEventListener('DOMContentLoaded', function () {
    initAddOtherPersons();
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

function addFormToCollection(collectionContainerClass) {
    let collectionContainer = document.getElementsByClassName(collectionContainerClass)[0];
    let prototype = collectionContainer.dataset.prototype;
    let index = collectionContainer.dataset.index;
    let newForm = prototype;
    
    newForm = newForm.replace(/__name__/g, index);
    collectionContainer.dataset.index = index + 1;
    newFormLi = document.createElement('li');
    newFormLi.insertAdjacentHTML('beforeend', newForm);
    collectionContainer.appendChild(newFormLi);
}