<script setup>
import { ref, onMounted, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useProjectStore } from '@/stores/project';
import { useClientsStore } from "@/stores/clients";
import { useProjectTypesStore } from "@/stores/projectType";
import { useProjectSituationsStore } from "@/stores/projectSituation";

import { NumberField, NumberFieldContent, NumberFieldDecrement, NumberFieldIncrement, NumberFieldInput } from '@/components/ui/number-field';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Separator } from '@/components/ui/separator';
import { Button } from '@/components/ui/button';
import InputSelector from "@/components/InputSelector.vue";

// Refs para listas de opções
const allClients = ref([]);
const clients = ref([]);
const clientAddresses = ref([]);
const projectTypes = ref([]);
const projectSituations = ref([]);
const loading = ref(true);
const isUpdateMode = ref(false);

const route = useRoute();
const router = useRouter();

const formData = ref({
  client_id: '',
  address_id: '',
  type_id: '',
  situation_id: '',
  mcmv: false,
  price: 5000,
  square_meters: 50,
});

// Instâncias das stores
const clientsStore = useClientsStore();
const projectTypesStore = useProjectTypesStore();
const projectSituationsStore = useProjectSituationsStore();
const projectStore = useProjectStore();

onMounted(async () => {
  fetchProject();

  try {
    loading.value = true;

    // Buscar clientes
    const fetchedClients = await clientsStore.getAllClients();
    allClients.value = fetchedClients;
    clients.value = fetchedClients.map(client => ({
      value: client.id,
      label: client.name
    }));

    // Buscar tipos de projeto
    const fetchedProjectTypes = await projectTypesStore.getAllProjectTypes();
    projectTypes.value = fetchedProjectTypes.map(type => ({
      value: type.id,
      label: type.name
    }));

    // Buscar situações de projeto
    const fetchedProjectSituations = await projectSituationsStore.getAllProjectSituations();
    projectSituations.value = fetchedProjectSituations.map(situation => ({
      value: situation.id,
      label: situation.name
    }));

  } finally {
    loading.value = false;
  }
});

watch(() => formData.value.client_id, async (newValue) => {
  if (!newValue) {
    clientAddresses.value = [];
    return;
  }

  const selectedClient = allClients.value.find(client => client.id === newValue);
  if (selectedClient) {
    clientAddresses.value = selectedClient.addresses.map(address => ({
      value: address.id,
      label: `${address.street}, ${address.city}`
    }));

    if (clientAddresses.value.length === 1) {
      formData.value.address_id = clientAddresses.value[0].value;
    }
  }
}, { deep: true });

const submitForm = async () => {
  if (isUpdateMode.value) {
    await projectStore.updateProject(route.params.id, formData.value);
  } else {
    const response = await projectStore.createProject(formData.value);
    if(response.status === 201) {
      router.push({ name: 'projectList' });
    }
  }
};

const fetchProject = async () => {
  if (route.params.id) {
    const project = await projectStore.getProject(route.params.id);
    console.log(project);
    formData.value = { ...project };
    isUpdateMode.value = true;
  }
};
</script>

<template>
  <div class="hidden space-y-6 p-10 pb-16 md:block">
    <Card>
      <CardHeader>
        <CardTitle>Cadastrar projeto</CardTitle>
        <CardDescription>Gerencie as informações de seu projeto</CardDescription>
        <Separator class="my-3" />
      </CardHeader>
      <form @submit.prevent="submitForm">
        <CardContent>
          <div class="grid grid-cols-2 gap-4 w-full">
            <div class="flex flex-col space-y-1.5">
              <Label for="client">Cliente</Label>
              <InputSelector v-if="!loading" :options="clients" v-model="formData.client_id" />
            </div>
            <div class="flex flex-col space-y-1.5">
              <Label for="address">Endereço</Label>
              <InputSelector v-if="!loading" :options="clientAddresses" v-model="formData.address_id" :disabled="formData.client_id === ''" />
            </div>
            <div class="flex flex-col space-y-1.5">
              <Label for="projectType">Tipo de projeto</Label>
              <InputSelector v-if="!loading" :options="projectTypes" v-model="formData.type_id" />
            </div>
            <div class="flex flex-col space-y-1.5">
              <Label for="projectSituation">Situação do projeto</Label>
              <InputSelector v-if="!loading" :options="projectSituations" v-model="formData.situation_id" />
            </div>
            <div class="flex items-center space-x-2 col-span-2">
              <Switch id="airplane-mode" v-model="formData.mcmv"/>
              <Label for="airplane-mode">Minha casa minha vida?</Label>
            </div>
            <div class="col-span-2">
              <NumberField
                id="value"
                v-model="formData.price"
                :default-value="5000"
                :format-options="{
                  style: 'currency',
                  currency: 'BRL',
                  currencyDisplay: 'symbol',
                  currencySign: 'accounting',
                }"
              >
                <Label for="value">Valor</Label>
                <NumberFieldContent>
                  <NumberFieldDecrement />
                  <NumberFieldInput />
                  <NumberFieldIncrement />
                </NumberFieldContent>
              </NumberField>
            </div>
            <div class="col-span-2">
              <NumberField
                id="square-meters"
                v-model="formData.square_meters"
                :default-value="50"
                :step="0.1"
                :min="0"
              >
                <Label for="square-meters">Metros Quadrados</Label>
                <NumberFieldContent>
                  <NumberFieldDecrement />
                  <NumberFieldInput />
                  <NumberFieldIncrement />
                </NumberFieldContent>
              </NumberField>
            </div>
          </div>
        </CardContent>
        <CardFooter>
          <div class="flex justify-end">
            <Button>Salvar</Button>
          </div>
        </CardFooter>
      </form>
    </Card>
  </div>
</template>