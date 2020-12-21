<template>
  <v-container>
    <v-row class="mb-2">
      <div class="text-right w100">
        <v-btn color="primary" @click="dialogRegisterCustomer = true">
          Add customer
        </v-btn>
      </div>
    </v-row>
    <v-row class="text-center"
      ><v-data-table
        :sort-by.sync="sort"
        :sort-desc.sync="sortDesc"
        :headers="headers"
        :items="customers"
        :page.sync="page"
        :footer-props="{'items-per-page-options': perPageOptions}"
        :items-per-page.sync="itemsPerPage"
        :server-items-length="totalItems"
        class="elevation-4 w100"
      >
        <template v-slot:item.actions="{ item }">
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-icon
                small
                class="mr-2"
                @click="gotoNumbers(item)"
                v-bind="attrs"
                v-on="on"
              >
                mdi-phone
              </v-icon>
            </template>
            <span>Numbers</span>
          </v-tooltip>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-icon
                small
                class="mr-2"
                @click="editCustomer(item)"
                v-bind="attrs"
                v-on="on"
              >
                mdi-pencil
              </v-icon>
            </template>
            <span>Edit</span>
          </v-tooltip>
        </template>
      </v-data-table>
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
              <v-col cols="12"
                ><v-select v-model="form.status" :items="status" label="Status"></v-select>
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
          <v-btn color="blue darken-1" text @click="customerPost()">
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
import router from "@/router/index";
import Vue from "vue";

export default {
  name: "Home",

  data: () => ({
    dialogRegisterCustomer: false,
    form: {},
    customers: [],
    headers: [
      {
        text: "Name",
        align: "start",
        sortable: true,
        value: "name",
      },
      {
        text: "Document",
        align: "end",
        sortable: true,
        value: "document",
      },
      {
        text: "Status",
        align: "end",
        sortable: true,
        value: "status",
      },
      {
        text: "Actions",
        align: "end",
        sortable: false,
        value: "actions",
      },
    ],
    page: 1,
    itemsPerPage: 5,
    totalItems: 0,
    sort: ["name"],
    sortDesc: [true],
    status: ["new", "active", "suspended", "cancelled"],
    perPageOptions: [5, 10, 50, 100, 500],
  }),

  mounted() {
    this.getCustomers();
  },

  watch: {
    // whenever question changes, this function will run
    page: function () {
      this.getCustomers();
    },
    itemsPerPage: function () {
      this.getCustomers();
    },
    sort: function () {
      this.getCustomers();
    },
    sortDesc: function () {
      this.getCustomers();
    },
  },

  methods: {
    editCustomer(item) {
      this.form = item;
      this.dialogRegisterCustomer = true;
    },
    gotoNumbers(item) {
      router.push({ path: "/numbers/" + item.id });
    },
    async getCustomers() {
      let filters = {
        page: this.page,
        per_page: this.itemsPerPage,
      };
      if (this.sort[0]) {
        filters.sort = this.sort[0];
        if (this.sortDesc[0]) {
          filters.sort_desc = "asc";
        } else {
          filters.sort_desc = "desc";
        }
      }
      var customers = await Services.get(endpoints.getcustomers, filters);
      if (customers.result !== undefined) {
        this.customers = customers.result.data;
        this.totalItems = customers.result.total;
      }
      return false;
    },
    async customerPost() {
      let endpoint = endpoints.newcustomer;
      if (this.form.id) {
        endpoint = endpoints.editcustomer;
      }
      let user = await Services.post(endpoint, this.form);
      if (user.result !== undefined) {
        Vue.prototype.$toast("Success!", { color: "success", y: "top", x: "" });
        this.dialogRegisterCustomer = false;
        this.form = {};
        this.getCustomers();
        return user.result;
      }
      return false;
    },
  },
};
</script>
