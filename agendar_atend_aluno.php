<?php ob_start(); ?>
<!-- FORM PARA ESCOLHER PROFESSOR PARA O ATENDIMENTO -->
    <section class='selecionar fora'>
    <?php   
        include('bd/conexao.php');

        echo "<form method='post' class='selecionar-prof'>";
        echo "<h1>Selecione o professor </h1>";
        $sql = "SELECT DISTINCT ID_prof FROM tbturmadisciplinaprofessor 
                    WHERE deletado = false 
                    AND ID_turma IN ( SELECT ID_turma FROM tbturma_aluno
                        WHERE ID_aluno = ".$_SESSION['ID_usuario']."
                    )";
        $pega_idProf = mysqli_query($conexao, $sql);
        if ($pega_idProf) {
            while ($recebe_idProf = mysqli_fetch_array($pega_idProf)) {
                
                $sql = "SELECT nome FROM tbusuarios WHERE ID_usuario = ".$recebe_idProf[0];
                $pega_nomeProf = mysqli_query($conexao, $sql);
                if ($pega_nomeProf) {
                    while ($recebe_nomeProf = mysqli_fetch_array($pega_nomeProf)) {

                        echo "<label><input type='radio' name='ID_professor' value='".$recebe_idProf[0]."'> ".$recebe_nomeProf[0]."</label>";

                    }
                }

            }
        }
        echo "<button type='submit' class='envia' name='continuar' value='continuar'>Enviar</button>";
        echo "</form>";
    ?>
    </section>

<!-- FORM PARA ESCOLHER HORARIO E DATA DE ATENDIMENTO -->
    <?php
        if (isset($_POST['ID_professor']) && isset($_POST['continuar'])) {

            echo "<section class='form-cadastrar-horarios'>";
            echo "<form method='post' class='sel-hor'>";
            $sql = "SELECT DISTINCT dia FROM tbhor_livres 
                        WHERE deletado = false 
                        AND ID_prof = ".$_POST['ID_professor'];
            $pegaDia = mysqli_query($conexao, $sql);
            if ($pegaDia) {
                while ($recebeDia = mysqli_fetch_array($pegaDia)) {

                    echo "<r class='day'>".$recebeDia[0]."</r>";
                    $sql = "SELECT horario_ini, horario_fim, ID_hor_livre FROM tbhor_livres 
                                WHERE deletado = false 
                                AND ID_prof = ".$_POST['ID_professor']." 
                                AND dia = '".$recebeDia[0]."'";
                    $pegaHorarios = mysqli_query($conexao, $sql);
                    if ($pegaHorarios) {
                        while ($recebeHorarios = mysqli_fetch_array($pegaHorarios)) {

                            echo "<label><input type='radio' name='horario_aten' value='".$recebeHorarios[2]."'> ".$recebeHorarios[0]." - ".$recebeHorarios[1]."</label>";
                            
                        }
                    }

                }
            }

            //MOSTRA O CALENDÁRIO
            echo "<input class='data' required='' type='date' name='date'>";

            echo "<input type='hidden' name='prof' value='".$_POST['ID_professor']."'>";
            echo "<button type='submit' class='envia' name='foi' value='foi'>Continuar</button>";
            echo "</form>";
            echo "<section>";
        }
    ?>

<!-- BOTÕES PARA ESCOLHER SE O ATENDIMENTO SERÁ INDIVIDUAL OU EM GRUPO -->
    <?php
        if (isset($_POST['foi'])) {
           if(isset($_POST['date'])) {
               if(isset($_POST['horario_aten'])) {
                    echo "<section class='form-cadastrar-horarios'>";
                    echo "<form method='post' class='selecion'>";
                    echo "<h1>Tipo</h1>";
                    echo "<input type='hidden' name='date' value='".$_POST['date']."'>";
                    echo "<input type='hidden' name='horario_atend' value='".$_POST['horario_aten']."'>";
                    echo "<input type='hidden' name='prof' value='".$_POST['prof']."'>";

                    // BOTAO PARA ESCOLHER ATENDIMENTO INDIVIDUAL
                    echo "<button type='submit' class='tipo' name='tipo' value='individual'>Individual</button>";
                    // BOTAO PARA ESCOLHER ATENDIMENTO EM GRUPO
                    echo "<button type='submit' class='tipo' name='tipo' value='grupo'>Em Grupo</button>";
                    
                    echo "</form>";
                    echo "</section>";
               } else {
                echo "<script> alert('Não foi possível agendar. Nenhum horário foi selecionado.')</script>";
            }
               
           } else {
               echo "<script> alert('Não foi possível agendar. Nenhuma data foi selecionada.')</script>";
           }
            
        }
    ?>

