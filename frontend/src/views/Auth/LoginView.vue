<script setup>
import { useAuthStore } from "@/stores/auth";
import { storeToRefs } from "pinia";
import { onMounted, reactive, ref } from "vue";

import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { EnvelopeClosedIcon, LockClosedIcon, ExclamationTriangleIcon } from '@radix-icons/vue';
import { Checkbox } from '@/components/ui/checkbox';
import Loader from '@/components/Loader.vue'; // Importa o Loader

const { errors } = storeToRefs(useAuthStore());
const { authenticate } = useAuthStore();

const formData = reactive({
  email: "",
  password: "",
  remember: null,
});

const isLoading = ref(false); // Define o estado do loader

onMounted(() => (errors.value = {}));

const submit = async () => {
  isLoading.value = true; // Inicia o loader
  try {
    await authenticate('login', formData);
  } catch (error) {
    console.error("Erro no login:", error);
  } finally {
    isLoading.value = false; // Desativa o loader após a tentativa de login
  }
};
</script>

<template>
  <main class="flex w-full">    
    <!-- Loader Condicional -->
    <Loader v-if="isLoading" />

    <!-- Formulário de Login -->
    <section class="flex h-full w-full items-center justify-center bg-background p-20">
      <!-- Mensagens de Erro -->
      <Alert v-if="errors.value && Object.keys(errors.value).length" variant="destructive" class="max-w-md shadow-sm mb-4">
        <ExclamationTriangleIcon class="w-4 h-4" />
        <AlertTitle>Algo deu errado!</AlertTitle>
        <AlertDescription v-for="(error, i) in errors.value" :key="i">
            {{ error }}
        </AlertDescription>
      </Alert>
      
      <!-- Card de Login -->
      <Card class="max-w-md shadow-xl">
        <CardHeader>
          <CardTitle class="text-2xl font-bold tracking-tighter">Entre com sua conta</CardTitle>
          <CardDescription>Não possui uma conta ainda?
            <RouterLink :to="{ name: 'register' }">
              <Button variant="link">Cadastrar-se</Button>
            </RouterLink>
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit">
            <div class="mt-4 flex items-center space-x-2 mb-1">
              <EnvelopeClosedIcon class="w-5 h-5 text-muted-foreground" />
              <Label htmlFor="email">E-mail</Label>
            </div>
            <Input
              id="email"
              placeholder="exemplo@email.com"
              type="email"
              v-model="formData.email"
            />
            <div class="mt-4 flex items-center space-x-2 mb-1">
              <LockClosedIcon class="w-5 h-5 text-muted-foreground" />
              <Label htmlFor="password">Senha</Label>
            </div>
            <Input
              id="password"
              placeholder="Sua senha secreta"
              type="password"
              v-model="formData.password"
            />
          
            <div class="mt-4 flex items-center justify-between space-x-2">
              <div>
                <Checkbox id="remember" v-model="formData.remember"/>
                <label
                  for="remember"
                  class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                >
                  Lembrar senha
                </label>
              </div>
              <Button variant="link" :to="{ name: 'password.request' }">Esqueceu a senha?</Button>
            </div>
            <Button class="mt-6 w-full bg-primary text-primary-foreground dark:bg-yellow-500 dark:text-primary-foreground hover:bg-secondary hover:text-white transform hover:scale-105 transition-transform duration-300 shadow-md">
              Entrar
            </Button>
          </form>
        </CardContent>
        <CardFooter>
          <p class="text-center text-sm text-muted-foreground">
            Ao entrar em nossa plataforma você concorda com nossos Termos de Uso
            e Política de Privacidade.
          </p>
        </CardFooter>
      </Card>
    </section>
  </main>
</template>
