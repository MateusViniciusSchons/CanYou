<?php session_start(); ob_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
    <meta name="description" content="PPI-Site">
    <meta name="keywords" content="PPI, Iffarroupilha - Câmpus Santo Augusto, Nome do site">
    <meta name="robots" content="index, follow"> 
    <meta name="author" content="Mateus Viniciius Schons"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Site PPI</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Baloo|Baloo+Bhai|Fredoka+One|Gothic+A1:600|Julius+Sans+One|Luckiest+Guy|Poiret+One|Solway:500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
    <link rel="icon" href="img/icon.png">
</head>
<body class='body-logado'>
    <?php
    if ($_SESSION['logado'] != true) {
        echo "<script> window.location.href = 'index.php'; </script>";
    }
    ?>
    <!--CABEÇALHO-->
    <?php
        if ($_SESSION['tipo'] == "professor") {
            include("cabecalho_prof.php");
        } else if ($_SESSION['tipo'] == "administrador") {
            include("cabecalho_admin.php");
        } else if ($_SESSION['tipo'] == "aluno") {
            include("cabecalho_aluno.php");
        } else if ($_SESSION['tipo'] == "masterEye") {
            include("cabecalho_masterEye.php");
        }
    ?>
    <!--PAGINA-->
    <?php
        if (isset($_GET['pagina'])) {
            $page = $_GET['pagina'];
        } else {
            if ($_SESSION['tipo'] == "professor") {
                $page = "escolher_horarios";
            } else if ($_SESSION['tipo'] == "administrador") {
                $page = "turmas_criadas";
            } else if ($_SESSION['tipo'] == "aluno") {
                $page = "atendimentos_aluno";
            } else if ($_SESSION['tipo'] == "masterEye") {
                $page = "turmas_criadas_masterEye";
            }
            else {
                $page = "http://canyousite.000webhostapp.com/index";
            }
        }
        include($page.".php");
    ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>
//   ABRE FORM NO INICIO
    $('.turmas').hide();
    $('.turmas').show('meddium');

//   ABRE E FECHA MENU CELULAR
    $('.btn-close').hide();
    $('.menu').hide();

    $('.btn-menu').click(function() {
        $('.menu').show('slow');
        $('.btn-close').show();
    });

    $('.btn-close').click(function() {
        $('.menu').hide('slow');
        $('.btn-close').hide();
    });

//   ABRE E FECHA FORM DE ADICIONAR ALUNOS À TURMA
    $('.form-adc-aln').hide();

    $('.abrir-adc-alunos').click(function() {
        $('.form-adc-aln').show('slow');
    });

    $('.fechar-adc-alunos').click(function() {
        $('.form-adc-aln').hide('slow');
    });

//   ABRE E FECHA O FORM DE ALTERAR DADOS DE ALUNO
    $('.fechar-alterar-alunos').click(function() {
        $('#alterar-usuario').hide();
    });

/*//   ABRE E FECHA O INPUT DE DEFINIR SENHA
    $('.nova_senha').hide('slow');

    $('.def-senha').click(function() {
        $('.nova_senha').show('slow');
    });*/

//   ABRE E FECHA FORM PARA ADICIONAR DISCIPLINAS À TURMA
    $('.form-adc-disc-turma').hide();

    $('.abrir-adc-disciplina').click(function() {
        $('.form-adc-disc-turma').show();
    });

    $('.fechar-adc-disciplina').click(function() {
        $('.form-adc-disc-turma').hide();
    });

//  FECHA ADICIONAR PROFESSOR À TURMA
    $('.fechar-adc-prof-disciplina').click(function() {
        $('.adc-disc-prof').hide();
    });

//  FECHA ALTERAR PROFESSOR DA TURMA
    $('.fechar-alterar-prof-disc').click(function(){
        $('.alter-prof-disc').hide();
    });

//  ABRE E FECHA ADICIONAR HORARIO LIVRE
    $('.form-cad-hor-liv').hide();

    $('.cad-hor-livres').click(function() {
        $('.form-cad-hor-liv').show();
    });

    $('.fechar-cad-hor-liv').click(function() {
        $('.form-cad-hor-liv').hide();
    });

// ABRE E FECHA FORM PARA CADASTRAR MONITORIA
    $('.form-cadastrar-monitoria').hide();

    $('.cadastrar-monitoria').click(function() {
        $('.form-cadastrar-monitoria').show();
    });
</script>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>