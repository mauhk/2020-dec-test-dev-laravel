<template>
  <v-container>
    <v-row class="text-center">
      <v-col cols="12">
        <v-img
          :src="require('../assets/logo.svg')"
          class="my-3"
          contain
          height="200"
        />
      </v-col>

      <v-col class="mb-4">
        <h1 class="display-2 font-weight-bold mb-3">Welcome</h1>
      </v-col>

      <v-col class="mb-5" cols="12">
        <v-btn target="_blank" text v-if="!user" @click="openLogin()">
          <span class="ma-2">Login</span>
        </v-btn>
      </v-col>

      <v-col class="mb-5" cols="12">
        {{user}}
        <v-btn target="_blank" text v-if="!user">
          <span class="ma-2">Register</span>
        </v-btn>
      </v-col>

      <v-col class="mb-5" cols="12">
        <v-row justify="center">
          <a class="subheading mx-3" target="_blank"> Forgot password? </a>
        </v-row>
      </v-col>
    </v-row>


    <v-dialog
      v-model="dialogLogin"
      persistent
      max-width="600px"
    >
      <v-card>
        <v-card-title>
          <span class="headline"></span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="form.email"
                  label="Email*"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="form.password"
                  label="Password*"
                  type="password"
                  required
                ></v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="blue darken-1"
            text
            @click="dialogLogin = false"
          >
            Close
          </v-btn>
          <v-btn
            color="blue darken-1"
            text
            @click="loginPost()"
          >
            Login
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
export default {
  name: "Home",

  data: () => ({
    user: null,
    dialogLogin: false,
    form: {},
  }),

  methods: {
    openLogin: function(){
      this.dialogLogin = true;
    },
    loginPost: function(){
      
    }
  }
};
</script>
