<script setup>
import { ref, onMounted, watch } from "vue";
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

// Refs para valores selecionados
const clientValue = ref('');

// Refs para listas de opções
const allClients = ref([]);
const clients = ref([]);
const clientAddresses = ref([]);
const projectTypes = ref([]);
const projectSituations = ref([]);
const loading = ref(true);
const isUpdateMode = ref(false);

const formData = ref({
  client: '',
  address: '',
  projectType: '',
  projectSituation: '',
  price: 5000,
  squareMeters: 50,
});

// Instâncias das stores
const clientsStore = useClientsStore();
const projectTypesStore = useProjectTypesStore();
const projectSituationsStore = useProjectSituationsStore();

onMounted(async () => {
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

// Monitorar seleção do cliente e buscar endereços
watch(clientValue, async (newValue) => {
  if (newValue) {
    formData.value.client = newValue;
    // Buscar endereços do cliente selecionado    
    const selectedClient = allClients.value.find(client => client.name === newValue);

    clientAddresses.value = selectedClient.addresses.map(address => ({
      value: address.street,
      label: address.street + ', ' + address.city
    }));
  } else {
    clientAddresses.value = [];
  }
});

// Função para enviar o formulário
const submitForm = async () => {
  if (isUpdateMode.value) {
    // Implementação para atualização
  } else {
    console.log(formData.value);
    
    // await useProjectStore.createProject(formData.value);
    // router.push({ name: 'projectList' });
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
              <InputSelector v-if="!loading" :options="clients" v-model="formData.client" />
            </div>
            <div class="flex flex-col space-y-1.5">
              <Label for="address">Endereço</Label>
              <InputSelector v-if="!loading" :options="clientAddresses" v-model="formData.address" disabled />
            </div>
            <div class="flex flex-col space-y-1.5">
              <Label for="projectType">Tipo de projeto</Label>
              <InputSelector v-if="!loading" :options="projectTypes" v-model="formData.projectType" />
            </div>
            <div class="flex flex-col space-y-1.5">
              <Label for="projectSituation">Situação do projeto</Label>
              <InputSelector v-if="!loading" :options="projectSituations" v-model="formData.projectSituation" />
            </div>
            <div class="flex items-center space-x-2 col-span-2">
              <Switch id="airplane-mode" />
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
                v-model="formData.squareMeters"
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