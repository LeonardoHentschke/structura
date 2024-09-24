<script setup>
import { router, useForm } from "@inertiajs/vue3";
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from '@/components/ui/tabs'
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'

const props = defineProps({
  clients: Array
});

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
      console.error(errors); // Loga os erros no console
    },
  });
};
</script>

<template>
  <Tabs default-value="show">
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

            <!-- Data de nascimento -->
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

            <!-- Endereços -->
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
                <Button @click="addAddress" variant="secondary"
                  >Adicionar mais um endereço</Button
                >
              </div>
            </div>
          </CardContent>
          <CardFooter>
            <Button class="w-full">Salvar</Button>
          </CardFooter>
        </form>
      </Card>
    </TabsContent>
  </Tabs>
</template>
