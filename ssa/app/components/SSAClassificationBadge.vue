<template>
  <div class="ssa-classification-badge" :class="badgeClasses">
    <span class="badge-text">{{ text }}</span>
    <div v-if="scanner" class="scanner-line"></div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  text: string
  level?: 'public' | 'confidential' | 'secret' | 'top-secret'
  scanner?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  level: 'top-secret',
  scanner: true
})

const badgeClasses = computed((): Record<string, boolean> => ({
  [`ssa-classification-badge--${props.level}`]: true
}))
</script>

<style scoped lang="scss">
@import '@/assets/scss/ssa-variables.scss';
@import '@/assets/scss/ssa-keyframes.scss';

.ssa-classification-badge {
  position: relative;
  display: inline-block;
  padding: $ssa-spacing-xs $ssa-spacing-sm;
  border-radius: $ssa-border-radius-xxl;
  overflow: hidden;
  font-family: $ssa-font-mono;
  font-size: 14px;
  font-weight: 700;
  letter-spacing: 2px;
  
  @each $level, $colors in $ssa-classification-levels {
    $primary: nth($colors, 1);
    $secondary: nth($colors, 2);
    $border: nth($colors, 3);
    
    &--#{$level} {
      background: linear-gradient(45deg, rgba($primary, 0.8), rgba($secondary, 0.8));
      border: 1px solid $border;
      color: white;
    }
  }
	
	.badge-text {
		text-shadow: 0 0 10px currentColor;
		position: relative;
		z-index: 2;
	}
	
	.scanner-line {
		position: absolute;
		top: 0;
		left: -100%;
		width: 100%;
		height: 100%;
		background: linear-gradient(90deg, 
			transparent, 
			rgba(white, 0.6), 
			transparent
		);
		animation: scanner 3s linear infinite;
		z-index: 1;
	}
}

</style>