<!-- FAZ A ROTA PARA OS DOIS CASOS (INDIVIDUAL OU EM GRUPO) -->
    <?php
        if (isset($_POST['tipo'])) {
            
            if ($_POST['tipo'] == "individual") {

                $_POST['date'] = $_POST['date'];
                $_POST['horario_atend'] = $_POST['horario_atend'];
                $_POST['alunos'] = [$_SESSION['ID_usuario']];
                $_POST['ID_profes'] = $_POST['prof'];
                $_POST['continue'] = 'continue';
            } else {
                echo "<section class='fundo-escuro'>";
                echo "<form method='post' class='seleciona'>";
                $sql = "SELECT ID_aluno FROM tbturma_aluno 
                            WHERE deletado = false 
                            AND ID_aluno <> ".$_SESSION['ID_usuario']." 
                            AND ID_turma IN (SELECT ID_turma FROM tbturma_aluno 
                                WHERE ID_aluno = ".$_SESSION['ID_usuario']."
                            )";
                $pega_idAluno = mysqli_query($conexao, $sql);
                if ($pega_idAluno) {
                    while ($recebe_idAluno = mysqli_fetch_array($pega_idAluno)) {
                    
                        $sql = "SELECT nome FROM tbusuarios WHERE ID_usuario = ".$recebe_idAluno[0];
                        $pega_nomeAluno = mysqli_query($conexao, $sql);
                        if ($pega_nomeAluno) {
                            while ($recebe_nomeAluno = mysqli_fetch_array($pega_nomeAluno)) {
                                //SE FOR EM GRUPO PEDE PARA ESCOLHER OS ALUNOS
                                echo "<label class='alns alns2'><input type='checkbox' name='alunos[]' value='".$recebe_idAluno[0]."'> ".$recebe_nomeAluno[0]."</label>";
                            }
                        }

                    }
                }
                echo "<input type='hidden' name='date' value='".$_POST['date']."'>";
                echo "<input type='hidden' name='horario_atend' value='".$_POST['horario_atend']."'>";
                echo "<input type='hidden' name='alunos[]' value='".$_SESSION['ID_usuario']."'>";
                echo "<input type='hidden' name='ID_profes' value='".$_POST['prof']."'>";
                        
                echo "<button type='submit' class='env envia' name='continue' value='continue'>Continuar</button>";
                echo "</form>";
                echo "</section>";
            }
            
        }
    ?>

<!-- FORM PARA ASSUNTO DO ATENDIMENTO -->
    <?php
        if(isset($_POST['continue']) && isset($_POST['alunos'])) {
            echo "<section class='form-cadastrar-horarios'>";
            echo "<form method='post' class='assunto'>";
            echo "<textarea name='assunto' placeholder='Assunto do atendimento'></textarea>";
            echo "<input type='hidden' name='date' value='".$_POST['date']."'>";
            echo "<input type='hidden' name='horario_atend' value='".$_POST['horario_atend']."'>";

            foreach ($_POST['alunos'] as $key => $value) {
                echo "<input type='hidden' name='alunos[]' value='".$value."'>";
            }
                
            echo "<input type='hidden' name='ID_profes' value='".$_POST['ID_profes']."'>";
            
            echo "<button type='submit' class='envia' name='continuar_' value='continuar_'>Enviar</button>";
            echo "</form>";
            echo "</section>";
        }
    ?>

