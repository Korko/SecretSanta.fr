<template>
  <span><slot v-if="show"></slot></span>
</template>

<script>
export default {
  props: {
    delay: Number
  },
  data: () => {
    return {
      show: false
    };
  },
  mounted() {
    setTimeout(() => {
      this.show = true;
    }, this.delay);
  }
}
</script>
