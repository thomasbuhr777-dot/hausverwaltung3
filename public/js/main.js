document.addEventListener('DOMContentLoaded', () => {
    initThemeToggle();
});

function initThemeToggle() {
    const root = document.documentElement;
    const toggleButton = document.getElementById('themeToggle');
    const themeItems = document.querySelectorAll('[data-theme]');

    if (!toggleButton || !themeItems.length) {
        return;
    }

    const STORAGE_KEY = 'app-theme';
    const DEFAULT_THEME = 'light';
    const VALID_THEMES = ['light', 'dark'];

    function getStoredTheme() {
        const stored = localStorage.getItem(STORAGE_KEY);
        return VALID_THEMES.includes(stored) ? stored : null;
    }

    function getPreferredTheme() {
        const stored = getStoredTheme();
        if (stored) {
            return stored;
        }

        return window.matchMedia('(prefers-color-scheme: dark)').matches
            ? 'dark'
            : DEFAULT_THEME;
    }

    function getThemeIcon(theme) {
        return theme === 'dark'
            ? 'bi bi-moon-stars-fill'
            : 'bi bi-sun-fill';
    }

    function updateToggleIcon(theme) {
        const icon = toggleButton.querySelector('i');
        if (!icon) {
            return;
        }

        icon.className = getThemeIcon(theme);
    }

    function updateActiveMenu(theme) {
        themeItems.forEach((item) => {
            const isActive = item.dataset.theme === theme;
            item.classList.toggle('active', isActive);
            item.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    }

    function applyTheme(theme, persist = true) {
        const safeTheme = VALID_THEMES.includes(theme) ? theme : DEFAULT_THEME;

        root.setAttribute('data-bs-theme', safeTheme);
        updateToggleIcon(safeTheme);
        updateActiveMenu(safeTheme);

        if (persist) {
            localStorage.setItem(STORAGE_KEY, safeTheme);
        }
    }

    // Initial setzen
    applyTheme(getPreferredTheme(), false);

    // Klicks auf Dropdown-Einträge
    themeItems.forEach((item) => {
        item.addEventListener('click', (event) => {
            event.preventDefault();

            const selectedTheme = item.dataset.theme;
            applyTheme(selectedTheme, true);
        });
    });

    // Reaktion auf Systemwechsel nur dann, wenn nichts gespeichert wurde
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    const handleSystemThemeChange = (event) => {
        if (getStoredTheme() !== null) {
            return;
        }

        applyTheme(event.matches ? 'dark' : 'light', false);
    };

    if (typeof mediaQuery.addEventListener === 'function') {
        mediaQuery.addEventListener('change', handleSystemThemeChange);
    } else if (typeof mediaQuery.addListener === 'function') {
        mediaQuery.addListener(handleSystemThemeChange);
    }
}