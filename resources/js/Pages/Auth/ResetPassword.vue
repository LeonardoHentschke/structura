<script setup>
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { EnvelopeClosedIcon, LockClosedIcon, ExclamationTriangleIcon } from '@radix-icons/vue';


import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    token: String,
    email: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("password.update"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <div>
        <div class="mb-8 text-center">
            <p>Digite sua nova senha</p>
        </div>

        <!-- Errors messages -->
        <Alert v-if="Object.keys(form.errors).length" variant="destructive" class="max-w-md shadow-sm mb-4">
        <ExclamationTriangleIcon class="w-4 h-4" />
        <AlertTitle>Algo deu errado!</AlertTitle>
        <AlertDescription v-for="(error, i) in form.errors" :key="i">
            {{ error }}
        </AlertDescription>
        </Alert>

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

            <Button :disabled="form.processing" class="mt-4">
                Redefinir senha
            </Button>
        </form>
    </div>
</template>