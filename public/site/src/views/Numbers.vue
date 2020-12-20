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
    <v-row class="text-center"
      ><v-data-table
        :sort-by.sync="sort"
        :sort-desc.sync="sortDesc"
        :headers="headers"
        :items="numbers"
        :page.sync="page"
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
          <v-btn color="blue darken-1" text @click="newNumberPost()">
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
  }),

  mounted() {
    this.getNumbers();
    this.customer_id = this.$route.params.id;
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
    gotoBack() {
      router.push({ path: "/home" });
    },
    async getNumbers() {
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
      var numbers = await Services.get(endpoints.getnumbers, filters);
      console.log(numbers);
      if (numbers.result !== undefined) {
        this.numbers = numbers.result.data;
        this.totalItems = numbers.result.total;
      }
      return false;
    },
    async newNumberPost() {
      this.form.customer_id = this.customer_id;
      var number = await Services.post(endpoints.newnumber, this.form);
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
