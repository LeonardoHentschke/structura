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
  created_at: "",
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
  if (isUpdateMode.value) {
    await clientsStore.updateClient(route.params.id, formData.value);
  } else {
    await clientsStore.createClient(formData.value);
    router.push({ name: 'clientList' });
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
  formData.value.addresses.splice(index, 1);
};

onMounted(() => {
  fetchClient();
});

const sidebarNavItems = [
  {
    key: 'data',
    title: 'Dados',
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
  <div class="hidden space-y-6 p-10 pb-16 md:block">
    <div class="space-y-0.5">
      <h2 class="text-2xl font-bold tracking-tight">
        {{ isUpdateMode ? 'Editar cliente' : 'Cadastrar cliente' }}: {{ formData.name }}
      </h2>
      <p class="text-muted-foreground">Gerencie as informações de seu cliente</p>
    </div>
    <Separator class="my-6" />
    <div class="flex flex-col space-y-8 lg:flex-row lg:space-x-12 lg:space-y-0">
      <aside class="-mx-4 lg:w-1/5">
        <nav class="flex space-x-2 lg:flex-col lg:space-x-0 lg:space-y-1">
          <Button
            v-for="item in sidebarNavItems"
            :key="item.title"
            variant="ghost"
            @click="selectNavItem(item)"
            :class="{'bg-accent': selectedNavItem === item.key}"
          >
            {{ item.title }}
          </Button>
        </nav>
      </aside>
      <div class="flex-1">
        <div class="space-y-6">
          <form @submit.prevent="submitForm">
            <Card v-if="selectedNavItem === 'data'">
              <CardHeader>
                <CardTitle>{{ isUpdateMode ? `Cliente ID: ${formData.id}` : 'Cadastrar Novo Cliente' }}</CardTitle>
                <CardDescription>Altere ou insira as informações do cliente</CardDescription>
              </CardHeader>
              <CardContent>
                <div class="grid items-center w-full gap-4">
                  <div v-if="isUpdateMode" class="flex flex-col space-y-1.5">
                    <Label for="name">ID</Label>
                    <Input id="name" type="text" v-model="formData.id" disabled />
                  </div>
                  <div class="flex flex-col space-y-1.5">
                    <Label for="name">Nome</Label>
                    <Input id="name" type="text" v-model="formData.name" />
                  </div>
                  <div class="flex flex-col space-y-1.5">
                    <Label for="email">Email</Label>
                    <Input id="email" type="email" v-model="formData.email" />
                  </div>
                  <div class="flex flex-col space-y-1.5">
                    <Label for="phone">Telefone</Label>
                    <Input id="phone" type="text" v-model="formData.phone" />
                  </div>
                  <div class="flex flex-col space-y-1.5">
                    <Label for="cpf">CPF</Label>
                    <Input id="cpf" type="text" v-model="formData.cpf" />
                  </div>
                  <div class="flex flex-col space-y-1.5">
                    <Label for="birth_date">Data de Nascimento</Label>
                    <Input id="birth_date" type="date" v-model="formData.birth_date" />
                  </div>
                  <div v-if="isUpdateMode" class="flex flex-col space-y-1.5">
                    <Label for="created_at">Adicionado em:</Label>
                    <Input id="created_at" type="date" v-model="formData.created_at" disabled />
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card v-if="selectedNavItem === 'address'">
              <CardHeader>
                <CardTitle>{{ isUpdateMode ? `Endereços do cliente ID: ${formData.id}` : 'Cadastrar Endereços' }}</CardTitle>
                <CardDescription>Gerencie os endereços do cliente</CardDescription>
              </CardHeader>
              <CardContent>
                <div v-for="(address, index) in formData.addresses" :key="index" class="grid items-center w-full gap-4">
                  <div class="flex flex-col space-y-1.5">
                    <Label for="street">Rua</Label>
                    <Input id="street" type="text" v-model="address.street" />
                  </div>
                  <div class="flex flex-col space-y-1.5">
                    <Label for="number">Número</Label>
                    <Input id="number" type="text" v-model="address.number" />
                  </div>
                  <div class="flex flex-col space-y-1.5">
                    <Label for="city">Cidade</Label>
                    <Input id="city" type="text" v-model="address.city" />
                  </div>
                  <div class="flex flex-col space-y-1.5">
                    <Label for="latitude">Latitude</Label>
                    <Input id="latitude" type="text" v-model="address.latitude" />
                  </div>
                  <div class="flex flex-col space-y-1.5">
                    <Label for="longitude">Longitude</Label>
                    <Input id="longitude" type="text" v-model="address.longitude" />
                  </div>
                  <div class="flex justify-end">
                    <Button variant="destructive" @click.prevent="removeAddress(index)" :disabled="formData.addresses.length === 1">Remover Endereço</Button>
                  </div>
                  <Separator class="my-5" />
                </div>
                <Button variant="outline" @click.prevent="addAddress">Adicionar mais um endereço</Button>
              </CardContent>
            </Card>

            <Button class="mt-3 w-full">{{ isUpdateMode ? 'Atualizar Cliente' : 'Cadastrar Cliente' }}</Button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
