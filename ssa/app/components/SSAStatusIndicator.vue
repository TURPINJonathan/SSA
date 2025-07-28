<template>
  <div class="ssa-status-indicator" :class="indicatorClasses">
    <span class="indicator-dot"></span>
    <span v-if="text" class="indicator-text">{{ text }}</span>
  </div>
</template>

<script setup lang="ts">
interface Props {
  text?: string
  status: 'active' | 'warning' | 'error' | 'success' | 'info'
  animated?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  animated: true
})

const indicatorClasses = computed((): Record<string, boolean> => ({
  [`ssa-status-indicator--${props.status}`]: true,
  'ssa-status-indicator--animated': props.animated
}))
</script>

<style scoped lang="scss">
@import '@/assets/scss/ssa-variables.scss';
@import '@/assets/scss/ssa-mixins.scss';

.ssa-status-indicator {
  display: flex;
  align-items: center;
  gap: $ssa-spacing-sm;
  font-family: $ssa-font-mono;
  font-size: 11px;
  letter-spacing: 1px;

  @each $status, $color in $ssa-status-types {
    &--#{$status} {
      color: $color;
      
      .indicator-dot { 
        background: $color; 
        box-shadow: 0 0 10px $color; 
      }
    }
  }
}

.indicator-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  
  .ssa-status-indicator--animated & {
    @include ssa-pulse();
  }
}

.indicator-text {
  text-transform: uppercase;
}
</style>