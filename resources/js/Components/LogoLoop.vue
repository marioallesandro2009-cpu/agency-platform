<template>
  <div
    ref="containerRef"
    :class="rootClassName"
    :style="containerStyle"
    role="region"
    :aria-label="ariaLabel"
  >
    <div
      ref="trackRef"
      class="logoloop__track"
      @mouseenter="handleMouseEnter"
      @mouseleave="handleMouseLeave"
    >
      <ul
        v-for="copyIndex in copyCount"
        :key="`copy-${copyIndex - 1}`"
        class="logoloop__list"
        role="list"
        :aria-hidden="copyIndex > 1"
        :ref="copyIndex === 1 ? setSeqRef : undefined"
      >
        <li
          v-for="(item, itemIndex) in logos"
          :key="`${copyIndex - 1}-${itemIndex}`"
          class="logoloop__item"
          role="listitem"
        >
          <slot name="item" :item="item" :index="itemIndex">
            <component
              :is="item.href ? 'a' : 'span'"
              v-bind="
                item.href
                  ? {
                      href: item.href,
                      target: '_blank',
                      rel: 'noreferrer noopener',
                      class: 'logoloop__link',
                      'aria-label': item.ariaLabel || item.title || 'logo link',
                    }
                  : {}
              "
            >
              <span
                v-if="item.icon"
                class="logoloop__node"
                :aria-hidden="!!item.href && !item.ariaLabel"
              >
                <component :is="item.icon" />
              </span>
              <img
                v-else-if="item.src"
                :src="item.src"
                :srcset="item.srcSet"
                :sizes="item.sizes"
                :width="item.width"
                :height="item.height"
                :alt="item.alt || ''"
                :title="item.title"
                loading="lazy"
                decoding="async"
                draggable="false"
              />
            </component>
          </slot>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'

const props = defineProps({
  logos: { type: Array, required: true },
  speed: { type: Number, default: 120 },
  direction: { type: String, default: 'left' },
  width: { type: [Number, String], default: '100%' },
  logoHeight: { type: Number, default: 28 },
  gap: { type: Number, default: 32 },
  pauseOnHover: { type: Boolean, default: undefined },
  hoverSpeed: { type: Number, default: undefined },
  fadeOut: { type: Boolean, default: false },
  fadeOutColor: { type: String, default: undefined },
  scaleOnHover: { type: Boolean, default: false },
  ariaLabel: { type: String, default: 'Partner logos' },
})

const ANIMATION_CONFIG = { SMOOTH_TAU: 0.25, MIN_COPIES: 2, COPY_HEADROOM: 2 }

const containerRef = ref(null)
const trackRef = ref(null)
const seqRef = ref(null)

const seqWidth = ref(0)
const seqHeight = ref(0)
const copyCount = ref(ANIMATION_CONFIG.MIN_COPIES)
const isHovered = ref(false)

let resizeObserver = null
let rafId = null
let lastTimestamp = null
let offset = 0
let velocity = 0

const isVertical = computed(() => props.direction === 'up' || props.direction === 'down')

const effectiveHoverSpeed = computed(() => {
  if (props.hoverSpeed !== undefined) return props.hoverSpeed
  if (props.pauseOnHover === true) return 0
  if (props.pauseOnHover === false) return undefined
  return 0
})

const targetVelocity = computed(() => {
  const magnitude = Math.abs(props.speed)
  let directionMultiplier
  if (isVertical.value) {
    directionMultiplier = props.direction === 'up' ? 1 : -1
  } else {
    directionMultiplier = props.direction === 'left' ? 1 : -1
  }
  const speedMultiplier = props.speed < 0 ? -1 : 1
  return magnitude * directionMultiplier * speedMultiplier
})

const rootClassName = computed(() =>
  [
    'logoloop',
    isVertical.value ? 'logoloop--vertical' : 'logoloop--horizontal',
    props.fadeOut && 'logoloop--fade',
    props.scaleOnHover && 'logoloop--scale-hover',
  ]
    .filter(Boolean)
    .join(' ')
)

const toCssLength = (value) => (typeof value === 'number' ? `${value}px` : value ?? undefined)

const containerStyle = computed(() => ({
  width: isVertical.value
    ? toCssLength(props.width) === '100%'
      ? undefined
      : toCssLength(props.width)
    : (toCssLength(props.width) ?? '100%'),
  '--logoloop-gap': `${props.gap}px`,
  '--logoloop-logoHeight': `${props.logoHeight}px`,
  ...(props.fadeOutColor ? { '--logoloop-fadeColor': props.fadeOutColor } : {}),
}))