<!-- RECEBE INFORMACOES PARA AGENDAR ATENDIMENTO -->
    <?php
            if (isset($_POST['continuar_'])) {
                
                

                if ($_POST['date'] > date('Y-m-d')) {
                    include_once('./utils/helpers.php');
                    // Formata a data em dd/mm/AAAA
                    
                    $data = $_POST['date'];
                    //$data = formatarData($data);
    
                    $sql = "SELECT horario_ini FROM tbhor_livres 
                                WHERE deletado = false 
                                AND ID_hor_livre = ".$_POST['horario_atend'];
                    $pegaHor = mysqli_query($conexao, $sql);
                    if ($pegaHor) {
                        
                        while ($recebeHor = mysqli_fetch_array($pegaHor)) {
    
                            // Ve se é maior do que a data atual
                            //$dataAtual = date('d/m/Y');
                            date_default_timezone_set('America/Sao_Paulo');
                            // HORA SEM HORARIO DE VERAO
                            $horarioAtual = date('H:i', strtotime('-1 hour'));

                            $dataAtual = date('Y-m-d');
                            $hora = date('H:i', strtotime($recebeHor[0]));
                            if ($data > $dataAtual || ($data == $dataAtual && $hora > $horarioAtual)) {
                                
                            // COMPARA COM AS DO BANCO
    
                            $sql = "SELECT data FROM tbalunoprofessor 
                                        WHERE deletado = false 
                                        AND ID_prof = ".$_POST['ID_profes'];
                            $pegaData = mysqli_query($conexao, $sql);
                            $j = 0;
                            if ($pegaData) {
                                $N_datas = mysqli_num_rows($pegaData);
    
                                if ($N_datas != 0) {
                                
                                    while ($recebeData = mysqli_fetch_array($pegaData)) {
                                        include_once('./utils/helpers.php');
                                        //$recebeDatas = formatarData($recebeData[0]);
                                        $recebeDatas = $recebeData[0];
                                        if ($recebeDatas == $data) {
                                            
                                            // SE EXISTE UM ATENDIMENTO NO MESMO DIA, COMPARA OS HORARIOS
                                            $sql = "SELECT DISTINCT ID_hor_livre FROM tbalunoprofessor 
                                                        WHERE deletado = false
                                                        AND ID_prof = ".$_POST['ID_profes']." 
                                                        AND data = '".$recebeData[0]."'";
                                            $pegaidHorLivre = mysqli_query($conexao, $sql);
                                            if ($pegaidHorLivre) {
                                                while ($recebeidhor = mysqli_fetch_array($pegaidHorLivre)) {
                                                    if ($recebeidhor[0] == $_POST['horario_atend']) {
                                                        $j += 1;
                                                    }
                                                }
                                            }
        
                                        } 
                                        
                                    }
                                }
                                
                                if ($j == 0) {
                        
                                    //valida e cadastra
                                    // ve se data e horario batem
                                    $sql = "SELECT DISTINCT dia FROM tbhor_livres 
                                                WHERE deletado = false 
                                                AND ID_prof = ".$_POST['ID_profes'];
                                    $pegaDia = mysqli_query($conexao, $sql);
                                    if ($pegaDia) {
                                        
                                        while ($recebeDia = mysqli_fetch_array($pegaDia)) {
                                            $dia = $recebeDia[0];
                                            $dia_pt = $dia;
                                            // transforma o dia para inglês para fazer a comparacao de dia livre em ingles com a funcao date() do PHP
                                            switch ($dia) {
                                                case 'seg':
                                                    $dia = 'Mon';
                                                    break;
                                                case 'ter':
                                                    $dia = 'Tue';
                                                    break;
                                                case 'qua':
                                                    $dia = 'Wed';
                                                    break;
                                                case 'qui':
                                                    $dia = 'Thu';
                                                    break;
                                                case 'sex':
                                                    $dia = 'Fri';
                                                    break;
                                            }
                                            // SE DIA ESCOLHIDO FOR IGUAL A UM DIA LIVRE DO BANCO...
                                            $data = $_POST['date'];
                                            $data = explode('-', $data);
                                            if ($dia == date('D', mktime(00,00,00,$data[1],$data[2],$data[0]))) {
                                                
                                                $sql = "SELECT DISTINCT ID_hor_livre, horario_ini, horario_fim FROM tbhor_livres 
                                                            WHERE deletado = false 
                                                            AND ID_prof = ".$_POST['ID_profes']." 
                                                            AND dia = '".$dia_pt."'";
                                                $pega_hor = mysqli_query($conexao, $sql);
                                                if ($pega_hor) {
                                                    while ($recebe_hor = mysqli_fetch_array($pega_hor)) {
    
                                                        if ($recebe_hor[0] == $_POST['horario_atend']) {
                                                
                                                            foreach ($_POST['alunos'] as $key => $value) {
                                                                // Vê se os outros alunos já não tem horarios marcados na mesma hora 
                                                                // pega o horario própriamente dito e a data e vê se o usuario tem um atendimento com essas informações
                                                                $sql = "SELECT ID_hor_livre FROM tbalunoprofessor 
                                                                            WHERE deletado = false 
                                                                            AND data = '".$_POST['date']."' 
                                                                            AND ID_aluno = ".$value."";
                                                                $conecta_hora = mysqli_query($conexao, $sql);
                                                                if ($conecta_hora) {
    
                                                                    if (mysqli_num_rows($conecta_hora) > 0) {
    
                                                                        while ($recebe_hora= mysqli_fetch_array($conecta_hora)) {
                                                                
                                                                            $sql = "SELECT horario_ini FROM tbhor_livres 
                                                                                        WHERE deletado = false
                                                                                        AND ID_hor_livre = ".$recebe_hora[0]." 
                                                                                        AND horario_ini = ".$recebe_hor[1];
                                                                            $conecta = mysqli_query($conexao, $sql);
                                                                            if ($conecta) {
                                                                                if(mysqli_num_rows($conecta) > 0) {
                                                                                    $sql = "SELECT ID_usuario FROM tbalunos 
                                                                                                WHERE deletado = false 
                                                                                                AND ID_aluno = ".$value;
                                                                                    $pega_id_usuario = mysqli_query($conexao, $sql);
                                                                                    if ($pega_id_usuario) {
                                                                                        while ($recebe_id_usuario = mysqli_fetch_array($pega_id_usuario)) {
                                                                                            $sql = "SELECT nome FROM tbusuarios 
                                                                                                        WHERE ID_usuario = ".$recebe_id_usuario[0];
                                                                                            $pega_nome_usuario = mysqli_query($conexao, $sql);
                                                                                            if ($pega_nome_usuario) {
                                                                                                while ($recebe_nome_usuario = mysqli_fetch_array($pega_nome_usuario)) {
                                                                                                    
                                                                                                    echo "<script> window.alert('O aluno ".$recebe_nome_aluno[0]." já possui um atendimento marcado para o dia ".$_POST['date']." às ".$recebe_hor[1]." ! Não foi possivel agendar para ele') </script>";
                
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                    
                                                                                } else {
                                                                                    if ($value == $_SESSION['ID_usuario']) {
                                                                                        
                                                                                        $sql = "INSERT INTO tbalunoprofessor (ID_aluno, ID_prof, ID_hor_livre, data, confirmado, assunto, ocorreu, deletado) values (".$value.",".$_POST['ID_profes'].", ".$recebe_hor[0].", '".$_POST['date']."', 'p_usu', '".$_POST['assunto']."', 'aindaN', false)";
                                                                                        $insere = mysqli_query($conexao, $sql);
                                                                                    } else {
                                                                                        $sql = "INSERT INTO tbalunoprofessor (ID_aluno, ID_prof, ID_hor_livre, data, confirmado, assunto, ocorreu, deletado) values (".$value.",".$_POST['ID_profes'].", ".$recebe_hor[0].", '".$_POST['date']."', 'aindaN', '".$_POST['assunto']."', 'aindaN', false)";
                                                                                        $insere = mysqli_query($conexao, $sql);
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
    
                                                                    } else {
    
                                                                    if ($value == $_SESSION['ID_usuario']) {
                                                                        $sql = "INSERT INTO tbalunoprofessor (ID_aluno, ID_prof, ID_hor_livre, data, confirmado, assunto, ocorreu, deletado) values (".$value.",".$_POST['ID_profes'].", ".$recebe_hor[0].", '".$_POST['date']."', 'p_usu', '".$_POST['assunto']."', 'aindaN', false)";
                                                                        $insere = mysqli_query($conexao, $sql);
                                                                    } else {
                                                                        $sql = "INSERT INTO tbalunoprofessor (ID_aluno, ID_prof, ID_hor_livre, data, confirmado, assunto, ocorreu, deletado) values (".$value.",".$_POST['ID_profes'].", ".$recebe_hor[0].", '".$_POST['date']."', 'aindaN', '".$_POST['assunto']."', 'aindaN', false)";
                                                                        $insere = mysqli_query($conexao, $sql);
                                                                        header('Refresh: 0');
                                                                    }
                                                                    echo "<script> window.alert('Atendimento Agendado') </script>";
                                                                } 
    
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            } else {
                                                //$dataEscolhida = $dia;
                                                //$diaEscolhido = date('D', mktime(00,00,00,$data[1],$data[2],$data[0]));

                                                switch ($dia) {
                                                    case 'Mon':
                                                        $dia = 'Segunda-feira';
                                                        break;
                                                    case 'Tue':
                                                        $dia = 'Terça-feira';
                                                        break;
                                                    case 'Wed':
                                                        $dia = 'Quarta-feira';
                                                        break;
                                                    case 'Thu':
                                                        $dia = 'Quinta-feira';
                                                        break;
                                                    case 'Fri':
                                                        $dia = 'Sexta-feira';
                                                        break;
                                                }

                                                switch (date('D', mktime(00,00,00,$data[1],$data[2],$data[0]))) {
                                                    case 'Mon':
                                                        $dat = 'Segunda-feira';
                                                        break;
                                                    case 'Tue':
                                                        $dat = 'Terça-feira';
                                                        break;
                                                    case 'Wed':
                                                        $dat = 'Quarta-feira';
                                                        break;
                                                    case 'Thu':
                                                        $dat = 'Quinta-feira';
                                                        break;
                                                    case 'Fri':
                                                        $dat = 'Sexta-feira';
                                                        break;
                                                    case 'Sat':
                                                        $dat = 'Sábado';
                                                        break;
                                                    case 'Sun': 
                                                        $dat = 'Domingo';
                                                    break;
                                                }

                                                //echo "<script> alert('Dia da semada incompatível! A data escolhida é em $dat, enquanto o horário escolhido é referente à $dia') </script>";
                                                
                                            }
                                        }
                                    }
                                } else {
                                    echo "<script> window.alert('Não foi possivel agendar! O horário escolhido não está disponível no momento para o professor.') </script>";

                                }
                            }
                        }
                    }
                }
                
            }else {
                    echo "<script> window.alert('Não foi possivel agendar, pois a data escolhida é anterior à data atual') </script>";
                }
        } 
    ?>