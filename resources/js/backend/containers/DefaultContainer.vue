<template>
  <div class="app">
    <AppHeader fixed>
      <SidebarToggler class="d-lg-none" display="md" mobile />
      <b-link style="height: 36px;" class="navbar-brand" to="#">
        <h4>Shiatic</h4>
        <img
          class="navbar-brand-minimized"
          src="/images/shiatic-logo-small.png"
          width="30"
          height="30"
          alt="CoreUI Logo"
        />
      </b-link>
      <SidebarToggler class="d-md-down-none" display="lg" />
      <b-navbar-nav class="ml-auto"></b-navbar-nav>
      <AsideToggler class="d-none d-lg-block" />
      <AsideToggler class="d-lg-none" mobile />
    </AppHeader>
    <div class="app-body">
      <AppSidebar fixed>
        <SidebarHeader />
        <SidebarForm />
        <SidebarNav :navItems="nav"></SidebarNav>
        <SidebarFooter />
        <SidebarMinimizer />
      </AppSidebar>
      <main class="main">
        <div class="container-fluid" style="margin-top: 20px;">
          <slot></slot>
        </div>
      </main>
      <AppAside fixed>
        <!--aside-->
        <DefaultAside />
      </AppAside>
    </div>
    <TheFooter>
      <!--footer-->
      <div>
        <span class="ml-1">&copy; {{ new Date().getFullYear() }}</span>
      </div>
      <div class="ml-auto"></div>
    </TheFooter>
  </div>
</template>

<script>
import nav from "../_nav";
import {
  Header as AppHeader,
  SidebarToggler,
  Sidebar as AppSidebar,
  SidebarFooter,
  SidebarForm,
  SidebarHeader,
  SidebarMinimizer,
  Aside as AppAside,
  AsideToggler,
  Footer as TheFooter
} from "@coreui/vue";
import { SidebarNav } from "../views/sidebar";
import { Breadcrumb } from "../views/breadcrumb";
import DefaultAside from "./DefaultAside";
import DefaultHeaderDropdown from "./DefaultHeaderDropdown";
import DefaultHeaderDropdownNotif from "./DefaultHeaderDropdownNotif";
import DefaultHeaderDropdownAccnt from "./DefaultHeaderDropdownAccnt";
import DefaultHeaderDropdownMssgs from "./DefaultHeaderDropdownMssgs";
import DefaultHeaderDropdownTasks from "./DefaultHeaderDropdownTasks";

export default {
  name: "DefaultContainer",
  components: {
    AsideToggler,
    AppHeader,
    AppSidebar,
    AppAside,
    TheFooter,
    DefaultAside,
    DefaultHeaderDropdown,
    DefaultHeaderDropdownMssgs,
    DefaultHeaderDropdownNotif,
    DefaultHeaderDropdownTasks,
    DefaultHeaderDropdownAccnt,
    SidebarNav,
    SidebarForm,
    SidebarFooter,
    SidebarToggler,
    SidebarHeader,
    Breadcrumb,
    SidebarMinimizer
  },
  data() {
    return {
      nav: nav.items
    };
  },
  computed: {
    name() {
      return this.$route.name;
    },
    list() {
      return [
        { url: "/one", label: "One" },
        { url: "/one/two", label: "Two" },
        { url: "/one/two/three", label: "Three" }
      ];
    }
  }
};
</script>
<style>
</style>
