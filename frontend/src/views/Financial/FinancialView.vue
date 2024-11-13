<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useFinancialStore } from '@/stores/financial';

import { NumberField, NumberFieldContent, NumberFieldDecrement, NumberFieldIncrement, NumberFieldInput } from '@/components/ui/number-field';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea'
import InputSelector from "@/components/InputSelector.vue";

// Refs para listas de opções
const isUpdateMode = ref(false);

const route = useRoute();
const router = useRouter();

const formData = ref({
  description: '',
  amount: 0,
  type: '',
  transaction_date: new Date().toISOString().slice(0, 10),
});

// Instâncias das stores
const financeStore = useFinancialStore();

onMounted(async () => {
  fetchFinanceData();
});

const submitForm = async () => {
  const action = isUpdateMode.value
    ? financeStore.updateFinance(route.params.id, formData.value)
    : financeStore.createFinance(formData.value);
  
  const response = await action;
  if (response.ok) {
    router.push({ name: 'financialList' });
  }
};

const fetchFinanceData = async () => {
  if (route.params.id) {
    const transaction = await financeStore.getFinance(route.params.id);
    formData.value = { ...transaction };
    isUpdateMode.value = true;
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
            <Button>{{ isUpdateMode ? 'Salvar alterações' : 'Cadastrar' }}</Button>
          </div>
        </CardFooter>
      </form>
    </Card>
  </div>
</template>
