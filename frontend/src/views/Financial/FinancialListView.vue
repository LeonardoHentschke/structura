<script setup>
import { onMounted } from "vue";
import { useFinancialStore } from "@/stores/financial";
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { DotsHorizontalIcon } from "@radix-icons/vue";

const financialStore = useFinancialStore();

const deleteFinance = (id) => {
  financialStore.deleteFinance(id);
};

onMounted(financialStore.getAllFinances);
</script>

<template>
  <div class="hidden h-full flex-1 flex-col space-y-8 p-8 md:flex">
    <div class="flex items-center justify-between space-y-2">
      <div>
        <h2 class="text-2xl font-bold tracking-tight">Movimentações Financeiras</h2>
        <p class="text-muted-foreground">Aqui está uma lista de suas movimentações financeiras!</p>
      </div>
      <div class="flex items-center space-x-2">
        <Button>
          <router-link :to="{ name: 'financial' }">Adicionar nova movimentação</router-link>
        </Button>
      </div>
    </div>

    <Table>
      <TableCaption v-if="!financialStore.finances.length > 0">Nenhuma movimentação cadastrada ainda.</TableCaption>
      <TableHeader>
        <TableRow>
          <TableHead>ID</TableHead>
          <TableHead>Descrição</TableHead>
          <TableHead>Valor</TableHead>
          <TableHead>Data</TableHead>
          <TableHead>Tipo</TableHead>
          <TableHead>Categoria</TableHead>
          <TableHead>Ações</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="finance in financialStore.finances" :key="finance.id">
          <TableCell>{{ finance.id }}</TableCell>
          <TableCell>{{ finance.description }}</TableCell>
          <TableCell>{{ finance.amount }}</TableCell>
          <TableCell>{{ finance.date }}</TableCell>
          <TableCell>{{ finance.type }}</TableCell>
          <TableCell>{{ finance.category }}</TableCell>
          <TableCell>
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button variant="ghost" class="flex h-8 w-8 p-0 data-[state=open]:bg-muted">
                  <DotsHorizontalIcon class="h-4 w-4" />
                  <span class="sr-only">Abrir menu</span>
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent class="w-[160px]">
                <DropdownMenuItem>
                  <Button variant="ghost">
                    <router-link :to="{ name: 'financial', params: { id: finance.id } }">Editar</router-link>
                  </Button>
                </DropdownMenuItem>
                <DropdownMenuItem>
                  <Button variant="ghost" @click="deleteFinance(finance.id)">Excluir</Button>
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </div>
</template>
