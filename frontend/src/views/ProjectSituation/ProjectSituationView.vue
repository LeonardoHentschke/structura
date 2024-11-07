<script setup>
import { onMounted, reactive, ref, computed } from "vue";
import { useProjectSituationsStore } from "@/stores/projectSituation";
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dialogState } from "@/composable/dialog";

const projectSituationsStore = useProjectSituationsStore();
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
    await projectSituationsStore.updateProjectSituation(formData.id, formData);
  } else {
    await projectSituationsStore.createProjectSituation(formData);
  }

  projectSituationsStore.getAllProjectSituations();
  closeDialog();
  resetForm();
  isSubmitting.value = false;
};

const editClient = (situation) => {
  formData.id = situation.id;
  formData.name = situation.name;
  formData.description = situation.description;
  isEditMode.value = true;
  isOpen.value = true;
};

const deleteClient = (id) => {
  if (confirm("Tem certeza de que deseja excluir esta situação de projeto?")) {
    projectSituationsStore.deleteProjectSituation(id);
  }
};

onMounted(projectSituationsStore.getAllProjectSituations);

const situations = computed(() => projectSituationsStore.situations);
</script>

<template>
  <div class="hidden h-full flex-1 flex-col space-y-8 p-8 md:flex">
    <div class="flex items-center justify-between space-y-2">
      <div>
        <h2 class="text-2xl font-bold tracking-tight">Situações de Projeto!</h2>
        <p class="text-muted-foreground">Aqui estão suas situações de projeto personalizadas!</p>
      </div>
      <div class="flex items-center space-x-2">
        <Dialog v-model:open="isOpen">
          <DialogTrigger as-child>
            <Button variant="outline" @click="resetForm">Adicionar nova situação</Button>
          </DialogTrigger>
          <DialogContent class="sm:max-w-[425px]">
            <form @submit.prevent="submitForm">
              <DialogHeader>
                <DialogTitle>{{ isEditMode ? 'Editar Situação de Projeto' : 'Nova Situação de Projeto' }}</DialogTitle>
                <DialogDescription>{{ isEditMode ? 'Edite os detalhes da situação de projeto' : 'Crie uma nova situação de projeto personalizada' }}</DialogDescription>
              </DialogHeader>
              <div class="grid gap-4 py-4">
                <div class="grid w-full max-w-sm items-center gap-1.5">
                  <Label for="name">Nome</Label>
                  <Input id="name" placeholder="Ex: Finalizado, Em andamento, Aguardando ..." v-model="formData.name" />
                  <p v-if="formErrors.name" class="text-red-600">{{ formErrors.name }}</p>
                </div>
                <div class="grid w-full max-w-sm items-center gap-1.5">
                  <Label for="description">Descrição</Label>
                  <Input id="description" placeholder="Forneça uma descrição da situação" v-model="formData.description" />
                  <p v-if="formErrors.description" class="text-red-600">{{ formErrors.description }}</p>
                </div>
              </div>
              <DialogFooter>
                <Button type="submit" :disabled="isSubmitting">{{ isEditMode ? 'Salvar alterações' : 'Salvar' }}</Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>
      </div>
    </div>

    <Table>
      <TableCaption v-if="!situations.length">Nenhuma situação de projeto cadastrada ainda.</TableCaption>
      <TableHeader>
        <TableRow>
          <TableHead>ID</TableHead>
          <TableHead>Nome</TableHead>
          <TableHead>Descrição</TableHead>
          <TableHead>Ações</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="situation in situations" :key="situation.id">
          <TableCell>{{ situation.id }}</TableCell>
          <TableCell class="font-medium">{{ situation.name }}</TableCell>
          <TableCell class="font-medium">{{ situation.description }}</TableCell>
          <TableCell>
            <Button variant="outline" @click="editClient(situation)">Editar</Button>
            <Button variant="destructive" @click="deleteClient(situation.id)">Excluir</Button>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </div>
</template>
