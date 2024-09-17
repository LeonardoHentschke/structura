<script setup>
import { Primitive } from "radix-vue";
import { buttonVariants } from ".";
import { cn } from "@/lib/utils";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
  variant: { type: null, required: false },
  size: { type: null, required: false },
  class: { type: null, required: false },
  asChild: { type: Boolean, required: false },
  as: { type: null, required: false, default: "button" },
  href: { type: String, required: false }
});
</script>

<template>
  <component :is="href ? Link : Primitive"
             :href="href"
             :as="as"
             :as-child="asChild"
             :class="cn(buttonVariants({ variant, size }), props.class)">
    <slot />
  </component>
</template>
