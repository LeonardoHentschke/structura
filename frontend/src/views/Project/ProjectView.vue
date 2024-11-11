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
import { Separator } from '@/components/ui/separator';
import { Button } from '@/components/ui/button';
import InputSelector from "@/components/InputSelector.vue";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import ProjectTypeForm from "@/components/ProjectTypeForm.vue";
import ProjectSituationForm from "@/components/ProjectSituationForm.vue";
import AddressForm from "@/components/AddressForm.vue"; // Novo componente para adicionar endereços

// Refs para listas de opções
const allClients = ref([]);
const clients = ref([]);
const clientAddresses = ref([]);
const projectTypes = ref([]);
const projectSituations = ref([]);
const loading = ref(true);
const isUpdateMode = ref(false);

// Estados para abrir os modais
const isProjectTypeModalOpen = ref(false);
const isProjectSituationModalOpen = ref(false);
const isAddressModalOpen = ref(false); // Novo estado para o modal de endereços

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
  await loadInitialData();

  // Se estivermos editando um projeto, buscar os dados do projeto
  if (route.params.id) {
    await fetchProject();
  }

  loading.value = false;
});

const loadInitialData = async () => {
  loading.value = true;

  // Buscar clientes
  const fetchedClients = await clientsStore.getAllClients();
  allClients.value = fetchedClients;
  clients.value = fetchedClients.map(client => ({
    value: client.id,
    label: client.name
  }));

  // Buscar tipos de projeto
  await refreshProjectTypes();

  // Buscar situações de projeto
  await refreshProjectSituations();
};

// Carrega um projeto existente
const fetchProject = async () => {
  if (route.params.id) {
    try {
      const project = await projectStore.getProject(route.params.id);

      // Atribuir dados do projeto ao formData
      formData.value = {
        client_id: project.client_id,
        address_id: project.address_id,
        type_id: project.type_id,
        situation_id: project.situation_id,
        mcmv: !!project.mcmv, // Garantir que `mcmv` seja booleano
        price: project.price,
        square_meters: project.square_meters,
      };

      isUpdateMode.value = true;

      // Carregar endereços do cliente selecionado
      loadClientAddresses(project.client_id);
    } catch (error) {
      console.error('Erro ao buscar o projeto:', error);
    }
  }
};

// Função para carregar endereços do cliente selecionado
const loadClientAddresses = (clientId) => {
  const selectedClient = allClients.value.find(client => client.id === clientId);
  if (selectedClient) {
    clientAddresses.value = selectedClient.addresses.map(address => ({
      value: address.id,
      label: `${address.street}, ${address.city}`
    }));

    // Garantir que o endereço seja selecionado automaticamente, se houver apenas um
    if (clientAddresses.value.length === 1) {
      formData.value.address_id = clientAddresses.value[0].value;
    }
  }
};

watch(() => formData.value.client_id, (newValue) => {
  if (newValue) {
    loadClientAddresses(newValue);
  } else {
    clientAddresses.value = [];
  }
}, { deep: true });

const submitForm = async () => {
  try {
    let response;

    if (isUpdateMode.value) {
      response = await projectStore.updateProject(route.params.id, formData.value);
    } else {
      response = await projectStore.createProject(formData.value);
    }

    if (response && (response.status === 200 || response.status === 201)) {
      // Redirecionar para a lista de projetos após salvar/atualizar com sucesso
      router.push({ name: 'projectList' });
    } else if (response && response.errors) {
      console.error('Erro ao salvar o projeto:', response.errors);
    } else {
      console.error('Erro ao salvar o projeto: Resposta inesperada', response);
    }
  } catch (error) {
    console.error('Erro ao salvar o projeto:', error);
  }
};

// Função para reatualizar as opções após adicionar um novo Tipo ou Situação
const refreshProjectTypes = async () => {
  const fetchedProjectTypes = await projectTypesStore.getAllProjectTypes();
  projectTypes.value = fetchedProjectTypes.map(type => ({
    value: type.id,
    label: type.name
  }));
};

const refreshProjectSituations = async () => {
  const fetchedProjectSituations = await projectSituationsStore.getAllProjectSituations();
  projectSituations.value = fetchedProjectSituations.map(situation => ({
    value: situation.id,
    label: situation.name
  }));
};

// Funções para tratar o fechamento do modal e atualização da lista ao salvar um novo tipo ou situação
const handleProjectTypeSaved = async () => {
  await refreshProjectTypes();
  isProjectTypeModalOpen.value = false; // Fechar o modal após salvar
};

const handleProjectSituationSaved = async () => {
  await refreshProjectSituations();
  isProjectSituationModalOpen.value = false; // Fechar o modal após salvar
};

// Função para tratar o fechamento do modal e atualização da lista de endereços ao salvar um novo endereço
const handleAddressSaved = async (newAddress) => {
  await loadClientAddresses(formData.value.client_id);
  if (newAddress && newAddress.id) {
    formData.value.address_id = newAddress.id;
  }
  isAddressModalOpen.value = false; // Fechar o modal após salvar
};
</script>

<template>
  <div class="hidden space-y-6 p-10 pb-16 md:block dark:bg-gray-900">
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
              <div class="flex items-center space-x-2">
                <InputSelector v-if="!loading" :options="clientAddresses" v-model="formData.address_id" :disabled="formData.client_id === ''" />
                <Dialog v-model:open="isAddressModalOpen">
                  <DialogTrigger as-child>
                    <Button variant="outline">+</Button>
                  </DialogTrigger>
                  <DialogContent>
                    <DialogHeader>
                      <DialogTitle>Novo Endereço</DialogTitle>
                    </DialogHeader>
                    <AddressForm @saved="handleAddressSaved" :client-id="formData.client_id" />
                  </DialogContent>
                </Dialog>
              </div>
            </div>
            <div class="flex flex-col space-y-1.5">
              <Label for="projectType">Tipo de projeto</Label>
              <div class="flex items-center space-x-2">
                <InputSelector v-if="!loading" :options="projectTypes" v-model="formData.type_id" />
                <Dialog v-model:open="isProjectTypeModalOpen">
                  <DialogTrigger as-child>
                    <Button variant="outline">+</Button>
                  </DialogTrigger>
                  <DialogContent>
                    <DialogHeader>
                      <DialogTitle>Novo Tipo de Projeto</DialogTitle>
                    </DialogHeader>
                    <ProjectTypeForm @saved="handleProjectTypeSaved" />
                  </DialogContent>
                </Dialog>
              </div>
            </div>
            <div class="flex flex-col space-y-1.5">
              <Label for="projectSituation">Situação do projeto</Label>
              <div class="flex items-center space-x-2">
                <InputSelector v-if="!loading" :options="projectSituations" v-model="formData.situation_id" />
                <Dialog v-model:open="isProjectSituationModalOpen">
                  <DialogTrigger as-child>
                    <Button variant="outline">+</Button>
                  </DialogTrigger>
                  <DialogContent>
                    <DialogHeader>
                      <DialogTitle>Nova Situação de Projeto</DialogTitle>
                    </DialogHeader>
                    <ProjectSituationForm @saved="handleProjectSituationSaved" />
                  </DialogContent>
                </Dialog>
              </div>
            </div>
            <div class="flex items-center space-x-2 col-span-2">
              <input type="checkbox" id="mcmv-switch" :checked="formData.mcmv" @change="handleMcmvChange" />
              <Label for="mcmv-switch">Minha casa minha vida?</Label>
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
