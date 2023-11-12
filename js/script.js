"use strict";

const toggleBtn = document.querySelector('.toggle-menu');
const hamburgerBtn = document.querySelector('.hamburger-btn');
const spMenu = document.querySelector('.sp-menu');
const closeBtn = document.querySelector('.close-btn');

toggleBtn.addEventListener('click', () => {
    spMenu.classList.add('active');
})
hamburgerBtn.addEventListener('click', () => {
    spMenu.classList.add('active');
})

closeBtn.addEventListener('click', () => {
    spMenu.classList.remove('active');
})