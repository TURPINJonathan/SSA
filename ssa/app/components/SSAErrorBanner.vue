<template>
  <div class="ssa-error-banner" :class="bannerClasses">
    <div class="error-icon">⚠️</div>
    <div class="error-content">
      <div class="error-title">ALERTE SYSTÈME</div>
      <div class="error-message">{{ message }}</div>
    </div>
    <div class="error-icon">⚠️</div>
    <div class="scanner-overlay"></div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  message: string
  type?: 'error' | 'warning' | 'critical'
}

const props = withDefaults(defineProps<Props>(), {
  type: 'error'
})

const bannerClasses = computed((): Record<string, boolean> => ({
  [`ssa-error-banner--${props.type}`]: true
}))
</script>

<style scoped lang="scss">
@import '@/assets/scss/ssa-variables.scss';
@import '@/assets/scss/ssa-keyframes.scss';

.ssa-error-banner {
  display: flex;
  align-items: center;
  gap: $ssa-spacing-xs;
  padding: $ssa-spacing-sm $ssa-spacing-md;
  border-radius: $ssa-border-radius-md;
  margin-top: $ssa-spacing-sm;
  position: relative;
  overflow: hidden;
  font-family: $ssa-font-mono;
  animation: error-pulse 2s ease-in-out infinite;

  @each $type, $colors in $ssa-error-types {
    $primary: nth($colors, 1);
    $secondary: nth($colors, 2);
    $border: nth($colors, 3);
    
    &--#{$type} {
      background: linear-gradient(45deg, rgba($primary, 0.9), rgba($secondary, 0.9));
      border: 1px solid $border;
      box-shadow: 0 0 20px rgba($border, 0.3);
    }
  }
}

.error-icon {
  font-size: 24px;
  animation: warning-blink 1s ease-in-out infinite;
	margin: auto;
}

.error-content {
  flex: 1;
  color: white;
	margin: auto;
	text-align: center;
}

.error-title {
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 2px;
  margin-bottom: 4px;
  opacity: 0.9;
}

.error-message {
  font-size: 14px;
  font-weight: 500;
}

.scanner-overlay {
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
  animation: scanner 3s linear infinite;
}
</style>