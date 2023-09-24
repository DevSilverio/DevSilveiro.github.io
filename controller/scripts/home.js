const button = document.getElementById("personButton");
const menu = document.getElementById("menu");

button.addEventListener("click", function () {
  if (menu.classList.contains("hidden")) {
    menu.classList.remove("hidden");
  } else {
    menu.classList.add("hidden");
  }
});
