<script setup>
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'

import { EnvelopeClosedIcon, LockClosedIcon, PersonIcon, PaperPlaneIcon, ExclamationTriangleIcon } from '@radix-icons/vue';

import { useForm } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    email: "",
    phone: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {    
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <main class="h-full w-full flex flex-col items-center justify-center">
      <Alert v-if="Object.keys(form.errors).length" variant="destructive" class="max-w-md shadow-sm mb-4">
        <ExclamationTriangleIcon class="w-4 h-4" />
        <AlertTitle>Algo deu errado!</AlertTitle>
        <AlertDescription v-for="(error, i) in form.errors" :key="i">
            {{ error }}
        </AlertDescription>
      </Alert>
      <Card class="max-w-md shadow-xl">
        <CardHeader>
          <CardTitle class="text-2xl font-bold tracking-tighter">Cadastrando nova conta</CardTitle>
          <CardDescription>Já possui conta?<Button variant="link" :href="route('login')">Entrar</Button></CardDescription>
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
              v-model="form.name"
            />
  
            <div class="mt-4 flex items-center space-x-2 mb-1">
              <EnvelopeClosedIcon class="w-5 h-5 text-muted-foreground" />
              <Label htmlFor="email">E-mail</Label>
            </div>
            <Input
              id="email"
              placeholder="exemplo@email.com"
              type="email"
              v-model="form.email"
            />
  
            <div class="mt-4 flex items-center space-x-2 mb-1">
              <PaperPlaneIcon class="w-5 h-5 text-muted-foreground" />
              <Label htmlFor="phone">Telefone</Label>
            </div>
            <Input
              id="phone"
              placeholder="(XX) XXXXX-XXXX"
              type="tel"
              v-model="form.phone"
            />
  
            <div class="mt-4 flex items-center space-x-2 mb-1">
              <LockClosedIcon class="w-5 h-5 text-muted-foreground" />
              <Label htmlFor="password">Senha</Label>
            </div>
            <Input
              id="password"
              placeholder="Sua senha secreta"
              type="password"
              v-model="form.password"
            />
  
            <div class="mt-4 flex items-center space-x-2 mb-1">
              <LockClosedIcon class="w-5 h-5 text-muted-foreground" />
              <Label htmlFor="confirmPassword">Confirme sua Senha</Label>
            </div>
            <Input
              id="confirmPassword"
              placeholder="Confirme sua senha"
              type="password"
              v-model="form.password_confirmation"
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
  
