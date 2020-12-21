<template>
  <v-container>
    <v-row class="mb-2 mr-0 ml-0 w100">
      <v-icon large color="blue darken-2" @click="gotoBack()">
        mdi-arrow-left
      </v-icon>
      <v-spacer></v-spacer>
      <v-btn color="primary" @click="dialogRegisterNumber = true">
        Add number
      </v-btn>
    </v-row>
    <v-row class="text-center">
      <v-data-table
        :sort-by.sync="sort"
        :sort-desc.sync="sortDesc"
        :headers="headers"
        :items="numbers"
        :page.sync="page"
        :items-per-page.sync="itemsPerPage"
        :server-items-length="totalItems"
        :footer-props="{ 'items-per-page-options': perPageOptions }"
        class="elevation-4 w100"
      >
        <template v-slot:item.auto_attendant="{ item }">
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-icon
                small
                class="mr-2"
                @click="editNumber(item)"
                v-bind="attrs"
                v-on="on"
                v-if="item.auto_attendant == 1"
              >
                mdi-checkbox-marked-outline
              </v-icon>
              <v-icon
                small
                class="mr-2"
                @click="editNumber(item)"
                v-bind="attrs"
                v-on="on"
                v-else
              >
                mdi-checkbox-blank-outline
              </v-icon>
            </template>
            <span>Numbers</span>
          </v-tooltip>
        </template>
        <template v-slot:item.voicemail_enabled="{ item }">
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-icon
                small
                class="mr-2"
                @click="editNumber(item)"
                v-bind="attrs"
                v-on="on"
                v-if="item.voicemail_enabled == 1"
              >
                mdi-checkbox-marked-outline
              </v-icon>
              <v-icon
                small
                class="mr-2"
                @click="editNumber(item)"
                v-bind="attrs"
                v-on="on"
                v-else
              >
                mdi-checkbox-blank-outline
              </v-icon>
            </template>
            <span>Numbers</span>
          </v-tooltip>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-icon
                small
                class="mr-2"
                @click="editNumber(item)"
                v-bind="attrs"
                v-on="on"
              >
                mdi-pencil
              </v-icon>
            </template>
            <span>Numbers</span>
          </v-tooltip>
        </template>
      </v-data-table>
    </v-row>

    <v-dialog v-model="dialogRegisterNumber" persistent max-width="600px">
      <v-card>
        <v-card-title>
          <span class="headline">New number</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="form.number"
                  label="Number*"
                  required
                ></v-text-field>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="6">
                <v-checkbox
                  v-model="form.voicemail_enabled"
                  label="Voicemail"
                ></v-checkbox>
              </v-col>
              <v-col cols="6">
                <v-checkbox
                  v-model="form.auto_attendant"
                  label="Automated attendant"
                ></v-checkbox>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="blue darken-1"
            text
            @click="dialogRegisterNumber = false"
          >
            Close
          </v-btn>
          <v-btn color="blue darken-1" text @click="numberPost()"> Save </v-btn>
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
  name: "Numbers",

  data: () => ({
    dialogRegisterNumber: false,
    form: {},
    numbers: [],
    headers: [
      {
        text: "Number",
        align: "start",
        sortable: true,
        value: "number",
      },
      {
        text: "Status",
        align: "start",
        sortable: true,
        value: "status",
      },
      {
        text: "Voicemail",
        align: "center",
        sortable: false,
        value: "voicemail_enabled",
      },
      {
        text: "Automated attendant",
        align: "center",
        sortable: false,
        value: "auto_attendant",
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
    sort: ["number"],
    sortDesc: [true],
    customer_id: "",
    perPageOptions: [5, 10, 50, 100, 500],
  }),

  mounted() {
    this.customer_id = this.$route.params.id;
    this.getNumbers();
  },

  watch: {
    // whenever question changes, this function will run
    page: function () {
      this.getNumbers();
    },
    itemsPerPage: function () {
      this.getNumbers();
    },
    sort: function () {
      this.getNumbers();
    },
    sortDesc: function () {
      this.getNumbers();
    },
  },

  methods: {
    editNumber(item) {
      this.form = item;
      this.dialogRegisterNumber = true;
    },
    gotoBack() {
      router.push({ path: "/home" });
    },
    async getNumbers() {
      let filters = {
        page: this.page,
        per_page: this.itemsPerPage,
        customer_id: this.customer_id,
      };
      if (this.sort[0]) {
        filters.sort = this.sort[0];
        if (this.sortDesc[0]) {
          filters.sort_desc = "asc";
        } else {
          filters.sort_desc = "desc";
        }
      }
      var numbers = await Services.get(endpoints.getnumbers, filters);
      console.log(numbers);
      if (numbers.result !== undefined) {
        this.numbers = numbers.result.data;
        this.totalItems = numbers.result.total;
      }
      return false;
    },
    async numberPost() {
      let endpoint = endpoints.newnumber;
      if (this.form.id) {
        endpoint = endpoints.editnumber;
      }
      this.form.customer_id = this.customer_id;
      var number = await Services.post(endpoint, this.form);
      if (number.result !== undefined) {
        Vue.prototype.$toast("Success!", { color: "success", y: "top", x: "" });
        this.dialogRegisterNumber = false;
        this.form = {};
        this.getNumbers();
        return number.result;
      }
      return false;
    },
  },
};
</script>
