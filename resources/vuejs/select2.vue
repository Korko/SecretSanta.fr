<template>
  <select multiple="multiple" :name="name">
    <option v-for="option in options" :value="option.value" :key="option.id || option.value">{{ option.text }}</option>
  </select>
</template>

<script>
  var jQuery = require('jquery');
  require('multiple-select');

  export default {
    props: ['name', 'options', 'value', 'placeholder'],
    mounted: function() {
      jQuery(this.$el).multipleSelect({selectAll: false, filter: true, placeholder: this.placeholder, ellipsis: true});
    },
    updated: function() {
      jQuery(this.$el).multipleSelect('refresh');
    }
  }
</script>

<style>
  select {
    min-width: 300px;
  }
</style>
