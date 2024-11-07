<script setup>
import { useAuthStore } from "@/stores/auth";
import { storeToRefs } from "pinia";
import { onMounted, reactive } from "vue";
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { EnvelopeClosedIcon, LockClosedIcon, PersonIcon, PaperPlaneIcon, ExclamationTriangleIcon } from '@radix-icons/vue';

const { errors } = storeToRefs(useAuthStore());
const { authenticate } = useAuthStore();

const formData = reactive({
  name: "",
  email: "",
  phone: "",
  password: "",
  password_confirmation: "",
});

onMounted(() => (errors.value = {}));

const submit = () => {
  authenticate('register', formData);
};
</script>

<template>
  <main class="h-full w-full flex flex-col items-center justify-center relative p-20">
    <!-- Mensagens de Erro -->
    <Alert v-if="errors.value && Object.keys(errors.value).length" variant="destructive" class="max-w-md shadow-sm mb-4">
      <ExclamationTriangleIcon class="w-4 h-4" />
      <AlertTitle>Algo deu errado!</AlertTitle>
      <AlertDescription v-for="(error, i) in errors.value" :key="i">
          {{ error }}
      </AlertDescription>
    </Alert>

    <!-- Formulário -->
    <Card class="max-w-md shadow-xl">
      <CardHeader>
        <CardTitle class="text-2xl font-bold tracking-tighter">Cadastrando nova conta</CardTitle>
        <CardDescription>
          Já possui conta?
          <RouterLink :to="{ name: 'login' }">
            <Button variant="link">Entrar</Button>
          </RouterLink>
        </CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="submit">
          <div class="flex items-center space-x-2 mb-1">
            <PersonIcon class="w-5 h-5 text-muted-foreground" />
            <Label htmlFor="name">Nome</Label>
          </div>
          <Input
            id="name"
            placeholder="Seu nome completo"
            type="text"
            v-model="formData.name"
          />

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
            <PaperPlaneIcon class="w-5 h-5 text-muted-foreground" />
            <Label htmlFor="phone">Telefone</Label>
          </div>
          <Input
            id="phone"
            placeholder="(XX) XXXXX-XXXX"
            type="tel"
            v-model="formData.phone"
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

          <div class="mt-4 flex items-center space-x-2 mb-1">
            <LockClosedIcon class="w-5 h-5 text-muted-foreground" />
            <Label htmlFor="confirmPassword">Confirme sua Senha</Label>
          </div>
          <Input
            id="confirmPassword"
            placeholder="Confirme sua senha"
            type="password"
            v-model="formData.password_confirmation"
          />

          <Button class="mt-6 w-full">Cadastrar-se</Button>
        </form>
      </CardContent>
      <CardFooter>
        <p class="text-center text-sm text-muted-foreground">
          Ao entrar em nossa plataforma você concorda com nossos Termos de Uso
          e Política de Privacidade.
        </p>
      </CardFooter>
    </Card>
  </main>
</template>