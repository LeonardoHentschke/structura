<script setup>
import { onMounted, ref, computed, watch } from "vue";
import { useFinancialStore } from "@/stores/financial";
import { useProjectStore } from "@/stores/project";
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label'; 

const financialStore = useFinancialStore();
const projectStore = useProjectStore();
const selectedProjectId = ref('');

// Computed property para obter as transações financeiras filtradas por projeto
const filteredFinances = computed(() => {
  if (!selectedProjectId.value) {
    return financialStore.finances; // Retornar todas as movimentações se nenhum projeto for selecionado
  }
  return financialStore.finances.filter(finance => finance.project_id === selectedProjectId.value);
});

// Carregar projetos e movimentações ao montar o componente
onMounted(async () => {
  await projectStore.getAllProjects();
  await financialStore.getAllFinances();
});

// Função para deletar uma movimentação
const deleteFinance = async (id) => {
  await financialStore.deleteFinance(id);
};

// Função para filtrar movimentações por projeto
const filterByProject = () => {
  // Chamando a função para atualizar as movimentações filtradas
  // Essa abordagem garante que o `filteredFinances` seja atualizado automaticamente pela computed property
  if (selectedProjectId.value) {
    console.log(`Filtrando por projeto: ${selectedProjectId.value}`);
  } else {
    console.log("Mostrando todas as movimentações");
  }
};
</script>

<template>
  <div class="hidden h-full flex-1 flex-col space-y-8 p-8 md:flex">
    <!-- Cabeçalho -->
    <div class="flex items-center justify-between space-y-2">
      <div>
        <h2 class="text-2xl font-bold tracking-tight">Movimentações Financeiras</h2>
        <p class="text-muted-foreground">Aqui está uma lista de suas movimentações financeiras!</p>
      </div>
      <!-- Filtro por Projeto e Adicionar Nova Movimentação -->
      <div class="flex items-center space-x-4">
        <div class="flex items-center space-x-2">
          <Label for="project">Filtrar por Projeto</Label>
          <select v-model="selectedProjectId" @change="filterByProject" class="input w-full max-w-xs">
            <option value="">Todos os Projetos</option>
            <option v-for="project in projectStore.projects" :key="project.id" :value="project.id">
              {{ project.name }}
            </option>
          </select>
        </div>
        <router-link :to="{ name: 'financial' }">
          <Button class="bg-primary text-primary-foreground font-bold shadow-md hover:shadow-lg transition-all">
            Adicionar Nova Movimentação
          </Button>
        </router-link>
      </div>
    </div>

    <!-- Tabela de Movimentações -->
    <div class="bg-card dark:bg-gray-800 p-6 rounded-lg shadow-xl">
      <Table class="w-full text-left bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700">
        <TableHeader>
          <TableRow class="bg-primary dark:bg-yellow-500 text-primary-foreground">
            <TableHead class="py-3 px-4">Projeto</TableHead>
            <TableHead class="py-3 px-4">Descrição</TableHead>
            <TableHead class="py-3 px-4">Valor</TableHead>
            <TableHead class="py-3 px-4">Tipo</TableHead>
            <TableHead class="py-3 px-4">Data</TableHead>
            <TableHead class="py-3 px-4 text-center">Ações</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <!-- Verificar se há movimentações financeiras disponíveis -->
          <TableRow v-for="finance in filteredFinances" :key="finance.id" class="hover:bg-accent/30 dark:hover:bg-gray-700">
            <TableCell class="py-4 px-4">{{ finance.project?.id || 'Sem Projeto' }}</TableCell>
            <TableCell class="py-4 px-4">{{ finance.description }}</TableCell>
            <TableCell class="py-4 px-4">
              <span :class="finance.type === 'income' ? 'text-green-500' : 'text-red-500'">
                {{ finance.type === 'income' ? '+' : '-' }} {{ finance.amount }}
              </span>
            </TableCell>
            <TableCell class="py-4 px-4">{{ finance.type === 'expense' ? 'Saída' : 'Entrada' }}</TableCell>
            <TableCell class="py-4 px-4">{{ finance.transaction_date }}</TableCell>
            <TableCell class="flex justify-center space-x-2 py-4">
              <router-link :to="{ name: 'financial', params: { id: finance.id } }">
                <Button variant="ghost" class="text-blue-600">Editar</Button>
              </router-link>
              <Button variant="ghost" @click="deleteFinance(finance.id)" class="text-red-600">Excluir</Button>
            </TableCell>
          </TableRow>
        </TableBody>
        <TableCaption v-if="filteredFinances.length === 0" class="text-muted-foreground dark:text-gray-400">
          Nenhuma movimentação cadastrada ainda.
        </TableCaption>
      </Table>
    </div>
  </div>
</template>
