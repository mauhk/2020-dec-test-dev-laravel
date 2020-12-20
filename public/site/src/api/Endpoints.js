const BASE_URL = process.env.VUE_APP_API_ENDPOINT;

const endpoints = {
  login: BASE_URL + "/api/user/login",
  register: BASE_URL + "/api/user/register",
  newcustomer: BASE_URL + "/api/customer/create",
  getcustomers: BASE_URL + "/api/customer/list",
};

export { endpoints };
