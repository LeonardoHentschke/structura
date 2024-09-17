<script setup>
import { ref, computed } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { EnvelopeClosedIcon, LockClosedIcon, PersonIcon } from '@radix-icons/vue';

// Props
const props = defineProps({
    user: Object,
    status: String,
});

// Form de atualização de informações
const formUpdateInfo = useForm({
    name: props.user.name,
    email: props.user.email,
});

// Form de atualização de senha
const formUpdatePassword = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

// Form de exclusão de conta
const formDeleteAccount = useForm({
    password: "",
});

// Estado de exibição de confirmação de senha
const showConfirmPassword = ref(false);

// Computed para verificar se o e-mail foi verificado
const isEmailVerified = computed(() => props.user.email_verified_at === null);

// Função para reenviar e-mail de verificação
const resendEmail = (e) => {
    router.post(route("verification.send"), {}, {
        onStart: () => (e.target.disabled = true),
        onFinish: () => (e.target.disabled = false),
    });
};

// Submissão da atualização de senha
const submitUpdatePassword = () => {
    formUpdatePassword.put(route("profile.password"), {
        onSuccess: () => formUpdatePassword.reset(),
        onError: () => formUpdatePassword.reset(),
        preserveScroll: true,
    });
};

// Submissão de exclusão de conta
const submitDeleteAccount = () => {
    formDeleteAccount.delete(route("profile.destroy"), {
        onFinish: () => formDeleteAccount.reset(),
        preserveScroll: true,
    });
};
</script>

<template>
  <!-- Atualização de Informações -->
  <Card>
    <CardHeader>
      <CardTitle>Atualizar informações</CardTitle>
      <CardDescription>
        Atualize as informações de perfil e o endereço de e-mail de sua conta.
      </CardDescription>
    </CardHeader>
    <form @submit.prevent="formUpdateInfo.patch(route('profile.info'))">
      <CardContent>
        <div class="flex items-center space-x-2 mb-1">
          <PersonIcon class="w-5 h-5 text-muted-foreground" />
          <Label htmlFor="name">Nome</Label>
        </div>
        <Input
          id="name"
          placeholder="Seu nome completo"
          type="text"
          v-model="formUpdateInfo.name"
        />

        <div class="mt-4 flex items-center space-x-2 mb-1">
          <EnvelopeClosedIcon class="w-5 h-5 text-muted-foreground" />
          <Label htmlFor="email">E-mail</Label>
        </div>
        <Input
          id="email"
          placeholder="exemplo@email.com"
          type="email"
          v-model="formUpdateInfo.email"
        />

        <!-- Verificação de E-mail -->
        <div v-if="isEmailVerified">
          <p>
            Seu endereço de e-mail não foi verificado.
            <Button @click="resendEmail" variant="link">Clique aqui para reenviar o e-mail de verificação.</Button>
          </p>
        </div>
      </CardContent>
      <CardFooter class="flex justify-end">
        <Button :disabled="formUpdateInfo.processing">Salvar</Button>
      </CardFooter>
    </form>
  </Card>

  <!-- Atualização de Senha -->
  <Card>
    <CardHeader>
      <CardTitle>Atualizar senha</CardTitle>
      <CardDescription>
        Certifique-se de que você esteja usando uma senha longa e aleatória para se manter seguro.
      </CardDescription>
    </CardHeader>
    <form @submit.prevent="submitUpdatePassword">
      <CardContent>
        <div class="flex items-center space-x-2 mb-1">
          <LockClosedIcon class="w-5 h-5 text-muted-foreground" />
          <Label htmlFor="current_password">Senha Atual</Label>
        </div>
        <Input
          id="current_password"
          placeholder="Sua senha atual"
          type="password"
          v-model="formUpdatePassword.current_password"
        />

        <div class="mt-4 flex items-center space-x-2 mb-1">
          <LockClosedIcon class="w-5 h-5 text-muted-foreground" />
          <Label htmlFor="new_password">Nova Senha</Label>
        </div>
        <Input
          id="new_password"
          placeholder="Sua nova senha"
          type="password"
          v-model="formUpdatePassword.password"
        />

        <div class="mt-4 flex items-center space-x-2 mb-1">
          <LockClosedIcon class="w-5 h-5 text-muted-foreground" />
          <Label htmlFor="password_confirmation">Confirme sua Nova Senha</Label>
        </div>
        <Input
          id="password_confirmation"
          placeholder="Confirme sua nova senha"
          type="password"
          v-model="formUpdatePassword.password_confirmation"
        />

        <p v-if="formUpdatePassword.recentlySuccessful" class="text-green-500 font-medium">Salvo!</p>
      </CardContent>
      <CardFooter class="flex justify-end">
        <Button :disabled="formUpdatePassword.processing">Salvar</Button>
      </CardFooter>
    </form>
  </Card>

  <!-- Exclusão de Conta -->
  <Card>
    <CardHeader>
      <CardTitle>Excluir conta</CardTitle>
      <CardDescription>
        Quando sua conta for excluída, todos os dados de seus recursos serão permanentemente excluídos. Essa ação não pode ser desfeita.
      </CardDescription>
    </CardHeader>
    <form @submit.prevent="submitDeleteAccount">
      <CardContent>
        <div class="mt-4 flex items-center space-x-2 mb-1">
          <LockClosedIcon class="w-5 h-5 text-muted-foreground" />
          <Label htmlFor="password">Senha Atual</Label>
        </div>
        <Input
          id="password"
          placeholder="Sua senha atual"
          type="password"
          v-model="formDeleteAccount.password"
        />
      </CardContent>
      <CardFooter class="flex justify-end">
        <div v-if="showConfirmPassword">
          <Button @click="submitDeleteAccount" :disabled="formDeleteAccount.processing">Confirmar</Button>
          <Button @click="showConfirmPassword = false" variant="link">Cancelar</Button>
        </div>
        <Button v-else @click="showConfirmPassword = true" variant="destructive">Excluir conta</Button>
      </CardFooter>
    </form>
  </Card>
</template>
