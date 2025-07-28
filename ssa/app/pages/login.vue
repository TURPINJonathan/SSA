<template>
  <div class="ssa-login-container">
    <div class="ssa-login-header">
      <div class="ssa-title">SSA</div>
      <div class="ssa-subtitle">Super Secret Agency</div>
    </div>

    <QForm @submit.prevent="handleLogin" class="ssa-login-form">
      <SSAInput
        v-model="form.email"
        label="Email"
        type="email"
        :rules="emailRules"
        prefix="👤"
      />
      
      <SSAInput
        v-model="form.password"
        label="Code d'accès"
        type="password"
        :rules="passwordRules"
        prefix="🔐"
      />

      <SSAButton
        label="demande d'accès"
        type="submit"
        :loading="loading"
        size="lg"
        variant="success"
        full-width
        scanner
        @click="handleLogin"
      />
    </QForm>

    <SSAErrorBanner v-if="error" :message="error" />
  </div>
</template>

<script setup lang="ts">
import { QForm } from 'quasar'

interface LoginForm {
	email: string
	password: string
}

definePageMeta({
  layout: 'login'
})

const authStore = useAuthStore()

const form = reactive<LoginForm>({
  email: '',
  password: ''
})

const loading = ref<boolean>(false)
const error = ref<string>('')

const handleLogin = async (): Promise<void> => {
  if (loading.value) return
  
  loading.value = true
  error.value = ''
  
  try {
    await authStore.login(form)
  } catch (err: unknown) {
    error.value = 'ACCÈS REFUSÉ'
    console.error('Erreur de connexion:', err)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped lang="scss">
@import '@/assets/scss/ssa-variables.scss';

.ssa-login-container {
  padding: 20px;

	.ssa-login-header {
		text-align: center;
		margin-bottom: 30px;

		.ssa-title {
			color: $ssa-accent;
			font-family: $ssa-font-mono;
			font-weight: 300;
			text-shadow: 0 0 20px rgba($ssa-accent, 0.5);
			font-size: 2.5rem;
			margin-bottom: 8px;
			letter-spacing: 3px;
		}
		
		.ssa-subtitle {
			color: rgba($ssa-accent, 0.8);
			font-family: $ssa-font-mono;
			margin-bottom: 20px;
			font-size: 1rem;
			letter-spacing: 1px;
		}
	}

	.ssa-login-form {
		display: flex;
		flex-direction: column;
		gap: 10px;
	}
}
</style>