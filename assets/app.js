// JS
import $ from 'jquery';
import flatpickr from 'flatpickr';

// CSS
import './styles/app.scss';
require("flatpickr/dist/themes/dark.css");

require('bootstrap');

$(document).ready(function() {
    initDatePickr();
    checkSidebarHeight();
    initFileUploadInput();
});

function initDatePickr() {
    const dateInput = document.getElementById('case_date');
    if (!dateInput) {
        return;
    }

    flatpickr(dateInput, {
        enableTime: true,
        altInput: true,
        altFormat: 'F j, Y h:i K',
        dateFormat: 'Y-m-d H:i:s',
    });
}

function checkSidebarHeight() {
    const docHeight = document.body.scrollHeight;
    const windowHeight = window.innerHeight;
    let sidebar = document.getElementsByClassName('sidebar')[0];

    if (docHeight > windowHeight) {
        sidebar.style.height = 'auto';
    }
}

function initFileUploadInput() {
    const fileInputs = document.getElementsByClassName('custom-file-input');

    if (fileInputs.length > 0) {
        for (let input of fileInputs) {
            input.addEventListener('change', (e) => {
                let inputFile = e.target;
                let inputLabel = inputFile.parentElement.querySelector('.custom-file-label');
                inputLabel.textContent = inputFile.files[0].name;
            });
        }
    }
}
