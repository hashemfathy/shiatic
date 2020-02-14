<template>
  <a tag="li" class="nav-item nav-dropdown" :class="classOpen" :url="url" disabled>
    <div style="text-align: -webkit-auto" class="nav-link nav-dropdown-toggle" @click="handleClick">
      <i :class="classIcon"></i>
      {{name}}
    </div>
    <ul class="nav-dropdown-items">
      <slot></slot>
    </ul>
  </a>
</template>

<script>
export default {
  props: {
    name: {
      type: String,
      default: ""
    },
    url: {
      type: String,
      default: ""
    },
    icon: {
      type: String,
      default: ""
    }
  },
  computed: {
    classIcon() {
      return ["nav-icon", this.icon];
    },
    classOpen() {
      console.log(
        window.location.pathname.split("/")[1],
        this.url.split("/")[1]
      );
      if (window.location.pathname.split("/")[1] == this.url.split("/")[1]) {
        return "open";
      }
      return "";
    }
  },
  methods: {
    handleClick(e) {
      e.preventDefault();
      e.target.parentElement.classList.toggle("open");
    }
  }
};
</script>

<style scoped lang="css">
.nav-link {
  cursor: pointer;
}
</style>
