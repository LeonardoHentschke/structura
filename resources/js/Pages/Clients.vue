<script setup>
import { h, ref } from 'vue';
import { router, useForm } from "@inertiajs/vue3";

// UI Components
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from '@/components/ui/tabs';
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';

// Data Table Component
import DropdownAction from '@/components/DataTableDropDown.vue';

// Radix Icons
import { ChevronDownIcon } from '@radix-icons/vue';

// TanStack Table
import {
  FlexRender,
  getCoreRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  getFilteredRowModel,
  useVueTable,
} from '@tanstack/vue-table';


const props = defineProps({
  clients: Array
});

const data = ref(props.clients);
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
})

</script>

<template>
  <div class="w-full">
    <div class="flex items-center py-4">
      <Input
        class="max-w-sm"
        placeholder="Filtre por nome..."
        :model-value="table.getColumn('name')?.getFilterValue()"
        @update:model-value="table.getColumn('name')?.setFilterValue($event)"
      />
      <Dialog>
        <DialogTrigger as-child>
          <Button class="ml-auto">Novo cliente</Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[640px]">
          <DialogHeader>
            <DialogTitle>Cadastrar novo cliente</DialogTitle>
            <Separator class="my-3" />
          </DialogHeader>
          <Tabs default-value="client">
            <TabsList>
              <TabsTrigger value="client">Cliente</TabsTrigger>
              <TabsTrigger value="address">Endereço</TabsTrigger>
            </TabsList>
            <TabsContent value="client">
              <div class="grid gap-4 py-4">
                <div class="grid grid-cols-4 items-center gap-4">
                  <Label for="name" class="text-right">Nome</Label>
                  <Input id="name" v-model="form.name" class="col-span-3" placeholder="Nome do cliente" />
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                  <Label for="cpf" class="text-right">CPF</Label>
                  <Input id="cpf" v-model="form.cpf" class="col-span-3" placeholder="000.000.000-00" />
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                  <Label for="email" class="text-right">Email</Label>
                  <Input id="email" v-model="form.email" class="col-span-3" placeholder="email@exemplo.com" />
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                  <Label for="phone" class="text-right">Telefone</Label>
                  <Input id="phone" v-model="form.phone" class="col-span-3" placeholder="(00) 00000-0000" />
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                  <Label for="birth_date" class="text-right">Data de Nascimento</Label>
                  <Input id="birth_date" v-model="form.birth_date" type="date" class="col-span-3" />
                </div>
              </div>
            </TabsContent>
            <TabsContent value="address">
              <div class="grid gap-4 py-4 overflow-auto scrollbar-thin scrollbar-thumb-yellow-500 scrollbar-track-gray-200 max-h-[320px]">
                <div v-for="(address, index) in form.addresses" :key="index" class="grid gap-4">
                  <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="street" class="text-right">Rua</Label>
                    <Input id="street" v-model="address.street" class="col-span-3" />
                  </div>
                  <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="number" class="text-right">Número</Label>
                    <Input id="number" v-model="address.number" class="col-span-3" />
                  </div>
                  <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="city" class="text-right">Cidade</Label>
                    <Input id="city" v-model="address.city" class="col-span-3" />
                  </div>
                  <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="latitude" class="text-right">Latitude</Label>
                    <Input id="latitude" v-model="address.latitude" class="col-span-3" />
                  </div>
                  <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="longitude" class="text-right">Longitude</Label>
                    <Input id="longitude" v-model="address.longitude" class="col-span-3" />
                  </div>
                  <Separator class="my-3" />
                </div>
              </div>
              <div class="mt-4">
                <Button @click="addAddress" variant="secondary">
                  Adicionar mais um endereço
                </Button>
              </div>
            </TabsContent>

          </Tabs>
          <DialogFooter>
            <Button type="submit" @click="submitForm">Salvar</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
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
            @update:checked="(value) => column.toggleVisibility(!!value)"
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
              <TableRow>
                <template v-for="cell in row.getVisibleCells()" :key="cell.id">
                  <TableCell>
                    <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                  </TableCell>
                </template>
              </TableRow>
            </template>
          </template>
          <template v-else>
            <TableRow>
              <TableCell colspan="100%" class="text-center">Sem dados para exibir.</TableCell>
            </TableRow>
          </template>
        </TableBody>
      </Table>
    </div>
  </div>
</template>
