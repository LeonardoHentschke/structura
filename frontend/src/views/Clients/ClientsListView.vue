<script setup>
import { onMounted, ref } from "vue";
import { useClientsStore } from "@/stores/clients";
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { DotsHorizontalIcon } from "@radix-icons/vue";

const clientsStore = useClientsStore();
const deleteClient = async () => {
  if (selectedClientId.value !== null) {
    const success = await clientsStore.deleteClient(selectedClientId.value); // Chame a função de exclusão usando o ID armazenado
    if (success) {
      isModalOpen.value = false;
      selectedClientId.value = null;
      router.push({ name: "clientList" });
    } else {
      console.error("Erro ao excluir cliente.");
    }
    isModalOpen.value = false;
  }
};
const isMenuOpen = ref({});
const hasProjects = ref(false);
// Função para alternar o estado do menu para um cliente específico
const toggleMenu = (id) => {
  isMenuOpen.value[id] = !isMenuOpen.value[id];
};
// Função para fechar o menu quando o mouse sai do botão de ação
const closeMenu = (id) => {
  isMenuOpen.value[id] = false;
};
const isModalOpen = ref(false);
// Função para verificar se o cliente possui projetos vinculados
const checkProjects = async (clientId) => {
  try {
    const response = await axios.get(`/api/projects/has`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}` // Insira o token do usuário autenticado
      },
      params: { client_id: clientId } // Passa o ID do cliente como parâmetro
    });

    hasProjects.value = response.data.has_projects;
  } catch (error) {
    console.error('Erro ao verificar projetos vinculados:', error.response?.data || error.message);
    hasProjects.value = false; // Define como falso caso ocorra um erro
  }
};
const selectedClientId = ref(null);
const openDeleteModal = async (clientId) => {
  selectedClientId.value = clientId;
  await checkProjects(clientId); // Aguarde a verificação de projetos vinculados
  isModalOpen.value = true; // Abre o modal após verificar
};

onMounted(clientsStore.getAllClients);
</script>

<template>
  <div class="hidden h-full flex-1 flex-col space-y-8 p-8 md:flex">
    <div class="flex items-center justify-between space-y-2">
      <div>
        <h2 class="text-3xl font-extrabold tracking-tighter text-primary dark:text-yellow-500">Sua lista de clientes</h2>
        <p class="text-muted-foreground text-sm tracking-wide dark:text-gray-300">Gerencie seus clientes cadastrados facilmente</p>
      </div>
      <div class="flex items-center space-x-2">
        <RouterLink :to="{ name: 'client' }">
          <Button class="bg-primary text-primary-foreground dark:bg-yellow-500 dark:text-gray-800 font-bold transition-colors duration-300 shadow-md hover:shadow-lg">
            Adicionar novo cliente
          </Button>
        </RouterLink>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-card dark:bg-gray-800 p-6 rounded-lg shadow-xl">
      <Table class="w-full text-left bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700">
        <TableCaption v-if="!clientsStore.clients.length" class="text-muted-foreground dark:text-gray-400">Nenhum cliente cadastrado ainda.</TableCaption>
        <TableHeader class="bg-primary dark:bg-yellow-500 text-primary-foreground dark:text-gray-800">
          <TableRow class="border-b border-muted dark:border-gray-700">
            <TableHead class="py-3 px-4 w-20 text-center dark:text-gray-100">ID</TableHead>
            <TableHead class="py-3 px-4 dark:text-gray-100">Nome</TableHead>
            <TableHead class="py-3 px-4 dark:text-gray-100">Email</TableHead>
            <TableHead class="py-3 px-4 w-40 dark:text-gray-100">Telefone</TableHead>
            <TableHead class="py-3 px-4 w-40 dark:text-gray-100">CPF</TableHead>
            <TableHead class="py-3 px-4 text-center w-60 dark:text-gray-100">Ações</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="client in clientsStore.clients" :key="client.id" class="hover:bg-accent/30 dark:hover:bg-gray-700 transition-colors duration-200">
            <TableCell class="py-4 px-4 text-center dark:text-gray-300">{{ client.id }}</TableCell>
            <TableCell class="font-medium py-4 px-4 dark:text-gray-100">{{ client.name }}</TableCell>
            <TableCell class="py-4 px-4 dark:text-gray-300">{{ client.email }}</TableCell>
            <TableCell class="py-4 px-4 dark:text-gray-300">{{ client.phone }}</TableCell>
            <TableCell class="py-4 px-4 dark:text-gray-300">{{ client.cpf }}</TableCell>
            <TableCell class="flex justify-center items-center relative" @mouseleave="closeMenu(client.id)">
              <!-- Botão de Menu (Três Pontos) -->
              <template v-if="!isMenuOpen[client.id]">
                <Button
                  @click="toggleMenu(client.id)"
                  variant="ghost"
                  class="flex h-8 w-8 p-0 text-muted-foreground hover:bg-primary hover:bg-opacity-20 rounded-full transition duration-300 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 shadow-lg"
                >
                  <DotsHorizontalIcon class="h-4 w-4" />
                  <span class="sr-only">Abrir menu</span>
                </Button>
              </template>

              <!-- Botões de Ação (Editar e Excluir) -->
              <template v-else>
                <div class="flex space-x-3 items-center">
                  <!-- Botão Editar -->
                  <RouterLink :to="{ name: 'client', params: { id: client.id } }" class="editBtn">
                      <svg height="0.8em" width="0.8em" viewBox="0 0 512 512">
                        <path
                          d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"
                        ></path>
                      </svg>
                  </RouterLink>

                  <!-- Botão Excluir -->
                  <button @click="openDeleteModal(client.id)" class="group relative flex h-10 w-10 flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-red-800 bg-red-400 hover:bg-red-600"
                    title="Excluir">
                      <svg viewBox="0 0 1.625 1.625" class="absolute -top-5 fill-white delay-100 group-hover:top-3 group-hover:animate-[spin_1.4s] group-hover:duration-1000" height="10" width="10">
                        <path d="M.471 1.024v-.52a.1.1 0 0 0-.098.098v.618c0 .054.044.098.098.098h.487a.1.1 0 0 0 .098-.099h-.39c-.107 0-.195 0-.195-.195"></path>
                        <path d="M1.219.601h-.163A.1.1 0 0 1 .959.504V.341A.033.033 0 0 0 .926.309h-.26a.1.1 0 0 0-.098.098v.618c0 .054.044.098.098.098h.487a.1.1 0 0 0 .098-.099v-.39a.033.033 0 0 0-.032-.033"></path>
                        <path d="m1.245.465-.15-.15a.02.02 0 0 0-.016-.006.023.023 0 0 0-.023.022v.108c0 .036.029.065.065.065h.107a.023.023 0 0 0 .023-.023.02.02 0 0 0-.007-.016"></path>
                      </svg>
                      <svg width="10" fill="none" viewBox="0 0 39 7" class="origin-right duration-500 group-hover:rotate-90">
                        <line stroke-width="4" stroke="white" y2="5" x2="39" y1="5"></line>
                        <line stroke-width="3" stroke="white" y2="1.5" x2="26.0357" y1="1.5" x1="12"></line>
                      </svg>
                      <svg width="10" fill="none" viewBox="0 0 33 39" class="">
                        <mask fill="white" id="path-1-inside-1_8_19">
                          <path d="M0 0H33V35C33 37.2091 31.2091 39 29 39H4C1.79086 39 0 37.2091 0 35V0Z"></path>
                        </mask>
                        <path mask="url(#path-1-inside-1_8_19)" fill="white" d="M0 0H33H0ZM37 35C37 39.4183 33.4183 43 29 43H4C-0.418278 43 -4 39.4183 -4 35H4H29H37ZM4 43C-0.418278 43 -4 39.4183 -4 35V0H4V35V43ZM37 0V35C37 39.4183 33.4183 43 29 43V35V0H37Z" ></path>
                        <path stroke-width="4" stroke="white" d="M12 6L12 29"></path>
                        <path stroke-width="4" stroke="white" d="M21 6V29"></path>
                      </svg>
                  </button>
                </div>
              </template>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
    <!-- Modal de Confirmação para Exclusão -->
    <div v-if="isModalOpen" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
      <div class="max-w-xl w-full mx-auto bg-gray-800 rounded-xl overflow-hidden">
        <div class="max-w-sm mx-auto pt-12 pb-8 px-5 text-center">
          <div class="inline-flex items-center justify-center w-12 h-12 mb-5 bg-gray-600 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 21" height="21" width="24">
              <path fill="#FF4842" d="M12.0005 15C11.8027 15 11.6093 15.0587 11.4449 15.1686C11.2804 15.2784 11.1523 15.4346 11.0766 15.6173C11.0009 15.8001 10.9811 16.0011 11.0197 16.1951C11.0583 16.3891 11.1535 16.5673 11.2933 16.7071C11.4332 16.847 11.6114 16.9422 11.8054 16.9808C11.9993 17.0194 12.2004 16.9996 12.3831 16.9239C12.5659 16.8482 12.722 16.72 12.8319 16.5556C12.9418 16.3911 13.0005 16.1978 13.0005 16C13.0005 15.7348 12.8951 15.4805 12.7076 15.2929C12.52 15.1054 12.2657 15 12.0005 15ZM22.6705 16.47L14.6205 2.47003C14.3603 2.00354 13.9802 1.61498 13.5196 1.3445C13.0591 1.07401 12.5346 0.931396 12.0005 0.931396C11.4663 0.931396 10.9419 1.07401 10.4813 1.3445C10.0207 1.61498 9.64065 2.00354 9.38046 2.47003L1.38046 16.47C1.11125 16.924 0.966598 17.441 0.9611 17.9688C0.955602 18.4966 1.08945 19.0166 1.34914 19.4761C1.60883 19.9356 1.98516 20.3185 2.44014 20.5861C2.89512 20.8536 3.41264 20.9964 3.94046 21H20.0605C20.5925 21.0053 21.1164 20.8689 21.5784 20.6049C22.0403 20.341 22.4238 19.9589 22.6894 19.4978C22.9551 19.0368 23.0933 18.5134 23.09 17.9814C23.0866 17.4493 22.9418 16.9277 22.6705 16.47ZM20.9405 18.47C20.8528 18.626 20.7249 18.7556 20.5701 18.8453C20.4154 18.935 20.2393 18.9815 20.0605 18.98H3.94046C3.76157 18.9815 3.58556 18.935 3.43077 18.8453C3.27599 18.7556 3.14811 18.626 3.06046 18.47C2.97269 18.318 2.92648 18.1456 2.92648 17.97C2.92648 17.7945 2.97269 17.622 3.06046 17.47L11.0605 3.47003C11.1444 3.30623 11.2719 3.16876 11.4289 3.07277C11.5859 2.97678 11.7664 2.92599 11.9505 2.92599C12.1345 2.92599 12.315 2.97678 12.472 3.07277C12.629 3.16876 12.7565 3.30623 12.8405 3.47003L20.8905 17.47C20.9897 17.6199 21.0467 17.7937 21.0555 17.9732C21.0643 18.1527 21.0245 18.3312 20.9405 18.49V18.47ZM12.0005 7.00003C11.7352 7.00003 11.4809 7.10538 11.2933 7.29292C11.1058 7.48046 11.0005 7.73481 11.0005 8.00003V12C11.0005 12.2652 11.1058 12.5196 11.2933 12.7071C11.4809 12.8947 11.7352 13 12.0005 13C12.2657 13 12.52 12.8947 12.7076 12.7071C12.8951 12.5196 13.0005 12.2652 13.0005 12V8.00003C13.0005 7.73481 12.8951 7.48046 12.7076 7.29292C12.52 7.10538 12.2657 7.00003 12.0005 7.00003Z" ></path>
            </svg>
          </div>
          <h4 class="text-xl text-gray-100 font-semibold mb-5">
            {{ hasProjects ? 'Desativar conta' : 'Desativar/Excluir conta' }}
          </h4>
          <p class="text-gray-300 font-medium">
            Tem certeza? {{ hasProjects ? 'Deseja apenas desativar o cliente, pois há projetos vinculados.' : 'Você pode desativar ou excluir permanentemente.' }}
          </p>
        </div>
        <div class="flex justify-center pt-5 pb-6 px-6 text-right bg-gray-900 -mb-2">
          <button @click="deactivateClient" class="inline-block w-full sm:w-auto py-3 px-5 mb-2 mr-4 text-center font-semibold leading-6 text-gray-200 bg-gray-500 hover:bg-gray-400 rounded-lg transition duration-200">
            Desativar
          </button>
          <button v-if="!hasProjects" @click="deleteClient" class="inline-block w-full sm:w-auto py-3 px-5 mb-2 text-center font-semibold leading-6 text-blue-50 bg-red-500 hover:bg-red-600 rounded-lg transition duration-200">
            Deletar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
  @keyframes fadeInScale {
    0% {
      opacity: 0;
      transform: scale(0.95);
    }
    100% {
      opacity: 1;
      transform: scale(1);
    }
  }
  .editBtn {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    border: none;
    background-color: rgb(93, 93, 116);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.123);
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.3s;
  }
  .editBtn::before {
    content: "";
    width: 200%;
    height: 200%;
    background-color: rgb(102, 102, 141);
    position: absolute;
    z-index: 1;
    transform: scale(0);
    transition: all 0.3s;
    border-radius: 50%;
    filter: blur(10px);
  }
  .editBtn:hover::before {
    transform: scale(1);
  }
  .editBtn:hover {
    box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.336);
  }

  .editBtn svg {
    height: 17px;
    fill: white;
    z-index: 3;
    transition: all 0.2s;
    transform-origin: bottom;
  }
  .editBtn:hover svg {
    transform: rotate(-15deg) translateX(5px);
  }
  .editBtn::after {
    content: "";
    width: 25px;
    height: 1.5px;
    position: absolute;
    bottom: 12px;
    left: -5px;
    background-color: white;
    border-radius: 2px;
    z-index: 2;
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.5s ease-out;
  }
  .editBtn:hover::after {
    transform: scaleX(1);
    left: -5px;
    transform-origin: right;
  }
</style>