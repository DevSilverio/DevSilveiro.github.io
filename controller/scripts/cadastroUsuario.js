import { getRoot } from "./functions";

$(document).ready(function () {
  $("#cadastroForm").on("submit", function (e) {
    e.preventDefault();

    let cpfInput = $("#cpf").val();

    cpfInput = cpfInput.replace(/\D/g, "");

    let cpfFormatado = cpfInput.replace(
      /(\d{3})(\d{3})(\d{3})(\d{2})/,
      "$1.$2.$3-$4"
    );

    let data = {
      nome: $("#nome").val(),
      data: $("#data").val(),
      email: $("#email").val(),
      cpf: cpfFormatado,
      nivel: $("#nivel").val(),
    };

    if (
      data.nome === "" ||
      data.data === "" ||
      data.email === "" ||
      data.cpf === "" ||
      data.nivel === ""
    ) {
      alert("Preencha todos os campos, por favor.");
      return;
    }

    $.ajax({
      type: "POST",
      url: getRoot() + "dashboard/controller/utils/cadastroUsuario",
      data: data,
      dataType: "json",
      success: function (response) {
        if (response) {
          if (response.success) {
            alert(response.messageSuccess);
          } else if (response.messageerror) {
            alert(response.messageerror);
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
