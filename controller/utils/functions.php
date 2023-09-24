<?php
global $conn;

function connection()
{
  $host = '127.0.0.1';
  $username = 'root';
  $password = '';
  $database = 'dashboard';

  global $conn;

  if (!isset($conn)) {
    $conn = mysqli_connect($host, $username, $password, $database);

    if (mysqli_connect_error()) {
      die('Erro na conexão com o banco de dados: ' . mysqli_connect_error());
    }
  }

  return $conn;
}

function loginUser()
{
  global $conn;
  $conn = connection();

  $email = $_POST['email'];
  $senha = $_POST['senha'];

  $sql = "SELECT id, email, senha, ip_cliente, nivel, primeiro_acesso FROM usuarios WHERE email = ?";
  $stmt = mysqli_prepare($conn, $sql);

  if ($stmt === false) {
    die("Erro ao preparar a declaração: " . mysqli_error($conn));
  }

  mysqli_stmt_bind_param($stmt, "s", $email);
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);
  $row = mysqli_fetch_assoc($result);


  if ($row) {

    $hashSenhaArmazenada = $row['senha'];

    if (password_verify($senha, $hashSenhaArmazenada)) {

      session_start();

      $_SESSION['id'] = $row['id'];
      $_SESSION['email'] = $row['email'];
      $_SESSION['primeiro_acesso'] = $row['primeiro_acesso'];
      $_SESSION['ip'] = $row['ip_cliente'];
      $_SESSION['nivel'] = $row['nivel'];

      echo json_encode(['success' => true, 'redirect' => 'view/pages/home']);
    } else {
      echo json_encode(['success' => false, 'message' => 'Senha incorreta.']);
    }
  } else {
    echo json_encode(['success' => false, 'message' => 'Email não encontrado.']);
  }

  mysqli_stmt_close($stmt);
  mysqli_close($conn);
}

function pageUser()
{
  session_start();

  if (isset($_SESSION['id'])) {

    primeiroAcesso();

    $userId = $_SESSION['id'];
    $userIp = $_SESSION['ip'];
    $userEmail = $_SESSION['email'];
    $primeiroAcesso = $_SESSION['primeiro_acesso'];
    $nivelAcesso = $_SESSION['nivel'];

  } else {
    header("Location: error");
  }
}

function usuarioJaCadastrado($conn, $campo, $valor)
{
  $sql = "SELECT * FROM dashboard.usuarios WHERE $campo = ?";
  $stmt = mysqli_prepare($conn, $sql);

  if ($stmt === false) {
    die("Erro ao preparar a declaração: " . mysqli_error($conn));
  }

  mysqli_stmt_bind_param($stmt, "s", $valor);
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);
  $row = mysqli_fetch_assoc($result);

  return $row ? true : false;
}

function primeiroAcesso()
{
  global $userId;
  global $userEmail;
  global $primeiroAcesso;
  global $conn;
  $conn = connection();


  $sql = "SELECT primeiro_acesso FROM dashboard.usuarios WHERE email = ?";
  $stmt = mysqli_prepare($conn, $sql);

  if ($stmt === false) {
    die("Erro ao preparar a declaração: " . mysqli_error($conn));
  }

  mysqli_stmt_bind_param($stmt, "s", $userEmail);

  $result = mysqli_stmt_execute($stmt);

  if ($result) {
    $resultado = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($resultado);

    if ($row) {
      $primeiroAcessosql = $row['primeiro_acesso'];

      if ($primeiroAcessosql == 1) {
        modalRedefine($userId);
        return;
      } else if ($primeiroAcesso == 1) {
        returnLogin();
        return;
      }
    }
  } else {
    echo "Erro ao executar a declaração.";
  }

  mysqli_stmt_close($stmt);
  mysqli_close($conn);
}

