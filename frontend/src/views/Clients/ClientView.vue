<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useClientsStore } from "@/stores/clients";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';

const route = useRoute();
const router = useRouter();
const isUpdateMode = ref(false);
const isLoading = ref(false); // Estado de carregamento para o botão de envio

const formData = ref({
  name: "",
  email: "",
  phone: "",
  cpf: "",
  birth_date: "",
  addresses: [
    {
      street: "",
      number: "",
      city: "",
      latitude: "",
      longitude: "",
    },
  ],
});

const clientsStore = useClientsStore();

const fetchClient = async () => {
  if (route.params.id) {
    const client = await clientsStore.getClient(route.params.id);
    formData.value = { ...client };
    isUpdateMode.value = true;
  } else {
    formData.value = {
      name: "",
      email: "",
      phone: "",
      cpf: "",
      birth_date: "",
      addresses: [
        {
          street: "",
          number: "",
          city: "",
          latitude: "",
          longitude: "",
        },
      ],
    };
  }
};

const submitForm = async () => {
  isLoading.value = true; // Ativar carregamento
  try {
    if (isUpdateMode.value) {
      await clientsStore.updateClient(route.params.id, formData.value);
    } else {
      await clientsStore.createClient(formData.value);
      router.push({ name: 'clientList' });
    }
  } finally {
    isLoading.value = false; // Desativar carregamento
  }
};

const addAddress = () => {
  formData.value.addresses.push({
    street: "",
    number: "",
    city: "",
    latitude: "",
    longitude: "",
  });
};

const removeAddress = (index) => {
  if (confirm("Tem certeza de que deseja remover este endereço?")) {
    formData.value.addresses.splice(index, 1);
  }
};

onMounted(() => {
  fetchClient();
});

const sidebarNavItems = [
  {
    key: 'data',
    title: 'Dados Pessoais',
  },
  {
    key: 'address',
    title: 'Endereço',
  },
];
const selectedNavItem = ref('data');
const selectNavItem = (item) => {
  selectedNavItem.value = item.key;
};
</script>

