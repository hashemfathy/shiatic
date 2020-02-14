<template>
  <ol class="breadcrumb">
    <li class="breadcrumb-item" :key="index" v-for="(route, index) in list">
      <span class="active" v-if="isLast(index)">{{ route.label }}</span>
      <a :href="route.url" v-else>{{ route.label }}</a>
    </li>
  </ol>
</template>

<script>
export default {
  props: {
    list: {
      type: Array,
      required: true,
      default: () => []
    }
  },
  computed: {
    routeRecords: function() {
      return this.list.filter(route => route.name || route.meta.label);
    }
  },
  methods: {
    getName(item) {
      return item.meta && item.meta.label ? item.meta.label : item.name || null;
    },
    isLast(index) {
      return index === this.list.length - 1;
    }
  }
};
</script>
