<template>
  <div class="auth-layout">
    <div class="grid-overlay"></div>
		
    <div class="auth-background">
      <div class="hologram-header">
				<SSAClassificationBadge text="TOP SECRET" level="confidential" />
        <div class="system-info">SSA.v0.0.7</div>
      </div>

      <slot />

      <div class="status-bar">
				<SSAStatusIndicator text="Niveau de sécurité: maximum" status="success" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
</script>

<style scoped lang="scss">
@use "sass:color";
@import '@/assets/scss/ssa-variables.scss';
@import '@/assets/scss/ssa-keyframes.scss';

.auth-layout {
  min-height: 100vh;
  background: 
    radial-gradient(ellipse at center, rgba($ssa-accent, 0.1) 0%, transparent 70%),
    linear-gradient(135deg, $ssa-dark 0%, color.adjust($ssa-primary, $lightness: -15%) 50%, $ssa-dark 100%);
  
  display: flex;
  align-items: center;
  justify-content: center;
  padding: $ssa-spacing-md;
  position: relative;
  overflow: hidden;

  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
      linear-gradient(90deg, transparent 79px, rgba($ssa-accent, 0.04) 81px, rgba($ssa-accent, 0.04) 82px, transparent 84px),
      linear-gradient(transparent 79px, rgba($ssa-accent, 0.04) 81px, rgba($ssa-accent, 0.04) 82px, transparent 84px);
    background-size: 80px 80px;
    animation: grid-move 1s linear infinite;
    pointer-events: none;
  }

	.grid-overlay {
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background-image: 
			linear-gradient(rgba($ssa-accent, 0.1) 1px, transparent 1px),
			linear-gradient(90deg, rgba($ssa-accent, 0.1) 1px, transparent 1px);
		background-size: 40px 40px;
		pointer-events: none;
		opacity: 0.3;
	}
	
	.auth-background {
		width: 100%;
		max-width: 50svw;
		position: relative;
		background: rgba($ssa-dark, 0.95);
		border: 1px solid rgba($ssa-accent, 0.5);
		border-radius: $ssa-border-radius-lg;
		box-shadow: 
			0 0 40px rgba($ssa-accent, 0.3),
			inset 0 0 40px rgba($ssa-accent, 0.05);
		backdrop-filter: blur(10px);
		overflow: hidden;
	
		&::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 2px;
			background: linear-gradient(90deg, 
				transparent, 
				$ssa-accent 20%, 
				$ssa-secondary 50%, 
				$ssa-accent 80%, 
				transparent
			);
			animation: ssa-pulse 2s ease-in-out infinite;
		}
	
		&::after {
			content: '';
			position: absolute;
			bottom: 0;
			left: 0;
			right: 0;
			height: 2px;
			background: linear-gradient(90deg, 
				transparent, 
				$ssa-success 20%, 
				$ssa-accent 50%, 
				$ssa-success 80%, 
				transparent
			);
			animation: pulse-border 2s ease-in-out infinite reverse;
		}

		.hologram-header {
			background: linear-gradient(135deg, rgba($ssa-accent, 0.2), rgba($ssa-secondary, 0.1));
			backdrop-filter: blur(5px);
			padding: $ssa-spacing-lg;
			text-align: center;
			border-bottom: 1px solid rgba($ssa-accent, 0.3);
			position: relative;
		
			.system-info {
				font-family: $ssa-font-mono;
				font-size: 10px;
				color: rgba($ssa-accent, 0.7);
				margin-top: $ssa-spacing-xs;
				letter-spacing: 1px;
			}
		}
		
		.status-bar {
			background: rgba($ssa-dark, 0.9);
			padding: $ssa-spacing-sm $ssa-spacing-md;
			border-top: 1px solid rgba($ssa-success, 0.3);
			display: flex;
			align-items: center;
			justify-content: center;
		}
	}
	
}


@media (max-width: 480px) {
  .auth-background {
    max-width: 95%;
    margin: 10px;
  }
}
</style>