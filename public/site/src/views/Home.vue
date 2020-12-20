<template>
  <v-container>
    <v-row class="text-center">
      <v-btn color="blue darken-1" text @click="dialogRegisterCustomer = true">
        Add customer
      </v-btn>
    </v-row>

    <v-dialog v-model="dialogRegisterCustomer" persistent max-width="600px">
      <v-card>
        <v-card-title>
          <span class="headline">New customer</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="form.name"
                  label="Name*"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="form.document"
                  label="Document*"
                  type="tel"
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
            @click="dialogRegisterCustomer = false"
          >
            Close
          </v-btn>
          <v-btn color="blue darken-1" text @click="newCustomerPost()">
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import { Services } from "@/api/Services";
import { endpoints } from "@/api/Endpoints";
import Vue from "vue";

export default {
  name: "Home",

  data: () => ({
    dialogRegisterCustomer: false,
    form: {},
  }),

  mounted() {
    this.getCustomers();
  },

  methods: {
    async getCustomers(){
      var customers = await Services.get(endpoints.getcustomers);
      console.log(customers);
      return false;
    },
    async newCustomerPost() {
      var user = await Services.post(endpoints.newcustomer, this.form);
      if (user.result !== undefined) {
        Vue.prototype.$toast("Saved!", { color: "success", y: "top", x: "" });
        this.dialogRegisterCustomer = false;
        this.form = {};
        return user.result;
      }
      return false;
    },
  },
};
</script>
