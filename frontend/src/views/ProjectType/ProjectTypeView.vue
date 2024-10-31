<script setup>
import { onMounted, reactive, ref, computed } from "vue";
import { useProjectTypesStore } from "@/stores/projectType";
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dialogState } from "@/composable/dialog";

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

const editClient = (type) => {
  formData.id = type.id;
  formData.name = type.name;
  formData.description = type.description;
  isEditMode.value = true;
  isOpen.value = true;
};

const deleteClient = (id) => {
  if (confirm("Tem certeza de que deseja excluir este tipo de projeto?")) {
    projectTypesStore.deleteProjectType(id);
  }
};

onMounted(projectTypesStore.getAllProjectTypes);

const projectTypes = computed(() => projectTypesStore.types);
</script>

<template>
  <div class="hidden h-full flex-1 flex-col space-y-8 p-8 md:flex">
    <div class="flex items-center justify-between space-y-2">
      <div>
        <h2 class="text-2xl font-bold tracking-tight">Seus tipos de projeto!</h2>
        <p class="text-muted-foreground">Aqui estão seus tipos de projetos personalizados!</p>
      </div>
      <div class="flex items-center space-x-2">
        <Dialog v-model:open="isOpen">
          <DialogTrigger as-child>
            <Button variant="outline" @click="resetForm">Adicionar novo tipo</Button>
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

    <Table>
      <TableCaption v-if="!projectTypes.length">Nenhum tipo de projeto cadastrado ainda.</TableCaption>
      <TableHeader>
        <TableRow>
          <TableHead>ID</TableHead>
          <TableHead>Nome</TableHead>
          <TableHead>Descrição</TableHead>
          <TableHead>Ações</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="type in projectTypes" :key="type.id">
          <TableCell>{{ type.id }}</TableCell>
          <TableCell class="font-medium">{{ type.name }}</TableCell>
          <TableCell class="font-medium">{{ type.description }}</TableCell>
          <TableCell>
            <Button variant="outline" @click="editClient(type)">Editar</Button>
            <Button variant="destructive" @click="deleteClient(type.id)">Excluir</Button>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </div>
</template>
