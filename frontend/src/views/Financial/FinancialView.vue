<script setup>
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useFinancialStore } from '@/stores/financial';
import { useProjectStore } from '@/stores/project';

import { NumberField, NumberFieldContent, NumberFieldDecrement, NumberFieldIncrement, NumberFieldInput } from '@/components/ui/number-field';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import InputSelector from "@/components/InputSelector.vue";

// Refs para listas de opções
const isUpdateMode = ref(false);
const loading = ref(true);

const route = useRoute();
const router = useRouter();

const formData = ref({
  description: '',
  amount: 0,
  type: '',
  transaction_date: new Date().toISOString().slice(0, 10),
  project_id: null,
});

// Instâncias das stores
const financeStore = useFinancialStore();
const projectStore = useProjectStore();

// Computed para projetos disponíveis (para garantir reatividade)
const projects = computed(() =>
  projectStore.projects.map(project => {
    console.log("Verificando estrutura do projeto:", project);
    return {
      value: project.id,
      label: "Projeto #" + project.id + " | Cliente: " + project.client.name
    };
  })
);


onMounted(async () => {
  loading.value = true;
  await loadProjects(); // Carregar todos os projetos disponíveis
  await fetchFinanceData();
  loading.value = false;
});

const loadProjects = async () => {
  try {
    await projectStore.getAllProjects();
  } catch (error) {
    console.error("Erro ao carregar projetos:", error);
  }
};

const submitForm = async () => {
  try {
    let response;

    if (isUpdateMode.value) {
      response = await financeStore.updateFinance(route.params.id, formData.value);
    } else {
      response = await financeStore.createFinance(formData.value);
    }

    if (response && response.ok) {
      router.push({ name: 'financialList' });
    } else if (response && response.errors) {
      console.error("Erro ao cadastrar movimentação financeira:", response.errors);
    } else {
      console.error("Erro ao cadastrar movimentação financeira: Resposta inesperada.", response);
    }
  } catch (error) {
    console.error("Erro ao cadastrar movimentação financeira:", error);
  }
};

const fetchFinanceData = async () => {
  if (route.params.id) {
    try {
      const transaction = await financeStore.getFinance(route.params.id);
      formData.value = { ...transaction };
      isUpdateMode.value = true;
    } catch (error) {
      console.error("Erro ao buscar dados da transação:", error);
    }
  }
};
</script>

<template>
  <div class="hidden space-y-6 p-10 pb-16 md:block">
    <Card>
      <CardHeader>
        <CardTitle>{{ isUpdateMode ? 'Editar Movimento Financeiro' : 'Cadastrar Movimento Financeiro' }}</CardTitle>
        <CardDescription>
          {{ isUpdateMode ? 'Edite as informações do movimento financeiro' : 'Gerencie as informações de seu movimento financeiro' }}
        </CardDescription>
        <Separator class="my-3" />
      </CardHeader>
      <form @submit.prevent="submitForm">
        <CardContent>
          <div class="grid grid-cols-2 gap-4 w-full">
            <div class="flex flex-col space-y-1.5">
              <Label for="transactionType">Tipo de Transação</Label>
              <InputSelector
                :options="[{ value: 'income', label: 'Entrada' }, { value: 'expense', label: 'Saída' }]"
                v-model="formData.type"
              />
            </div>
            <div class="flex flex-col space-y-1.5">
              <Label for="project">Projeto</Label>
              <InputSelector
                v-if="!loading"
                :options="projects"
                v-model="formData.project_id"
              />
              <p v-if="!projects.length && !loading" class="text-red-500 text-xs mt-1">
                Nenhum projeto disponível.
              </p>
            </div>
            <div class="flex flex-col space-y-1.5 col-span-2">
              <Label for="description">Descrição</Label>
              <Textarea v-model="formData.description" placeholder="Forneça uma descrição para esta transação financeira..." />
            </div>
            <div class="col-span-2">
              <NumberField
                id="amount"
                v-model="formData.amount"
                :default-value="0"
                :format-options="{
                    style: 'currency',
                    currency: 'BRL',
                    currencyDisplay: 'symbol',
                    }"
              >
                <Label for="amount">Valor</Label>
                <NumberFieldContent>
                  <NumberFieldDecrement />
                  <NumberFieldInput />
                  <NumberFieldIncrement />
                </NumberFieldContent>
              </NumberField>
            </div>
            <div class="flex flex-col space-y-1.5 col-span-2">
              <Label for="date">Data</Label>
              <input type="date" v-model="formData.transaction_date" class="input w-full" />
            </div>
          </div>
        </CardContent>
        <CardFooter>
          <div class="flex justify-end">
            <Button type="submit">{{ isUpdateMode ? 'Salvar alterações' : 'Cadastrar' }}</Button>
          </div>
        </CardFooter>
      </form>
    </Card>
  </div>
</template>
