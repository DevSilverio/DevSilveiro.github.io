import { getRoot } from "./functions";

$(document).ready(function () {
  $("#loginForm").on("submit", function (e) {
    e.preventDefault();

    let data = {
      email: $("#email").val(),
      senha: $("#senha").val(),
    };

    if (data.email === "" || data.senha === "") {
      alert("Preencha todos os campos, por favor.");
      return;
    }

    $.ajax({
      type: "POST",
      url: getRoot() + "dashboard/controller/utils/authLogin",
      data: data,
      dataType: "json",
      success: function (response) {
        if (response) {
          if (response.success) {
            document.location.href = response.redirect;
          } else if (response.message) {
            alert(response.message);
          }
        } else {
          console.error("Resposta JSON vazia ou inválida");
        }
      },
      error: function (xhr, status, error) {
        console.error(status);
      },
    });
  });
});
