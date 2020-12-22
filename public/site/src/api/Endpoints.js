const BASE_URL = process.env.VUE_APP_API_ENDPOINT;

const endpoints = {
  login: BASE_URL + "/api/user/login",
  register: BASE_URL + "/api/user/register",
  newcustomer: BASE_URL + "/api/customer/create",
  editcustomer: BASE_URL + "/api/customer/edit",
  getcustomers: BASE_URL + "/api/customer/list",
  getnumbers: BASE_URL + "/api/customer/numbers/list",
  newnumber: BASE_URL + "/api/customer/numbers/create",
  editnumber: BASE_URL + "/api/customer/numbers/edit",
  deletenumber: BASE_URL + "/api/customer/numbers/",
};

export { endpoints };
