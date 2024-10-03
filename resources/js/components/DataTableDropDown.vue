<script setup>
import { DotsHorizontalIcon } from '@radix-icons/vue'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { Button } from '@/components/ui/button'

const props = defineProps({
    data: Object
});

function copy(id) {
  navigator.clipboard.writeText(id)
}
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="w-8 h-8 p-0">
        <span class="sr-only">Abrir menu</span>
        <DotsHorizontalIcon class="w-4 h-4" />
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent>
      <DropdownMenuLabel>Ações</DropdownMenuLabel>
      <DropdownMenuItem @click="copy(props.data.id)">
        Copiar ID
      </DropdownMenuItem>
      <DropdownMenuSeparator />
      <DropdownMenuItem>Ver cliente</DropdownMenuItem>
      <DropdownMenuItem>Ver endereços</DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>