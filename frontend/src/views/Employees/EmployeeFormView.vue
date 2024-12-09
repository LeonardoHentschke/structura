<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useEmployeeStore } from "@/stores/employee";
import { useAuthStore } from "@/stores/auth"; // Importar o store de autenticação
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { Label } from "@/components/ui/label";

const formData = ref({
  name: "",
  email: "",
  cpf: "",
  phone: "",
  password: "",
  password_confirmation: "",
});

const loading = ref(false);
const isUpdateMode = ref(false);

const route = useRoute();
const router = useRouter();
const employeeStore = useEmployeeStore();
const authStore = useAuthStore(); // Para autenticação

const fetchEmployee = async () => {
  if (route.params.id) {
    loading.value = true;
    const employee = await employeeStore.getEmployee(route.params.id);
    formData.value = {
      name: employee.name,
      email: employee.email,
      cpf: employee.cpf,
      phone: employee.phone,
      password: "", // Campos de senha vazios
      password_confirmation: "",
    };
    isUpdateMode.value = true;
    loading.value = false;
  }
};

const submitForm = async () => {
  loading.value = true;

  try {
    if (isUpdateMode.value) {
      // Atualiza os dados do funcionário
      await employeeStore.updateEmployee(route.params.id, formData.value);
    } else {
      // Cria o funcionário
      await employeeStore.createEmployee({
        name: formData.value.name,
        email: formData.value.email,
        cpf: formData.value.cpf,
        phone: formData.value.phone,
      });

      // Registra o usuário
      await authStore.authenticate("register", {
        name: formData.value.name,
        email: formData.value.email,
        password: formData.value.password,
        password_confirmation: formData.value.password_confirmation,
      });

      console.log("Funcionário e usuário criados com sucesso!");
    }

    // Redireciona para a lista de funcionários
    router.push({ name: "employeeList" });
  } catch (error) {
    console.error("Erro ao cadastrar funcionário e usuário:", error);
    if (authStore.errors) {
      console.error("Detalhes do erro:", authStore.errors);
    }
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchEmployee();
});
</script>

<template>
  <div class="hidden space-y-6 p-10 pb-16 md:block dark:bg-gray-900">
    <Card>
      <CardHeader>
        <CardTitle>{{ isUpdateMode ? "Editar Funcionário" : "Cadastrar Funcionário" }}</CardTitle>
        <CardDescription>Gerencie as informações de um funcionário</CardDescription>
        <Separator class="my-3" />
      </CardHeader>
      <form @submit.prevent="submitForm">
      <CardContent>
        <div class="grid grid-cols-1 gap-4 w-full">
          <!-- Campos existentes -->
          <div class="flex flex-col space-y-1.5">
            <Label for="name">Nome</Label>
            <input
              id="name"
              type="text"
              v-model="formData.name"
              class="input w-full h-10 px-3 text-sm border border-gray-300 rounded-md bg-transparent dark:bg-gray-800 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:text-white"
              placeholder="Digite o nome do funcionário"
            />
          </div>
          <div class="flex flex-col space-y-1.5">
            <Label for="email">Email</Label>
            <input
              id="email"
              type="email"
              v-model="formData.email"
              class="input w-full h-10 px-3 text-sm border border-gray-300 rounded-md bg-transparent dark:bg-gray-800 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:text-white"
              placeholder="Digite o email do funcionário"
            />
          </div>
          <div class="flex flex-col space-y-1.5">
            <Label for="cpf">CPF</Label>
            <input
              id="cpf"
              type="text"
              v-model="formData.cpf"
              class="input w-full h-10 px-3 text-sm border border-gray-300 rounded-md bg-transparent dark:bg-gray-800 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:text-white"
              placeholder="Digite o CPF do funcionário"
            />
          </div>
          <div class="flex flex-col space-y-1.5">
            <Label for="phone">Telefone</Label>
            <input
              id="phone"
              type="text"
              v-model="formData.phone"
              class="input w-full h-10 px-3 text-sm border border-gray-300 rounded-md bg-transparent dark:bg-gray-800 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:text-white"
              placeholder="Digite o telefone do funcionário"
            />
          </div>

          <!-- Campos de senha -->
          <div class="flex flex-col space-y-1.5">
            <Label for="password">Senha</Label>
            <input
              id="password"
              type="password"
              v-model="formData.password"
              class="input w-full h-10 px-3 text-sm border border-gray-300 rounded-md bg-transparent dark:bg-gray-800 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:text-white"
              placeholder="Digite a senha"
            />
          </div>
          <div class="flex flex-col space-y-1.5">
            <Label for="password_confirmation">Confirmar Senha</Label>
            <input
              id="password_confirmation"
              type="password"
              v-model="formData.password_confirmation"
              class="input w-full h-10 px-3 text-sm border border-gray-300 rounded-md bg-transparent dark:bg-gray-800 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:text-white"
              placeholder="Confirme a senha"
            />
          </div>
        </div>
      </CardContent>
      <div class="flex justify-end p-4">
        <Button :disabled="loading">{{ isUpdateMode ? "Atualizar" : "Cadastrar" }}</Button>
      </div>
    </form>
    </Card>
  </div>
</template>
