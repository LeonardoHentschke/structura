<script setup>
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox'
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from '@/components/ui/carousel';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { EnvelopeClosedIcon, LockClosedIcon, ExclamationTriangleIcon } from '@radix-icons/vue';

import { useForm } from "@inertiajs/vue3";

const form = useForm({
    email: "",
    password: "",
    remember: null,
});

const submit = () => {  
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
  <main class="flex w-full">
    <div class="flex h-full w-full cursor-grab items-center justify-center bg-background p-16">
      <Carousel class="w-full max-w-xl">
        <CarouselContent>
          <CarouselItem>
            <div class="flex aspect-square rounded p-8">
              <img
                src="https://vj-parafusos.com.br/wp-content/uploads/2019/11/epi-fundo-transparente.png"
                alt="Dunk"
              />
            </div>
          </CarouselItem>
          <CarouselItem>
            <div class="flex aspect-square rounded p-8">
              <img
                src="https://vj-parafusos.com.br/wp-content/uploads/2019/11/epi-fundo-transparente.png"
                alt="Court"
              />
            </div>
          </CarouselItem>
        </CarouselContent>
        <CarouselPrevious />
        <CarouselNext />
      </Carousel>
    </div>
    <section class="flex flex-col h-full w-full max-w-3xl items-center justify-center bg-background p-4">
      <Alert v-if="Object.keys(form.errors).length" variant="destructive" class="max-w-md shadow-sm mb-4">
        <ExclamationTriangleIcon class="w-4 h-4" />
        <AlertTitle>Algo deu errado!</AlertTitle>
        <AlertDescription v-for="(error, i) in form.errors" :key="i">
            {{ error }}
        </AlertDescription>
      </Alert>
      <Card>
        <CardHeader>
          <CardTitle class="text-2xl font-bold tracking-tighter">Entre com sua conta</CardTitle>
          <CardDescription>Precisa de uma conta?
            <Button variant="link" :href="route('register')">Cadastrar-se</Button>
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
              v-model="form.email"
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
          
          <div class="mt-4 flex items-center justify-between space-x-2">
            <div>
              <Checkbox id="remember" v-model="form.remember"/>
              <label
                for="remember"
                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
              >
                Lembrar senha
              </label>
            </div>
            <Button variant="link" :href="route('password.request')">Esqueceu a senha?</Button>
          </div>
          <Button class="mt-6 w-full">Entrar</Button>
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


