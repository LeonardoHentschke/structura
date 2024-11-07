<script setup>
import { useAuthStore } from "@/stores/auth";
import { storeToRefs } from "pinia";
import { onMounted, reactive } from "vue";

import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'

const { errors } = storeToRefs(useAuthStore());
const { authenticate } = useAuthStore();

const formData = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

onMounted(() => (errors.value = {}));
</script>

<template>
  <main class="flex h-screen w-full" style="height: calc(100vh - 81px);">
    <div className="flex h-full w-full cursor-grab items-center justify-center bg-primary-foreground p-16">
    </div>
    <section class="flex h-full w-full max-w-3xl items-center justify-center bg-background p-4">
      <Card class="w-full max-w-md p-6">
        <CardHeader>
          <CardTitle>Bem-vindo à nossa plataforma!</CardTitle>
          <CardDescription>Crie sua nova conta e comece a explorar todos os nossos recursos.</CardDescription>
        </CardHeader>
        <form @submit.prevent="authenticate('register', formData)">
          <CardContent>
              <div class="grid items-center w-full gap-4">
                <div class="flex flex-col space-y-1.5">
                  <Label for="name">Nome</Label>
                  <Input id="name" placeholder="Seu nome completo" v-model="formData.name" />
                  <p v-if="errors.name" class="text-red-500 text-xs">{{ errors.name[0] }}</p>
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label for="email">Email</Label>
                  <Input id="email" placeholder="Seu email de preferencia" v-model="formData.email" />
                  <p v-if="errors.email" class="text-red-500 text-xs">{{ errors.email[0] }}</p>
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label for="password">Senha</Label>
                  <Input id="password" placeholder="Uma senha segura" type="password" v-model="formData.password" />
                  <p v-if="errors.password" class="text-red-500 text-xs">{{ errors.password[0] }}</p>
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label for="password_confirmation">Confirmar senha</Label>
                  <Input id="password_confirmation" placeholder="Confirme sua senha segura" type="password" v-model="formData.password_confirmation" />
                </div>
              </div>
          </CardContent>
          <CardFooter class="flex justify-end px-6 pb-6">
            <Button>Cadastrar</Button>
          </CardFooter>
        </form>
      </Card>
    </section>
  </main>

</template>