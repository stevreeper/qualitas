<!-- Add Jquery -->
<script src="<?php if($_SESSION['page'] == "home"){echo "./bibliotecas/jquery/jquery.min.js";}else{echo "../bibliotecas/jquery/jquery.min.js";} ?>"></script>
<!-- Add mask plugin -->
<script src="<?php if($_SESSION['page'] == "home"){echo "./bibliotecas/jquery/mask.js";}else{echo "../bibliotecas/jquery/mask.js";} ?>"></script>
<!-- Add bootstrap -->
<script src="<?php if($_SESSION['page'] == "home"){echo "./bibliotecas/jquery/popper.js";}else{echo "../bibliotecas/jquery/popper.js";} ?>" crossorigin="anonymous"></script>
<script src="<?php if($_SESSION['page'] == "home"){echo "./bibliotecas/bootstrap/bootstrap.min.js";}else{echo "../bibliotecas/bootstrap/bootstrap.min.js";} ?>" crossorigin="anonymous"></script>
<link rel="stylesheet" href="<?php if($_SESSION['page'] == "home"){echo "./bibliotecas/bootstrap/bootstrap.min.css";}else{echo "../bibliotecas/bootstrap/bootstrap.min.css";} ?>">
<!-- Add cropie -->
<link rel="stylesheet" href="<?php if($_SESSION['page'] == "home"){echo "./bibliotecas/croppie/croppie.css";}else{echo "../bibliotecas/croppie/croppie.css";} ?>"/>
<script src="<?php if($_SESSION['page'] == "home"){echo "./bibliotecas/croppie/croppie.js";}else{echo "../bibliotecas/croppie/croppie.js";} ?>"></script>
<!-- Add funções do firebase -->
<script src="https://www.gstatic.com/firebasejs/4.12.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.12.1/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.12.1/firebase-database.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.12.1/firebase-firestore.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.12.1/firebase-storage.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.12.1/firebase-messaging.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.12.1/firebase-functions.js"></script>
<!-- Add icons -->
<link href="<?php if($_SESSION['page'] == "home"){echo "./bibliotecas/googleIcons/icon.css";}else{echo "../bibliotecas/googleIcons/icon.css";} ?>" rel="stylesheet">
<!-- Add maps -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD_XmxvW05XB7WrV_lwhfYn-fzTAgfAYZ4" async defer></script>
<!-- Iniciar Firebase -->
<script>
  var config = {
    apiKey: "AIzaSyD_XmxvW05XB7WrV_lwhfYn-fzTAgfAYZ4",
    authDomain: "qualitas-24b79.firebaseapp.com",
    databaseURL: "https://qualitas-24b79.firebaseio.com",
    projectId: "qualitas-24b79",
    storageBucket: "qualitas-24b79.appspot.com",
    messagingSenderId: "347582656373"
  };
  firebase.initializeApp(config);

  //Pegar e mostrar mensagens existentes
  var msg = window.sessionStorage.getItem("msg");
  var msgType = window.sessionStorage.getItem("msgType");
  if(msg){
    if(msgType == 'err'){
      mensagemErr(msg, 1);
    }
    if(msgType == 'suc'){
      mensagemSuc(msg, 1);
    }
    sessionStorage.clear();
  }

  // Button login navBar
  $("#btnLoginNav").click(() => {
    showLoader();
    //Validar fomulário
    if ($("#formNav")[0].checkValidity()) {
      //Fazer login com firebase secundário
      var config = {
        apiKey: "AIzaSyD_XmxvW05XB7WrV_lwhfYn-fzTAgfAYZ4",
        authDomain: "qualitas-24b79.firebaseapp.com",
        databaseURL: "https://qualitas-24b79.firebaseio.com"
      };
      var secondaryApp = firebase.initializeApp(config, "Secondary");
      secondaryApp.auth().signInWithEmailAndPassword($("#emailInputNav").val(), $("#passInputNav").val())
      .then(()=>{
        //Caso o login seja um sucesso
        secondaryApp.auth().onAuthStateChanged(function(user) {
          if(user){
            if(user.emailVerified){
              //Caso o email tenha sido verificado login com o firebase primario
              firebase.auth().signInWithEmailAndPassword($("#emailInputNav").val(), $("#passInputNav").val())
              .then(()=>{
                hideLoader();
                $("#formNav").hide();
                window.sessionStorage.setItem('msg', 'Bem vindo ' + user.displayName + '!');
                window.sessionStorage.setItem('msgType', 'suc');
                secondaryApp.auth().signOut();
                window.location.reload();
              })
              .catch((err)=>{
                hideLoader();
                console.log(err);
                window.sessionStorage.setItem('msg', 'Erro ao fazer login. Tente novamente mais tarde.');
                window.sessionStorage.setItem('msgType', 'err');
                secondaryApp.auth().signOut();
                window.location.reload();
              })
            }else{
              //Caso o email não esteja verificado
              user.sendEmailVerification().then(()=>{
                hideLoader();
                window.sessionStorage.setItem('msg',"Seu email ainda não foi verificado! Um email foi enviado para verifica-lo.");
                window.sessionStorage.setItem('msgType', 'err');
                secondaryApp.auth().signOut();
                window.location.reload();
              })
            }
          }
        })
      })
      .catch(function(error) {
        //Caso ocorra algum erro no login
        hideLoader();
        console.log(error.message);
        mensagemErr("Email ou senha incorretos!", 1);
        $("#msgCross").click(()=>{
          window.location.href = "<?php if($_SESSION['page'] == "home"){echo "pages/login.php";}else{echo "../pages/login.php";}?>";
        })
      });
    } else {
      //Caso o forulário esteja preenchido incorretamente
      hideLoader();
      mensagemErr("Preencha todos os campos corretamente!", 1);
      $("#msgCross").click(()=>{
        window.location.href = "<?php if($_SESSION['page'] == "home"){echo "pages/login.php";}else{echo "../pages/login.php";}?>";
      })
    }
  })

  //Conferir se um usuário está logado e caso esteja colocar seu nome na navbar
  firebase.auth().onAuthStateChanged(function(user) {
    if(user){
      firebase.firestore().collection("usuarios").doc(user.uid).get().then((doc) => {
      //Usuário conectado
      console.log("Logged in");
      $("#dropdownNavConta").html(user.displayName);
      $('#bemVindoUsuario').html('Bem vindo ' + user.displayName);
      $('#formNav').hide();
    })
    }else{
      //Usuário desconectado
      console.log("Not logged in");
      $("#formNav").show();
      $('#divUsuario').hide();
    }
  })

  //Redefinir senha navbar
  $("#redefSenhaNav").click(()=>{
    //Validar campo de email
    if($("#emailInputNav")[0].checkValidity()){
      //Enviar email
      firebase.auth().sendPasswordResetEmail($("#emailInputNav").val())
      .then(function() {
        mensagemSuc("Email enviado com sucesso!", 1);
      }).catch(function(error) {
        mensagemErr("Erro ao enviar email!", 1);
      });
    }else{
      mensagemErr("Digite seu email!", 1);
    }
  })

  //Função sair navBar
  $('#btnSairNav').click(()=>{
    firebase.auth().signOut().then(()=>{
      window.location.reload();
    })
  })

  //Funções de mensagem
  function mensagemErr(msg, num){
    $('#msg' + num).html('<div class="alert alert-danger fade show">' + msg + '<button type="button" class="close" data-dismiss="alert" aria-label="Close" id="msgCross"><span aria-hidden="true">&times;</span></button></div>');
  }
  function mensagemSuc(msg, num){
    $('#msg' + num).html('<div class="alert alert-success fade show">' + msg + '<button type="button" class="close" data-dismiss="alert" aria-label="Close" id="msgCross"><span aria-hidden="true">&times;</span></button></div>');
  }
  function mensagemModErr(msg, num){
    $('#msgMod' + num).html('<div class="alert alert-danger fade show">' + msg + '<button type="button" class="close" data-dismiss="alert" aria-label="Close" id="msgCross"><span aria-hidden="true">&times;</span></button></div>');
  }
  function mensagemModSuc(msg, num){
    $('#msgMod' + num).html('<div class="alert alert-success fade show">' + msg + '<button type="button" class="close" data-dismiss="alert" aria-label="Close" id="msgCross"><span aria-hidden="true">&times;</span></button></div>');
  }

  //Loader1
  function showLoader(){
    $("#loaderDiv").css('display', 'flex');
  }
  function hideLoader(){
    $("#loaderDiv").hide();
  }

</script>
