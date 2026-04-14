export function initTheme() {
   const STORAGE_KEY = "appTheme";
   const html = document.documentElement;

   const applyTheme = theme => {
       localStorage.setItem(STORAGE_KEY, theme);
       html.setAttribute(
           "data-bs-theme",
           theme === "auto"
               ? (window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light")
               : theme
       );
   };

   applyTheme(localStorage.getItem(STORAGE_KEY) || "auto");
}