function modalRedefine()
{
  global $userId;

  $html = "
  <div
  class='relative z-10'
  aria-labelledby='modal-title'
  role='dialog'
  aria-modal='true'
>
  <div class='fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity'></div>

  <div class='fixed inset-0 z-10 w-screen overflow-y-auto'>
    <div
      class='flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0'
    >
      <div
        class='relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg'
      >
        <div class='bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4'>
          <div class='sm:flex sm:items-start'>
            <div
              class='mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10'
            >
              <svg
                class='h-6 w-6 text-red-600'
                fill='none'
                viewBox='0 0 24 24'
                stroke-width='1.5'
                stroke='currentColor'
                aria-hidden='true'
              >
                <path
                  stroke-linecap='round'
                  stroke-linejoin='round'
                  d='M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z'
                />
              </svg>
            </div>
            <div
              class='mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left'
              class='px-4 py-3'
            >
              <h3
                class='text-base font-semibold leading-6 text-gray-900'
                id='modal-title'
              >
                Redefinir senha
              </h3>
              <div class='mt-2'>
                <p class='text-sm text-gray-500'>
                  Agora que você tem acesso a nossa plataforma pela primeira vez
                  redefina sua senha para uma segura e que somente você saiba.
                  (Mínimo de 8 e máximo de 20 caracteres)
                </p>
              </div>
            </div>
          </div>
        </div>
        <!-- class='bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6' -->
        <div>
          <form id='redefine' method='POST'>
            <div class='p-2'>
              <label
                for='senha'
                class='block text-sm font-medium leading-6 text-gray-900'
                >Nova senha</label
              >
              <div class='mt-2'>
                <input
                  id='senha'
                  name='senha'
                  type='password'
                  autocomplete='senha'
                  minlength='8'
                  maxlength='20'
                  class='block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6'
                />

                <input
                  type='hidden'
                  name='userId'
                  id='id'
                  value=' " . $userId . " '
                />
                <input type='hidden' name='userId' id='ip' value=' " . $_SERVER['REMOTE_ADDR'] . " '>
              </div>
            </div>

            <div class='flex justify-end p-2'>
              <input
                type='submit'
                value='Redefinir'
                class='inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto'
              />
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

          <script type='module' src='../../controller/scripts/redefine.js'></script>
          
          ";

  echo $html;
}

function redefine()
{
  global $conn;
  $conn = connection();

  $senha = $_POST['novaSenha'];
  $id = $_POST['userId'];
  $ip = $_SERVER['REMOTE_ADDR'];
  $primeiroAcesso = 2;

  $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

  $sql = "CALL AtualizarSenhaEPrimeiroAcesso(?, ?, ?, ?);";
  $stmt = mysqli_prepare($conn, $sql);

  if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Erro ao preparar a declaração: ' . mysqli_error($conn)]);
  }

  mysqli_stmt_bind_param($stmt, "ssss", $id, $senhaHash, $primeiroAcesso, $ip);
  mysqli_stmt_execute($stmt);

  if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo json_encode(['success' => true]);
    return;
  } else {
    echo json_encode(['success' => false, 'message' => 'Falha ao mudar a senha e o campo primeiro acesso.']);
  }

  mysqli_stmt_close($stmt);
  mysqli_close($conn);
}

function returnLogin()
{

  $html = "
  <div class='relative z-10' aria-labelledby='modal-title' role='dialog' aria-modal='true'>
  <div class='fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity'></div>
  <div class='fixed inset-0 z-10 w-screen overflow-y-auto'>
    <div class='flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0'>
      <div class='relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg'>
      <div class='bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4'>
      <div class='sm:flex sm:items-start'>
      <div class='mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10'>
              <svg class='h-6 w-6 text-red-600' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' aria-hidden='true'>
                <path stroke-linecap='round' stroke-linejoin='round' d='M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z' />
                </svg>
                </div>
                <div class='mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left'>
                <h3 class='text-base font-semibold leading-6 text-gray-900' id='modal-title'>Atenção</h3>
                <div class='mt-2'>
                <p class='text-sm text-gray-500'>Redirecinaremos você para a pagina de login pois a senha foi alterada com sucesso, lembre-se guarde está senha somente para você e não divida a conta com terceiros.</p>
                </div>
                </div>
                </div>
                </div>
                </div>
                </div>
                </div>
                </div>
                <script>
                setTimeout(function() {
                  window.location.href = '../../index';
                }, 10000);
                </script>
                ";

  echo $html;
}

function cadastrarUsuario()
{
  global $conn;
  $conn = connection();

  $nome = $_POST['nome'];
  $email = $_POST['email'];
  $cpf = $_POST['cpf'];

  if (usuarioJaCadastrado($conn, 'nome', $nome)) {
    echo json_encode(['success' => false, 'messageerror' => 'Nome de usuário já cadastrado.']);
  } elseif (usuarioJaCadastrado($conn, 'email', $email)) {
    echo json_encode(['success' => false, 'messageerror' => 'Email de usuário já cadastrado.']);
  } elseif (usuarioJaCadastrado($conn, 'cpf', $cpf)) {
    echo json_encode(['success' => false, 'messageerror' => 'CPF de usuário já cadastrado.']);
  } else {
    $dataNascimento = $_POST['data'];
    $nivel = $_POST['nivel'];
    $senhaPadrao = "senha_padrao";
    $senhaPadraoHash = password_hash($senhaPadrao, PASSWORD_DEFAULT);
    $primeiroAcesso = '1';

    $sql = "CALL InserirUsuario(?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt === false) {
      die("Erro ao preparar a declaração: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "sssssss", $nome, $dataNascimento, $email, $cpf, $senhaPadraoHash, $nivel, $primeiroAcesso);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
      echo json_encode(['success' => true, 'messageSuccess' => 'Usuário cadastrado com sucesso']);
    } else {
      echo json_encode(['success' => false, 'messageerror' => 'Erro ao executar a declaração']);
    }

    mysqli_stmt_close($stmt);
  }

  mysqli_close($conn);
}
