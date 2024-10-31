<script setup>
import { onMounted } from "vue";
import { useProjectStore } from "@/stores/project";
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { DotsHorizontalIcon } from "@radix-icons/vue";

const projectStore = useProjectStore();

const deleteProject = (id) => {
  projectStore.deleteProject(id);
};

onMounted(projectStore.getAllProjects);
</script>

<template>
  <div class="hidden h-full flex-1 flex-col space-y-8 p-8 md:flex">
    <div class="flex items-center justify-between space-y-2">
      <div>
        <h2 class="text-2xl font-bold tracking-tight">Projetos!</h2>
        <p class="text-muted-foreground">Aqui está uma lista de seus projetos!</p>
      </div>
      <div class="flex items-center space-x-2">
        <Button>
          <router-link :to="{ name: 'project' }">Adicionar novo projeto</router-link>
        </Button>
      </div>
    </div>

    <Table>
      <TableCaption v-if="!projectStore.projects.length > 0">Nenhum projeto cadastrado ainda.</TableCaption>
      <TableHeader>
        <TableRow>
          <TableHead>ID</TableHead>
          <TableHead>Nome</TableHead>
          <TableHead>Email</TableHead>
          <TableHead>Telefone</TableHead>
          <TableHead>CPF</TableHead>
          <TableHead>Ações</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="client in projectStore.projects" :key="client.id">
          <TableCell>{{ client.id }}</TableCell>
          <TableCell class="font-medium">{{ client.name }}</TableCell>
          <TableCell>{{ client.email }}</TableCell>
          <TableCell>{{ client.phone }}</TableCell>
          <TableCell>{{ client.cpf }}</TableCell>
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
                    <router-link :to="{ name: 'client', params: { id: client.id } }">Editar</router-link>
                  </Button>
                </DropdownMenuItem>
                <DropdownMenuItem>
                  <Button variant="ghost" @click="deleteClient(client.id)">Excluir</Button>
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </div>
</template>