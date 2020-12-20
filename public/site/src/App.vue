
<template>
  <v-app id="app">
    <v-app-bar app color="primary" dark>
      <div class="d-flex align-center">
        <h1>Dev test</h1>
      </div>

      <v-spacer></v-spacer>

      <v-btn
        href=""
        target="_blank"
        @click="logout()"
        text
      >
        <v-icon>mdi-logout</v-icon>
      </v-btn>
    </v-app-bar>

    <v-main> <router-view /> </v-main>
  </v-app>
</template>

<script>
import * as Session from "@/api/SessionControllers";
import router from "@/router/index";

export default {
  name: "App",

  components: {},

  methods: {
    logout() {
      Session.remove("user");
      router.push({ path: "/" });
    },
  },

  data: () => ({
    user: null,
  }),
};
</script>

<style lang="scss">
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}

#nav {
  padding: 30px;

  a {
    font-weight: bold;
    color: #2c3e50;

    &.router-link-exact-active {
      color: #42b983;
    }
  }
}
</style>
