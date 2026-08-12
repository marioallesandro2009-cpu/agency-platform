<script setup>
import { computed, ref } from "vue";

const props = defineProps({
    modelValue: { type: String, default: "" },
    id: { type: String, required: true },
    label: { type: String, required: true },
    type: { type: String, default: "text" },
    autocomplete: { type: String, default: "" },
    required: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false },
    error: { type: String, default: "" },
    icon: { type: String, default: "user" }, // user | mail | lock
});

const emit = defineEmits(["update:modelValue"]);

const showPassword = ref(false);
const isPassword = computed(() => props.type === "password");
const resolvedType = computed(() => (isPassword.value && showPassword.value ? "text" : props.type));
</script>

<template>
    <div>
        <label :for="id" class="block text-sm font-medium text-gray-700 mb-1.5">{{ label }}</label>

        <div
            class="relative flex items-center rounded-lg border bg-gray-50 transition-colors duration-200 focus-within:bg-white focus-within:border-brand-600 focus-within:ring-4 focus-within:ring-brand-600/10"
            :class="error ? 'border-red-300 motion-safe:animate-shake' : 'border-gray-200'"
        >
            <span class="pl-3.5 text-gray-400 flex-shrink-0" aria-hidden="true">
                <svg v-if="icon === 'mail'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0-.83.67-1.5 1.5-1.5h16.5c.83 0 1.5.67 1.5 1.5v10.5c0 .83-.67 1.5-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V6.75z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7l9 6 9-6" />
                </svg>
                <svg v-else-if="icon === 'lock'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7.5a4.5 4.5 0 10-9 0v3" />
                    <rect x="4.5" y="10.5" width="15" height="9" rx="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0" />
                </svg>
            </span>

            <input
                :id="id"
                :type="resolvedType"
                :value="modelValue"
                @input="emit('update:modelValue', $event.target.value)"
                :autocomplete="autocomplete"
                :required="required"
                :autofocus="autofocus"
                class="w-full bg-transparent border-0 shadow-none focus:ring-0 focus:outline-none text-sm text-gray-900 py-3 pl-2.5"
                :class="isPassword ? 'pr-2' : 'pr-3.5'"
            />

            <button
                v-if="isPassword"
                type="button"
                tabindex="-1"
                @click="showPassword = !showPassword"
                class="pr-3.5 text-gray-400 hover:text-gray-600 transition-colors duration-150"
                :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
            >
                <svg v-if="showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.774 3.162 10.066 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </button>
        </div>

        <p v-if="error" class="mt-1.5 text-xs text-red-600">{{ error }}</p>
    </div>
</template>
