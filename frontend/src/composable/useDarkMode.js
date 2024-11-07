import { ref, computed, onMounted } from 'vue';

export function useDarkMode() {
  const isDarkMode = ref(false);

  // Função para alternar o modo escuro
  const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    document.documentElement.classList.toggle('dark', isDarkMode.value);
    localStorage.setItem('theme', isDarkMode.value ? 'dark' : 'light');
  };

  // Detecta o tema atual com base nas preferências do usuário ou no localStorage
  onMounted(() => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
      isDarkMode.value = savedTheme === 'dark';
    } else {
      isDarkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
    document.documentElement.classList.toggle('dark', isDarkMode.value);
  });

  return {
    isDarkMode: computed(() => isDarkMode.value),
    toggleDarkMode,
  };
}