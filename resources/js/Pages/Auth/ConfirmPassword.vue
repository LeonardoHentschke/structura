<script setup>
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { LockClosedIcon, ExclamationTriangleIcon } from '@radix-icons/vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';

import { useForm } from "@inertiajs/vue3";

const form = useForm({
    password: "",
});

const submit = () => {
    form.post(route("password.confirm"), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <div class="mb-8 text-center">
        <p>
            Esta é uma área segura do aplicativo. Confirme sua
            senha antes de continuar.
        </p>
    </div>

    <Alert v-if="Object.keys(form.errors).length" variant="destructive" class="shadow-sm mb-4">
        <ExclamationTriangleIcon class="w-4 h-4" />
        <AlertTitle>Algo deu errado!</AlertTitle>
        <AlertDescription v-for="(error, i) in form.errors" :key="i">
            {{ error }}
        </AlertDescription>
    </Alert>

    <form @submit.prevent="submit">
        <div class="flex items-center space-x-2 mb-1">
            <LockClosedIcon class="w-5 h-5 text-muted-foreground" />
            <Label htmlFor="password">Senha</Label>
        </div>
        <Input
            id="password"
            placeholder="Sua senha"
            type="password"
            v-model="form.password"
        />

        <Button class="mt-5 w-full" :disabled="form.processing">Confirmar</Button>
    </form>
</template>