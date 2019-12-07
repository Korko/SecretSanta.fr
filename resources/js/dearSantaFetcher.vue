<template>
</template>

<script>
import { mapState } from 'vuex'
import VueAjax from './ajaxVue.js'

export default {
  mixins: [VueAjax],
  props: ['formurl'],
  computed: mapState(['csrf', 'key']),

  mounted() {
    this.$nextTick(function () {
      this.call(this.formurl, {
        data: {
          _token: this.csrf,
          key: this.key
        }
      });
    });
  }
}
</script>

