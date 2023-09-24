import { getRoot } from "./functions";

$("#redefine").submit(function (e) {
  e.preventDefault();

  let novaSenha = $("#senha").val();
  let userId = $("#id").val();
  let userIp = $("#ip").val();

  let data = {
    novaSenha: novaSenha,
    userId: userId,
    userIp: userIp,
  };

  if (data.novaSenha == "") {
    alert("Por favor defina uma nova senha.");
    return;
  } else if (data.userId === null || data.userId === undefined) {
    alert("ID do usuário não encontrado.");
    return;
  } else if (data.userIp === null || data.userIp === undefined) {
    alert("ID do usuário não encontrado.");
    return;
  }

  $.ajax({
    type: "POST",
    url: getRoot() + "dashboard/controller/utils/primeiro_acesso",
    data: data,
    dataType: "json",
    success: function (response) {
      if (response) {
        if (response.success) {
          window.location.reload();
        } else if (response.message) {
          alert(response.message);
        }
      } else {
        console.error("Resposta JSON vazia ou inválida");
      }
    },
    error: function (xhr, status, error) {
      console.error(status);
      console.error(error);
      console.error(xhr.responseText);
    },
  });
});
