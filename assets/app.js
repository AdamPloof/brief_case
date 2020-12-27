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
