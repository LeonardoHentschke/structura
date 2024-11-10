function setThemeOnLoad() {
    // Detecta o tema do localStorage ou aplica a preferência do navegador
    if (
      localStorage.theme === "dark" ||
      (!("theme" in localStorage) &&
        window.matchMedia("(prefers-color-scheme: dark)").matches)
    ) {
      document.documentElement.classList.add("dark");
    } else {
      document.documentElement.classList.remove("dark");
    }
  }
  
  function switchTheme() {
    // Alterna o tema entre claro e escuro
    if (
      localStorage.theme === "dark" ||
      (!("theme" in localStorage) &&
        window.matchMedia("(prefers-color-scheme: dark)").matches)
    ) {
      document.documentElement.classList.remove("dark");
      localStorage.theme = "light";
    } else {
      document.documentElement.classList.add("dark");
      localStorage.theme = "dark";
    }
  }
  
  function getCurrentTheme() {
    // Retorna o tema atual (true para dark, false para light)
    return document.documentElement.classList.contains("dark");
  }
  
  export { setThemeOnLoad, switchTheme, getCurrentTheme };
  