
const btnchangtheme = document.getElementById('btnchangtheme');

const updateTheme = (theme) => {
    document.querySelector('html').setAttribute('data-bs-theme', theme);
    btnchangtheme.innerHTML = theme === 'light'
        ? `<i class="bi bi-moon-fill"></i>`
        : `<i class="bi bi-brightness-high-fill"></i>`;
    btnchangtheme.className = `btn btn-sm btn-${theme === 'light' ? 'dark' : 'light'}`;
    localStorage.setItem('theme', theme);
}

document.addEventListener('DOMContentLoaded', () => {
    updateTheme(localStorage.getItem('theme') || 'light');
    btnchangtheme.addEventListener('click', () =>
        updateTheme(document.querySelector('html').getAttribute('data-bs-theme') === 'light' ? 'dark' : 'light')
    );
});
