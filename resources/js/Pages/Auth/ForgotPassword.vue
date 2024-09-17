<script setup>
import { EnvelopeClosedIcon } from '@radix-icons/vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { ExclamationTriangleIcon } from '@radix-icons/vue';

import { useForm } from "@inertiajs/vue3";

const form = useForm({
    email: "",
});

defineProps({status: String})

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>
        <div class="mb-8 text-center">
            <p>
                Esqueceu sua senha? Não tem problema. Basta nos informar seu endereço de e-mail
                e nós lhe enviaremos um link para redefinição de senha que
                permitirá que você escolha uma nova senha.
            </p>
        </div>

        <Alert v-if="Object.keys(form.errors).length" variant="destructive" class="max-w-md shadow-sm mb-4">
            <ExclamationTriangleIcon class="w-4 h-4" />
            <AlertTitle>Algo deu errado!</AlertTitle>
            <AlertDescription v-for="(error, i) in form.errors" :key="i">
                {{ error }}
            </AlertDescription>
        </Alert>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-500">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div class="flex items-center space-x-2 mb-1">
              <EnvelopeClosedIcon class="w-5 h-5 text-muted-foreground" />
              <Label htmlFor="email">E-mail</Label>
            </div>
            <Input
              id="email"
              placeholder="exemplo@email.com"
              type="email"
              v-model="form.email"
            />

            <Button class="mt-6 w-full" :disabled="form.processing">Enviar link de redefinição de senha</Button>
        </form>
</template>