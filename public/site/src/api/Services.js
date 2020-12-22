import axios from "axios";
import router from "@/router/index";
import Vue from "vue";
import * as Session from "@/api/SessionControllers";

class Services {
  static serializeURI(obj) {
    var str = [];
    for (var key in obj)
      if (Object.prototype.hasOwnProperty.call(obj, key)) {
        str.push(encodeURIComponent(key) + "=" + encodeURIComponent(obj[key]));
      }
    return "?" + str.join("&");
  }

  static handleError(response) {
    let action = function (value, kvalue) {
      Vue.prototype.$toast(value, {color: 'error', y: 'top', x: ''});
      console.log(kvalue);
      if (kvalue == "10001") {
        //login expirado
        Session.remove("user");
        router.push({ path: "/" });
      }
    };
    if (typeof response.data.errors !== "undefined") {
      let errors = response.data.errors;
      for (const [kerror, error] of Object.entries(errors)) {
        if (Array.isArray(error)) {
          for (const [kvalue, value] of Object.entries(error)) {
            action(value, kvalue);
          }
        } else {
          action(error, kerror);
        }
      }
    }
  }

  static getConfig() {
    return {
      withCredentials: true,
      headers: {
        "Content-Type": "application/json"
      }
    };
  }

  static async get(endpoint, query) {
    if (query !== undefined) {
      endpoint = endpoint + Services.serializeURI(query);
    }
    const requestEndpoint = endpoint;
    return await axios
      .get(requestEndpoint, Services.getConfig())
      .then(response => {
        Services.handleError(response);
        return response.data;
      })
      .catch(error => {
        console.log("resposta erro " + error);
        return error;
      });
  }

  static async post(endpoint, payload) {
    return await axios
      .post(endpoint, payload, Services.getConfig())
      .then(response => {
        Services.handleError(response);
        return response.data;
      })
      .catch(error => {
        console.log("resposta erro" + error);
        return error;
      });
  }

  static async put(endpoint, payload) {
    await axios
      .put(endpoint, payload, Services.getConfig())
      .then(response => {
        Services.handleError(response);
        return response.data;
      })
      .catch(error => {
        console.log("resposta erro" + error);
        return error;
      });
  }

  static async delete(endpoint) {
    await axios
      .delete(endpoint, Services.getConfig())
      .then(response => {
        console.log(response);
        Services.handleError(response);
        return response.data;
      })
      .catch(error => {
        console.log("resposta erro" + error);
        return error;
      });
  }
}

export { Services };
