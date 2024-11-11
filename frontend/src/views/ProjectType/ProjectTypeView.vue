<script setup>
import { onMounted, reactive, ref, computed } from "vue";
import { useProjectTypesStore } from "@/stores/projectType";
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dialogState } from "@/composable/dialog";
import { DotsHorizontalIcon } from "@radix-icons/vue";

const projectTypesStore = useProjectTypesStore();
const [isOpen, closeDialog] = dialogState();

const formData = reactive({
  id: null,
  name: "",
  description: ""
});

const isSubmitting = ref(false);
const formErrors = ref({});
const isEditMode = ref(false);
const isMenuOpen = ref({});

const projectTypes = computed(() => projectTypesStore.types);

const resetForm = () => {
  formData.id = null;
  formData.name = "";
  formData.description = "";
  formErrors.value = {};
  isEditMode.value = false;
};

const validateForm = () => {
  formErrors.value = {};
  if (!formData.name) formErrors.value.name = "Nome é obrigatório.";
  if (!formData.description) formErrors.value.description = "Descrição é obrigatória.";
  return Object.keys(formErrors.value).length === 0;
};

const submitForm = async () => {
  if (!validateForm()) return;
  isSubmitting.value = true;

  if (isEditMode.value) {
    await projectTypesStore.updateProjectType(formData.id, formData);
  } else {
    await projectTypesStore.createProjectType(formData);
  }

  projectTypesStore.getAllProjectTypes();
  closeDialog();
  resetForm();
  isSubmitting.value = false;
};

const editProjectType = (type) => {
  formData.id = type.id;
  formData.name = type.name;
  formData.description = type.description;
  isEditMode.value = true;
  isOpen.value = true;
};

const deleteProjectType = (id) => {
  if (confirm("Tem certeza de que deseja excluir este tipo de projeto?")) {
    projectTypesStore.deleteProjectType(id);
  }
};

const toggleMenu = (id) => {
  isMenuOpen.value[id] = !isMenuOpen.value[id];
};

const closeMenu = (id) => {
  isMenuOpen.value[id] = false;
};

onMounted(() => {
  projectTypesStore.getAllProjectTypes();
});
</script>

<template>
  <div class="hidden h-full flex-1 flex-col space-y-8 p-8 md:flex dark:bg-gray-900">
    <div class="flex items-center justify-between space-y-2">
      <div>
        <h2 class="text-3xl font-extrabold tracking-tighter text-primary dark:text-yellow-500">Seus tipos de projeto</h2>
        <p class="text-muted-foreground text-sm tracking-wide dark:text-gray-300">Aqui estão seus tipos de projetos personalizados</p>
      </div>
      <div class="flex items-center space-x-2">
        <Dialog v-model:open="isOpen">
          <DialogTrigger as-child>
            <Button @click="resetForm" class="bg-primary text-primary-foreground dark:bg-yellow-500 dark:text-gray-800 font-bold transition-colors duration-300 shadow-md hover:shadow-lg">Adicionar novo tipo</Button>
          </DialogTrigger>
          <DialogContent class="sm:max-w-[425px]">
            <form @submit.prevent="submitForm">
              <DialogHeader>
                <DialogTitle>{{ isEditMode ? 'Editar Tipo de Projeto' : 'Novo Tipo de Projeto' }}</DialogTitle>
                <DialogDescription>{{ isEditMode ? 'Edite os detalhes do tipo de projeto' : 'Crie um novo tipo de projeto personalizado' }}</DialogDescription>
              </DialogHeader>
              <div class="grid gap-4 py-4">
                <div class="grid w-full max-w-sm items-center gap-1.5">
                  <Label for="name">Nome</Label>
                  <Input id="name" placeholder="Ex: Projeto..." v-model="formData.name" />
                  <p v-if="formErrors.name" class="text-red-600">{{ formErrors.name }}</p>
                </div>
                <div class="grid w-full max-w-sm items-center gap-1.5">
                  <Label for="description">Descrição</Label>
                  <Input id="description" placeholder="Forneça uma descrição do tipo" v-model="formData.description" />
                  <p v-if="formErrors.description" class="text-red-600">{{ formErrors.description }}</p>
                </div>
              </div>
              <DialogFooter>
                <Button type="submit" :disabled="isSubmitting">{{ isEditMode ? 'Salvar Alterações' : 'Salvar' }}</Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-card dark:bg-gray-800 p-6 rounded-lg shadow-xl">
      <Table class="w-full text-left bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700">
        <TableCaption v-if="!projectTypes.length" class="text-muted-foreground dark:text-gray-400">Nenhum tipo de projeto cadastrado ainda.</TableCaption>
        <TableHeader class="bg-primary dark:bg-yellow-500 text-primary-foreground dark:text-gray-800">
          <TableRow class="border-b border-muted dark:border-gray-700">
            <TableHead class="py-3 px-4 w-20 text-center dark:text-gray-100">ID</TableHead>
            <TableHead class="py-3 px-4 dark:text-gray-100">Nome</TableHead>
            <TableHead class="py-3 px-4 dark:text-gray-100">Descrição</TableHead>
            <TableHead class="py-3 px-4 text-center w-60 dark:text-gray-100">Ações</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="type in projectTypes" :key="type.id" class="hover:bg-accent/30 dark:hover:bg-gray-700 transition-colors duration-200">
            <TableCell class="py-4 px-4 text-center dark:text-gray-300">{{ type.id }}</TableCell>
            <TableCell class="font-medium py-4 px-4 dark:text-gray-100">{{ type.name }}</TableCell>
            <TableCell class="py-4 px-4 dark:text-gray-300">{{ type.description }}</TableCell>
            <TableCell class="flex justify-center items-center relative" @mouseleave="closeMenu(type.id)">
              <!-- Botão de Menu (Três Pontos) -->
              <template v-if="!isMenuOpen[type.id]">
                <Button @click="toggleMenu(type.id)" variant="ghost" class="flex h-8 w-8 p-0 text-muted-foreground hover:bg-primary hover:bg-opacity-20 rounded-full transition duration-300 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 shadow-lg">
                  <DotsHorizontalIcon class="h-4 w-4" />
                  <span class="sr-only">Abrir menu</span>
                </Button>
              </template>

              <!-- Botões de Ação (Editar e Excluir) -->
              <template v-else>
                <div class="flex space-x-3 items-center">
                  <Button @click="editProjectType(type)" class="editBtn">
                    <svg height="0.8em" width="0.8em" viewBox="0 0 512 512">
                      <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                    </svg>
                  </Button>
                  <!-- Botão Excluir -->
                  <Button @click="deleteProjectType(type.id)" class="group relative flex h-10 w-10 flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-red-800 bg-red-400 hover:bg-red-600"
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
                  </Button>
                </div>
              </template>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
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
