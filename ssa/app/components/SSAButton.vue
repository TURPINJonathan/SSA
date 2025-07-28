<template>
  <q-btn
    :label="label"
    :type="type"
    :loading="loading"
    :size="size"
    :icon="icon"
    :class="buttonClasses"
    class="ssa-button"
    @click="$emit('click')"
  />
</template>

<script setup lang="ts">
interface Props {
  label: string
  type?: string
  loading?: boolean
  size?: string
  icon?: string
  variant?: 'primary' | 'secondary' | 'success' | 'danger'
  fullWidth?: boolean
  scanner?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'primary',
  fullWidth: false,
  scanner: false
})

defineEmits<{
  click: []
}>()

const buttonClasses = computed((): Record<string, boolean> => ({
  'full-width': props.fullWidth,
  'ssa-button--scanner': props.scanner,
  [`ssa-button--${props.variant}`]: true
}))
</script>

<style scoped lang="scss">
@import '@/assets/scss/ssa-variables.scss';

.ssa-button {
  border-radius: $ssa-border-radius-md !important;
  font-family: $ssa-font-mono !important;
  font-weight: 500 !important;
  text-transform: uppercase !important;
  letter-spacing: 1px !important;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;

  @each $variant, $colors in $button-variants {
    $primary-color: nth($colors, 1);
    $secondary-color: nth($colors, 2);
    
    &.ssa-button--#{$variant} {
      background: linear-gradient(135deg, rgba($primary-color, 0.8), rgba($secondary-color, 0.8)) !important;
      border: 1px solid $primary-color !important;
      color: white !important;
    }
  }
  
  &.ssa-button--scanner::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, 
      transparent, 
      rgba(white, 0.2), 
      transparent
    );
    transition: left 0.5s ease;
  }
  
  &:hover {
    transform: translateY(-2px);
    
    &.ssa-button--primary {
      box-shadow: 0 0 25px rgba($ssa-accent, 0.5) !important;
    }
    
    &.ssa-button--scanner::before {
      left: 100%;
    }
  }
}
</style>