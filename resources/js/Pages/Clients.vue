<script setup>
import { router, useForm } from "@inertiajs/vue3";
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';

const form = useForm({
  name: '',
  cpf: '',
  birth_date: '',
  addresses: [{ street: '', number: '', city: '', latitude: null, longitude: null }],
});

const addAddress = () => {
  form.addresses.push({ street: '', number: '', city: '', latitude: null, longitude: null });
};

const submitForm = () => {
  form.post(route("clients"), {
    preserveScroll: true,
    onError: (errors) => {
      console.error(errors); // Loga os erros no console
    },
  });
};
</script>

<template>
  <div class="max-w-2xl mx-auto p-4">
    <h1 class="text-xl font-bold mb-4">Cadastrar Cliente</h1>

    <form @submit.prevent="submitForm">
      <div class="mb-4">
        <Label for="name" class="block text-sm font-medium">Nome</Label>
        <Input
          v-model="form.name"
          id="name"
          type="text"
          placeholder="Digite o nome"
          required
        />
      </div>
      <div class="mb-4">
        <Label for="cpf" class="block text-sm font-medium">CPF</Label>
        <Input
          v-model="form.cpf"
          id="cpf"
          type="text"
          placeholder="Digite o CPF"
          required
        />
      </div>

      <!-- Data de nascimento -->
      <div class="mb-4">
        <Label for="birth_date" class="block text-sm font-medium">Data de Nascimento</Label>
        <Input
          v-model="form.birth_date"
          id="birth_date"
          type="date"
          required
        />
      </div>

      <!-- Endereços -->
      <div class="mb-4">
        <Label class="block text-sm font-medium">Endereços</Label>
        <div v-for="(address, index) in form.addresses" :key="index" class="mb-4 border-b pb-2">
          <div class="mb-2">
            <Label class="block text-sm">Rua</Label>
            <Input
              v-model="address.street"
              type="text"
              placeholder="Digite a rua"
              required
            />
          </div>
          <div class="mb-2">
            <Label class="block text-sm">Número</Label>
            <Input
              v-model="address.number"
              type="text"
              placeholder="Digite o número"
              required
            />
          </div>
          <div class="mb-2">
            <Label class="block text-sm">Cidade</Label>
            <Input
              v-model="address.city"
              type="text"
              placeholder="Digite a cidade"
              required
            />
          </div>
          <div class="mb-2">
            <Label class="block text-sm">Latitude</Label>
            <Input
              v-model="address.latitude"
              type="number"
              placeholder="Digite a latitude (opcional)"
            />
          </div>
          <div class="mb-2">
            <Label class="block text-sm">Longitude</Label>
            <Input
              v-model="address.longitude"
              type="number"
              placeholder="Digite a longitude (opcional)"
            />
          </div>
        </div>
        <Button @click="addAddress" class="mt-2">Adicionar Endereço</Button>
      </div>

      <div class="mt-4">
        <Button>Cadastrar</Button>
      </div>
    </form>
  </div>
</template>
