<script setup>
import { reactive, ref} from "vue";
import { useProjectSituationsStore } from "@/stores/projectSituation";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";

// Definir o evento emit
const emit = defineEmits(['saved']);

const formData = reactive({
  name: "",
  description: "",
});
const formErrors = ref({});
const isSubmitting = ref(false);

const projectSituationsStore = useProjectSituationsStore();

const validateForm = () => {
  formErrors.value = {};
  if (!formData.name) formErrors.value.name = "Nome é obrigatório.";
  if (!formData.description) formErrors.value.description = "Descrição é obrigatória.";
  return Object.keys(formErrors.value).length === 0;
};

const submitForm = async () => {
  if (!validateForm()) return;
  isSubmitting.value = true;

  try {
    // Criar a nova situação do projeto
    await projectSituationsStore.createProjectSituation(formData);

    // Emitir um evento de sucesso ao salvar
    emit('saved');
  } catch (error) {
    console.error("Erro ao criar a situação do projeto:", error);
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <form @submit.prevent="submitForm">
    <div class="grid gap-4 py-4">
      <div class="grid w-full max-w-sm items-center gap-1.5">
        <label for="name">Nome</label>
        <Input id="name" placeholder="Ex: Em Andamento, Concluído" v-model="formData.name" />
        <p v-if="formErrors.name" class="text-red-600">{{ formErrors.name }}</p>
      </div>
      <div class="grid w-full max-w-sm items-center gap-1.5">
        <label for="description">Descrição</label>
        <Input id="description" placeholder="Forneça uma descrição da situação do projeto" v-model="formData.description" />
        <p v-if="formErrors.description" class="text-red-600">{{ formErrors.description }}</p>
      </div>
    </div>
    <Button type="submit" :disabled="isSubmitting">Salvar</Button>
  </form>
</template>