function setSeqRef(el) {
  if (el) seqRef.value = el
}

function updateDimensions() {
  const containerWidth = containerRef.value?.clientWidth ?? 0
  const sequenceRect = seqRef.value?.getBoundingClientRect?.()
  const sequenceWidth = sequenceRect?.width ?? 0
  const sequenceHeight = sequenceRect?.height ?? 0

  if (isVertical.value) {
    const parentHeight = containerRef.value?.parentElement?.clientHeight ?? 0
    if (containerRef.value && parentHeight > 0) {
      const targetHeight = Math.ceil(parentHeight)
      if (containerRef.value.style.height !== `${targetHeight}px`) {
        containerRef.value.style.height = `${targetHeight}px`
      }
    }
    if (sequenceHeight > 0) {
      seqHeight.value = Math.ceil(sequenceHeight)
      const viewport = containerRef.value?.clientHeight ?? parentHeight ?? sequenceHeight
      const copiesNeeded = Math.ceil(viewport / sequenceHeight) + ANIMATION_CONFIG.COPY_HEADROOM
      copyCount.value = Math.max(ANIMATION_CONFIG.MIN_COPIES, copiesNeeded)
    }
  } else if (sequenceWidth > 0) {
    seqWidth.value = Math.ceil(sequenceWidth)
    const copiesNeeded = Math.ceil(containerWidth / sequenceWidth) + ANIMATION_CONFIG.COPY_HEADROOM
    copyCount.value = Math.max(ANIMATION_CONFIG.MIN_COPIES, copiesNeeded)
  }
}

function waitForImagesThenUpdate() {
  const images = seqRef.value?.querySelectorAll('img') ?? []
  if (images.length === 0) {
    updateDimensions()
    return
  }
  let remaining = images.length
  const handleLoad = () => {
    remaining -= 1
    if (remaining === 0) updateDimensions()
  }
  images.forEach((img) => {
    if (img.complete) {
      handleLoad()
    } else {
      img.addEventListener('load', handleLoad, { once: true })
      img.addEventListener('error', handleLoad, { once: true })
    }
  })
}

function animate(timestamp) {
  const track = trackRef.value
  if (!track) return

  if (lastTimestamp === null) lastTimestamp = timestamp
  const deltaTime = Math.max(0, timestamp - lastTimestamp) / 1000
  lastTimestamp = timestamp

  const target =
    isHovered.value && effectiveHoverSpeed.value !== undefined
      ? effectiveHoverSpeed.value
      : targetVelocity.value

  const easingFactor = 1 - Math.exp(-deltaTime / ANIMATION_CONFIG.SMOOTH_TAU)
  velocity += (target - velocity) * easingFactor

  const seqSize = isVertical.value ? seqHeight.value : seqWidth.value
  if (seqSize > 0) {
    let nextOffset = offset + velocity * deltaTime
    nextOffset = ((nextOffset % seqSize) + seqSize) % seqSize
    offset = nextOffset

    track.style.transform = isVertical.value
      ? `translate3d(0, ${-offset}px, 0)`
      : `translate3d(${-offset}px, 0, 0)`
  }

  rafId = requestAnimationFrame(animate)
}

function handleMouseEnter() {
  if (effectiveHoverSpeed.value !== undefined) isHovered.value = true
}
function handleMouseLeave() {
  if (effectiveHoverSpeed.value !== undefined) isHovered.value = false
}

onMounted(async () => {
  await nextTick()
  waitForImagesThenUpdate()

  if (window.ResizeObserver) {
    resizeObserver = new ResizeObserver(() => updateDimensions())
    if (containerRef.value) resizeObserver.observe(containerRef.value)
    if (seqRef.value) resizeObserver.observe(seqRef.value)
  } else {
    window.addEventListener('resize', updateDimensions)
  }

  rafId = requestAnimationFrame(animate)
})

onBeforeUnmount(() => {
  if (rafId) cancelAnimationFrame(rafId)
  if (resizeObserver) resizeObserver.disconnect()
  else window.removeEventListener('resize', updateDimensions)
})

watch(
  () => [props.logos, props.gap, props.logoHeight, isVertical.value],
  async () => {
    await nextTick()
    updateDimensions()
  }
)
</script>

<style scoped src="./LogoLoop.css"></style>
