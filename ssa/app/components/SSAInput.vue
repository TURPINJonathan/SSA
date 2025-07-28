<template>
  <div class="ssa-input-wrapper">
    <q-input
      :model-value="modelValue"
      :label="label"
      :type="type"
      :rules="rules"
      :prefix="prefix"
      :suffix="suffix"
      :hint="hint"
      :error="hasError"
      :error-message="errorMessage"
      :readonly="readonly"
      :disable="disable"
      class="ssa-input"
      outlined
      @update:model-value="handleUpdate"
      @focus="$emit('focus')"
      @blur="$emit('blur')"
    >
      <template v-if="icon" v-slot:prepend>
        <q-icon :name="icon" class="ssa-input-icon" />
      </template>
      
      <template v-if="clearable" v-slot:append>
        <q-icon 
          name="close" 
          @click="$emit('update:modelValue', '')"
          class="cursor-pointer ssa-input-clear"
        />
      </template>
    </q-input>
  </div>
</template>

<script setup lang="ts">
interface Props {
  modelValue: string
  label: string
  type?: 'text' | 'password' | 'textarea' | 'email' | 'search' | 'tel' | 'file' | 'number' | 'url' | 'time' | 'date' | 'datetime-local'
  rules?: Array<(val: string) => boolean | string>
  prefix?: string
  suffix?: string
  hint?: string
  icon?: string
  readonly?: boolean
  disable?: boolean
  clearable?: boolean
  errorMessage?: string
}

const props = defineProps<Props>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
  focus: []
  blur: []
}>()

const hasError = computed((): boolean => !!props.errorMessage)

const handleUpdate = (value: unknown): void => {
  const stringValue = value !== null ? String(value) : ''
  emit('update:modelValue', stringValue)
}
</script>

<style scoped lang="scss">
@import '@/assets/scss/ssa-variables.scss';

:deep(.ssa-input) {
  .q-field__control {
    background: rgba($ssa-dark, 0.7) !important;
    border: 1px solid rgba($ssa-accent, 0.3) !important;
    border-radius: 6px !important;
    transition: all 0.3s ease;

    &:hover {
      border-color: rgba($ssa-accent, 0.6) !important;
      box-shadow: 0 0 15px rgba($ssa-accent, 0.2);
    }
  }
  
  &.q-field--focused .q-field__control {
    border-color: $ssa-accent !important;
    box-shadow: 0 0 20px rgba($ssa-accent, 0.3) !important;
  }

  &.q-field--error .q-field__control {
    border-color: $ssa-danger !important;
    box-shadow: 0 0 15px rgba($ssa-danger, 0.3) !important;
  }
  
  .q-field__label {
    color: rgba($ssa-accent, 0.7) !important;
    font-family: $ssa-font-mono;
    font-size: 13px;
  }
  
  input {
    color: white !important;
    font-family: $ssa-font-mono;
    font-size: 14px;
  }

  .ssa-input-icon {
    color: rgba($ssa-accent, 0.7);
  }

  .ssa-input-clear {
    color: rgba($ssa-accent, 0.5);
    
    &:hover {
      color: $ssa-accent;
    }
  }
}
</style>