<template>
  <div class="h-full p-10 pb-16 bg-gray-100 dark:bg-gray-900">
    <!-- Header -->
    <div class="mb-8">
      <h2 class="text-3xl font-extrabold text-primary dark:text-yellow-500 tracking-tight">{{ isUpdateMode ? 'Editar Cliente' : 'Cadastrar Cliente' }}</h2>
      <p class="text-gray-600 dark:text-gray-300 text-lg mt-1">Gerencie as informações do cliente de forma detalhada e profissional.</p>
    </div>
    
    <!-- Main Content -->
    <div class="grid lg:grid-cols-4 gap-12">
      <!-- Sidebar Navigation -->
      <aside class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <nav class="space-y-4">
          <Button
            v-for="item in sidebarNavItems"
            :key="item.key"
            variant="ghost"
            @click="selectNavItem(item)"
            :class="{
              'bg-primary/10 text-primary font-semibold dark:bg-yellow-500 dark:text-gray-900': selectedNavItem === item.key,
              'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-primary': selectedNavItem !== item.key
            }"
            class="block w-full text-left px-4 py-2 rounded-md transition-all duration-200"
          >
            {{ item.title }}
          </Button>
        </nav>
      </aside>

      <!-- Form Section -->
      <div class="lg:col-span-3">
        <form @submit.prevent="submitForm" class="space-y-6">
          <Card v-if="selectedNavItem === 'data'" class="bg-white dark:bg-gray-800 shadow-md rounded-lg">
            <CardHeader class="bg-gray-50 dark:bg-gray-700 p-4 rounded-t-lg">
              <CardTitle class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ isUpdateMode ? `Cliente ID: ${formData.id}` : 'Cadastrar Novo Cliente' }}</CardTitle>
              <CardDescription class="text-gray-600 dark:text-gray-300">Complete os dados pessoais do cliente abaixo.</CardDescription>
            </CardHeader>
            <CardContent class="p-6">
              <div class="grid lg:grid-cols-2 gap-6">
                <div class="flex flex-col space-y-1.5">
                  <Label for="name" class="text-gray-700 dark:text-gray-300">Nome Completo</Label>
                  <Input id="name" type="text" v-model="formData.name" placeholder="Digite o nome completo" class="bg-gray-50 dark:bg-gray-700 dark:text-gray-100" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label for="email" class="text-gray-700 dark:text-gray-300">Email</Label>
                  <Input id="email" type="email" v-model="formData.email" placeholder="Digite o email" class="bg-gray-50 dark:bg-gray-700 dark:text-gray-100" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label for="phone" class="text-gray-700 dark:text-gray-300">Telefone</Label>
                  <Input id="phone" type="text" v-model="formData.phone" placeholder="Digite o telefone" class="bg-gray-50 dark:bg-gray-700 dark:text-gray-100" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label for="cpf" class="text-gray-700 dark:text-gray-300">CPF</Label>
                  <Input id="cpf" type="text" v-model="formData.cpf" placeholder="Digite o CPF" class="bg-gray-50 dark:bg-gray-700 dark:text-gray-100" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label for="birth_date" class="text-gray-700 dark:text-gray-300">Data de Nascimento</Label>
                  <Input id="birth_date" type="date" v-model="formData.birth_date" class="bg-gray-50 dark:bg-gray-700 dark:text-gray-100" />
                </div>
              </div>
            </CardContent>
          </Card>

          <Card v-if="selectedNavItem === 'address'" class="bg-white dark:bg-gray-800 shadow-md rounded-lg">
            <CardHeader class="bg-gray-50 dark:bg-gray-700 p-4 rounded-t-lg">
              <CardTitle class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ isUpdateMode ? `Endereços do cliente ID: ${formData.id}` : 'Cadastrar Endereços' }}</CardTitle>
              <CardDescription class="text-gray-600 dark:text-gray-300">Gerencie os endereços do cliente.</CardDescription>
            </CardHeader>
            <CardContent class="p-6">
              <div v-for="(address, index) in formData.addresses" :key="index" class="grid lg:grid-cols-2 gap-6 p-4 border rounded-md border-gray-200 dark:border-gray-700 mb-4">
                <div class="flex flex-col space-y-1.5">
                  <Label for="street" class="text-gray-700 dark:text-gray-300">Rua</Label>
                  <Input id="street" type="text" v-model="address.street" placeholder="Digite a rua" class="bg-gray-50 dark:bg-gray-700 dark:text-gray-100" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label for="number" class="text-gray-700 dark:text-gray-300">Número</Label>
                  <Input id="number" type="text" v-model="address.number" placeholder="Digite o número" class="bg-gray-50 dark:bg-gray-700 dark:text-gray-100" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label for="city" class="text-gray-700 dark:text-gray-300">Cidade</Label>
                  <Input id="city" type="text" v-model="address.city" placeholder="Digite a cidade" class="bg-gray-50 dark:bg-gray-700 dark:text-gray-100" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label for="latitude" class="text-gray-700 dark:text-gray-300">Latitude</Label>
                  <Input id="latitude" type="text" v-model="address.latitude" placeholder="Digite a latitude (opcional)" class="bg-gray-50 dark:bg-gray-700 dark:text-gray-100" />
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label for="longitude" class="text-gray-700 dark:text-gray-300">Longitude</Label>
                  <Input id="longitude" type="text" v-model="address.longitude" placeholder="Digite a longitude (opcional)" class="bg-gray-50 dark:bg-gray-700 dark:text-gray-100" />
                </div>
                <Button variant="destructive" @click.prevent="removeAddress(index)" :disabled="formData.addresses.length === 1" class="text-red-600 dark:text-red-400">Remover Endereço</Button>
              </div>
              <Button variant="outline" class="mt-4 text-primary dark:text-yellow-500 dark:border-gray-600 dark:bg-gray-700">Adicionar mais um endereço</Button>
            </CardContent>
          </Card>

          <Button type="submit" :disabled="isLoading" class="mt-8 w-full bg-primary text-primary-foreground dark:bg-yellow-500 dark:text-gray-900 transform hover:scale-105 transition-transform duration-300 shadow-md">
            <span v-if="isLoading">Salvando...</span>
            <span v-else>{{ isUpdateMode ? 'Atualizar Cliente' : 'Cadastrar Cliente' }}</span>
          </Button>
        </form>
      </div>
    </div>
  </div>
</template>
