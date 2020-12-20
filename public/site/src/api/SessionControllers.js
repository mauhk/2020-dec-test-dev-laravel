function set(field, value) {
  sessionStorage[field] = JSON.stringify(value);
}
function get(field) {
  try {
    return JSON.parse(sessionStorage[field]);
  } catch (e) {
    return null;
  }
}
function remove(field) {
  sessionStorage.removeItem(field);
}
export { set, get, remove };
