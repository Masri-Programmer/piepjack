<template>
  <GuestLayout
    :title="$t('pages.login.title')"
    class="bg-accent min-h-screen overflow-hidden text-center"
  >
    <form class="mt-8 space-y-6" method="POST" @submit.prevent="login">
      <input type="hidden" name="rem" value="true" />
      <div class="rounded-md shadow-md space-y-4">
        <div>
          <label for="email-address" class="sr-only">{{ $t('common.forms.emailLabel') }}</label>
          <input
            id="email-address"
            name="email"
            type="email"
            autocomplete="email"
            autofocus="true"
            required=""
            v-model="user.email"
            class="block w-full p-3 border focus:outline-none focus:ring-2 text-accent_dark"
            :placeholder="$t('common.forms.emailPlaceholder')"
          />
        </div>
        <div>
          <label for="password" class="sr-only">{{ $t('pages.login.password') }}</label>
          <input
            id="password"
            name="password"
            type="password"
            autocomplete="current-password"
            required=""
            v-model="user.password"
            class="block w-full p-3 border focus:outline-none focus:ring-2 text-accent_dark"
            :placeholder="$t('pages.login.password')"
          />
        </div>
      </div>

      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <input
            id="remember-me"
            name="remember-me"
            type="checkbox"
            v-model="user.remember"
            class="h-4 w-4 text-gray focus:ring-main border-gray rounded"
          />
          <label for="remember-me" class="ml-2 block text-sm text-gray-300">
            {{ $t('pages.login.rememberMe') }}
          </label>
        </div>

        <div class="text-sm">
          </div>
      </div>

      <div>
        <button
          type="submit"
          :disabled="loading"
          class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gray hover:bg-main focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-main"
          :class="{
            'cursor-not-allowed': loading,
            'hover:bg-main': loading,
          }"
        >
          <svg
            v-if="loading"
            class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            ></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
          <span class="absolute left-0 inset-y-0 flex items-center pl-3">
            <Lock class="h-5 w-5 text-accent" aria-hidden="true" />
          </span>
          <div v-if="isLoading"><Spinner class="w-6 h-5" /></div>
          <div v-if="!isLoading" class="text-accent">{{ $t('components.buttons.signIn') }}</div>
        </button>
      </div>
    </form>
  </GuestLayout>
</template>

<script setup>
import { Lock } from "lucide-vue-next";
import GuestLayout from "@layouts/admin/GuestLayout.vue";
import { useLogin } from "@lib/user.js";
import Spinner from "@components/ui/Spinner.vue";
const { mutate: signIn, isLoading, data } = useLogin();

const user = {
  email: "",
  password: "",
  remember: false,
};

function login() {
  const formData = new FormData();
  formData.append("email", user.email);
  formData.append("password", user.password);
  formData.append("remember", user.remember ? 1 : 0);
  signIn(formData);
}
</script>