<script setup>
import { useForm } from "@inertiajs/vue3";
import { Button } from '@/components/ui/button';
import { RocketIcon } from '@radix-icons/vue'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'

const form = useForm({});

defineProps({
    status: String,
});

const submit = () => {
    form.post(route("verification.send"));
};
</script>

<template>
    <div>
        <div class="mb-8">
            <p>
                Obrigado por se registar! Antes de começar, por favor 
                verifique o seu endereço de e-mail clicando no link que acabámos de enviar. 
                Caso não tenha recebido o e-mail, teremos prazer em enviar-lhe outro.
            </p>
        </div>

        <Alert v-if="status" variant="success" class="shadow-lg">
          <RocketIcon class="h-4 w-4" />
          <AlertTitle>Atenção!</AlertTitle>
          <AlertDescription>
            {{ status }}
          </AlertDescription>
        </Alert>

        <form @submit.prevent="submit">
            <Button class="mt-6 w-full" :disabled="form.processing">
                Reenviar e-mail de verificação
            </Button>
        </form>
    </div>
</template>
