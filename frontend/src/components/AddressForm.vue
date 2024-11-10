<script setup>
import { reactive, ref, defineEmits } from "vue";
import { useProjectStore } from "@/stores/project";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";

// Emit para notificar o pai sobre o endereço salvo
const emit = defineEmits(['saved']);

// Campos do formulário para o endereço
const formData = reactive({
  street: "",
  number: "",
  city: "",
  latitude: "",
  longitude: "",
});

// Estado para erros no formulário
const formErrors = ref({});
const isSubmitting = ref(false);

const projectStore = useProjectStore();

// Função de validação do formulário
const validateForm = () => {
  formErrors.value = {};
  if (!formData.street) formErrors.value.street = "Rua é obrigatória.";
  if (!formData.number) formErrors.value.number = "Número é obrigatório.";
  if (!formData.city) formErrors.value.city = "Cidade é obrigatória.";
  return Object.keys(formErrors.value).length === 0;
};

// Função de envio do formulário
const submitForm = async () => {
  if (!validateForm()) return;
  isSubmitting.value = true;

  try {
    // Adiciona o endereço ao projeto (não é persistido de imediato no backend)
    emit('saved', { ...formData });
  } catch (error) {
    console.error("Erro ao criar o endereço:", error);
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <form @submit.prevent="submitForm">
    <div class="grid gap-4 py-4">
      <div class="grid w-full max-w-sm items-center gap-1.5">
        <label for="street">Rua</label>
        <Input id="street" placeholder="Ex: Avenida Brasil" v-model="formData.street" />
        <p v-if="formErrors.street" class="text-red-600">{{ formErrors.street }}</p>
      </div>
      <div class="grid w-full max-w-sm items-center gap-1.5">
        <label for="number">Número</label>
        <Input id="number" placeholder="Ex: 123" v-model="formData.number" />
        <p v-if="formErrors.number" class="text-red-600">{{ formErrors.number }}</p>
      </div>
      <div class="grid w-full max-w-sm items-center gap-1.5">
        <label for="city">Cidade</label>
        <Input id="city" placeholder="Ex: São Paulo" v-model="formData.city" />
        <p v-if="formErrors.city" class="text-red-600">{{ formErrors.city }}</p>
      </div>
      <div class="grid w-full max-w-sm items-center gap-1.5">
        <label for="latitude">Latitude</label>
        <Input id="latitude" placeholder="Ex: -23.5505" v-model="formData.latitude" />
      </div>
      <div class="grid w-full max-w-sm items-center gap-1.5">
        <label for="longitude">Longitude</label>
        <Input id="longitude" placeholder="Ex: -46.6333" v-model="formData.longitude" />
      </div>
    </div>
    <Button type="submit" :disabled="isSubmitting">Salvar</Button>
  </form>
</template>
