<script setup>
import { router, useForm } from "@inertiajs/vue3";
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'

const props = defineProps({
  clients: Array
});

const data = ref(props.clients);
console.log("Received clients:", props.clients);

const form = useForm({
  name: '',
  cpf: '',
  birth_date: '',
  addresses: [{ street: '', number: '', city: '', latitude: null, longitude: null }],
});

const addAddress = () => {
  form.addresses.push({ street: '', number: '', city: '', latitude: null, longitude: null });
};

const submitForm = () => {
  form.post(route("clients"), {
    preserveScroll: true,
    onError: (errors) => {
      console.error(errors);
    },
  });
};

import { DropdownMenu, DropdownMenuCheckboxItem, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import DropdownAction from '@/components/DataTableDropDown.vue'

import { ChevronDownIcon } from '@radix-icons/vue'
import {
  FlexRender,
  getCoreRowModel,
  getExpandedRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import { h, ref } from 'vue'

const columns = [
  {
    accessorKey: 'name',
    header: 'Nome',
    cell: ({ row }) => h('div', { class: 'capitalize' }, row.getValue('name')),
  },
  {
    accessorKey: 'cpf',
    header: 'CPF',
    cell: ({ row }) => h('div', { class: 'capitalize' }, row.getValue('cpf')),
  },
  {
    id: 'actions',
    enableHiding: false,
    cell: ({ row }) => {
      const client = row.original

      return h('div', { class: 'relative' }, h(DropdownAction, {
        client,
      }))
    },
  }
]

const table = useVueTable({
  data,
  columns,
  getCoreRowModel: getCoreRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getExpandedRowModel: getExpandedRowModel(),
})

</script>

<template>

<div class="w-full">
    <div class="flex items-center py-4">
      <Input
        class="max-w-sm"
        placeholder="Filtre por nome..."
        :model-value="table.getColumn('name')?.getFilterValue()"
        @update:model-value=" table.getColumn('name')?.setFilterValue($event)"
      />
      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <Button variant="outline" class="ml-auto">
            Colunas <ChevronDownIcon class="ml-2 h-4 w-4" />
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent>
          <DropdownMenuCheckboxItem
            v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
            :key="column.id"
            class="capitalize"
            :checked="column.getIsVisible()"
            @update:checked="(value) => {
              column.toggleVisibility(!!value)
            }"
          >
            {{ column.columnDef.header }}
          </DropdownMenuCheckboxItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
    <div class="rounded-md border">
      <Table>
        <TableHeader>
          <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
            <TableHead v-for="header in headerGroup.headers" :key="header.id">
              <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header" :props="header.getContext()" />
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="table.getRowModel().rows?.length">
            <template v-for="row in table.getRowModel().rows" :key="row.id">
              <TableRow :data-state="row.getIsSelected() && 'selected'">
                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                  <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                </TableCell>
              </TableRow>
              <TableRow v-if="row.getIsExpanded()">
                <TableCell :colspan="row.getAllCells().length">
                  {{ JSON.stringify(row.original) }}
                </TableCell>
              </TableRow>
            </template>
          </template>

          <TableRow v-else>
            <TableCell
              :colspan="columns.length"
              class="h-24 text-center"
            >
              Sem resultados
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>

    <div class="flex items-center justify-end space-x-2 py-4">
      <div class="flex-1 text-sm text-muted-foreground">
        {{ table.getFilteredSelectedRowModel().rows.length }} of
        {{ table.getFilteredRowModel().rows.length }} linha(s) selecionadas.
      </div>
      <div class="space-x-2">
        <Button
          variant="outline"
          size="sm"
          :disabled="!table.getCanPreviousPage()"
          @click="table.previousPage()"
        >
          Anterior
        </Button>
        <Button
          variant="outline"
          size="sm"
          :disabled="!table.getCanNextPage()"
          @click="table.nextPage()"
        >
          Próxima
        </Button>
      </div>
    </div>
  </div>

  <!-- <Tabs default-value="show">
    <TabsList class="grid w-full grid-cols-2">
      <TabsTrigger value="show"> Lista de clientes </TabsTrigger>
      <TabsTrigger value="store"> Cadastrar novo </TabsTrigger>
    </TabsList>
    <TabsContent value="show">
      <Card>
        <CardHeader>
          <CardTitle>Clientes</CardTitle>
          <CardDescription>Lista de clientes</CardDescription>
        </CardHeader>
        <CardContent class="space-y-2">
          <Table>
            <TableCaption>Lista de clientes cadastrados.</TableCaption>
            <TableHeader>
              <TableRow>
                <TableHead>Nome</TableHead>
                <TableHead>CPF</TableHead>
                <TableHead>Data de Nascimento</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="client in clients" :key="client.id">
                <TableCell class="font-medium">{{ client.name }}</TableCell>
                <TableCell>{{ client.cpf }}</TableCell>
                <TableCell>{{ client.birth_date }}</TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </CardContent>
        <CardFooter>
          <p>Total: {{ clients.length }} clientes</p>
        </CardFooter>
      </Card>
    </TabsContent>
    <TabsContent value="store">
      <Card>
        <CardHeader>
          <CardTitle>Cadastrar Cliente</CardTitle>
        </CardHeader>
        <form @submit.prevent="submitForm">
          <CardContent class="space-y-2">
            <div class="mb-4">
              <Label for="name" class="block text-sm font-medium">Nome</Label>
              <Input
                v-model="form.name"
                id="name"
                type="text"
                placeholder="Digite o nome"
                required
              />
            </div>
            <div class="mb-4">
              <Label for="cpf" class="block text-sm font-medium">CPF</Label>
              <Input
                v-model="form.cpf"
                id="cpf"
                type="text"
                placeholder="Digite o CPF"
                required
              />
            </div>

            <div class="mb-4">
              <Label for="birth_date" class="block text-sm font-medium"
                >Data de Nascimento</Label
              >
              <Input
                v-model="form.birth_date"
                id="birth_date"
                type="date"
                required
              />
            </div>

            <div class="mb-4">
              <div
                v-for="(address, index) in form.addresses"
                :key="index"
                class="mb-4 border-b pb-2"
              >
                <div class="mb-2">
                  <Label class="block text-sm">Rua</Label>
                  <Input
                    v-model="address.street"
                    type="text"
                    placeholder="Digite a rua"
                    required
                  />
                </div>
                <div class="mb-2">
                  <Label class="block text-sm">Número</Label>
                  <Input
                    v-model="address.number"
                    type="text"
                    placeholder="Digite o número"
                    required
                  />
                </div>
                <div class="mb-2">
                  <Label class="block text-sm">Cidade</Label>
                  <Input
                    v-model="address.city"
                    type="text"
                    placeholder="Digite a cidade"
                    required
                  />
                </div>
                <div class="mb-2">
                  <Label class="block text-sm">Latitude</Label>
                  <Input
                    v-model="address.latitude"
                    type="number"
                    placeholder="Digite a latitude (opcional)"
                  />
                </div>
                <div class="mb-2">
                  <Label class="block text-sm">Longitude</Label>
                  <Input
                    v-model="address.longitude"
                    type="number"
                    placeholder="Digite a longitude (opcional)"
                  />
                </div>
              </div>
              <div class="flex justify-end mt-2">
                <Button @click="addAddress" variant="secondary">
                  Adicionar mais um endereço
                </Button>
              </div>
            </div>
          </CardContent>
          <CardFooter>
            <Button class="w-full">Salvar</Button>
          </CardFooter>
        </form>
      </Card>
    </TabsContent>
  </Tabs> -->
</template>
