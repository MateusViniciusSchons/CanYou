<?php ob_start(); ?>
<!-- EXIBE AS TURMAS QUE O PROFESSOR PARTICIPA -->
    <section class="turmas-prof fora">    
        
        <?php
        include('bd/conexao.php');
            $sql = "SELECT ID_disc, ID_turma FROM tbturmadisciplinaprofessor 
                        WHERE deletado = false 
                        AND ID_prof = ".$_SESSION['ID_usuario'];
            $pega_id_turma = mysqli_query($conexao, $sql);
            if ($pega_id_turma) {
                if(mysqli_num_rows($pega_id_turma) <= 0) {
                    echo "<h5>Você ainda não foi adicionado à nenhuma turma</h5>";
                }
                while ($recebe_id_turma = mysqli_fetch_array($pega_id_turma)) {

                    $sql = "SELECT eixo, ano FROM tbturmas WHERE ID_turma = ".$recebe_id_turma[1];
                    $pega_nome_turma = mysqli_query($conexao, $sql);
                    if ($pega_nome_turma) {
                        while ($recebe_nome_turma = mysqli_fetch_array($pega_nome_turma)) {
                            echo "<section class='turma'>";
                            echo "<h2 class='t'>Turmas</h2>";
                            echo "<h5 class='tu'>";
                            echo "<br><br>".$recebe_nome_turma[1]."º ".$recebe_nome_turma[0];
                            
                            $sql = "SELECT nome FROM tbdisciplinas WHERE ID_disc = ".$recebe_id_turma[0];
                            $pega_nome_disc = mysqli_query($conexao, $sql);
                            if ($pega_nome_disc) {
                                while ($recebe_nome_disc = mysqli_fetch_array($pega_nome_disc)) {
                                    echo ": ".$recebe_nome_disc[0];
                                }
                            }
                            echo "</h5>";
                            // BOTAO PARA ENTRAR NA TURMA
                            echo "<form method='post'>";
                            echo "<input type='hidden' name='ano-turma-clicada' value='".$recebe_nome_turma[1]."'>";
                            echo "<input type='hidden' name='eixo-turma-clicada' value='".$recebe_nome_turma[0]."'>";
                            echo "<button type='submit' class='entra-na-turma confirmar duplo'>";
                            echo "Ver turma";
                            echo "</button>";
                            echo "</form>";
                            // BOTAO PARA AGENDAR ATENDIMENTO
                            echo "<form method='post'>";
                            echo "<input type='hidden' name='ano-turma' value='".$recebe_nome_turma[1]."'>";
                            echo "<input type='hidden' name='eixo-turma' value='".$recebe_nome_turma[0]."'>";
                            echo "<button type='submit' class='agendar confirmar duplo'>Agendar atendimento</button>";
                            echo "</form>";
                            echo "</section>";
                        }
                    }
                }
            }
        ?>
    </section>

<!-- RECEBE INFORMACOES PARA VER A TURMA -->
    <?php
        if (isset($_POST['ano-turma-clicada']) && isset($_POST['eixo-turma-clicada'])) {
            $_SESSION['ano'] = $_POST['ano-turma-clicada'];
            $_SESSION['eixo'] = $_POST['eixo-turma-clicada'];
            // redireciona para a pagina da turma
            Header('Location: index_logado.php?pagina=turmas_prof');
        }            
    ?>

<!-- FORM PARA AGENDAR ATENDIMENTOS -->
    <?php
        if (isset($_POST['ano-turma']) && isset($_POST['eixo-turma'])) {
            echo "<section class='alunos-selecionar fundo-escuro'>";
            echo "<form method='post' class='selecionar-aln'>";
            echo "<label><input type='radio' name='selecionar' value='turmaToda' checked=''>Turma toda</label>";
            echo "<label><input type='radio' name='selecionar' value='alguns'>Selecionar Alunos</label>";
            echo "<input type='hidden' name='ano-turminha' value='".$_POST['ano-turma']."'>";
            echo "<input type='hidden' name='eixo-turminha' value='".$_POST['eixo-turma']."'>";
            echo "<button type='submit' class='envia' name='cancelar' value='sim'>Cancelar</button>";
            echo "<button type='submit' class='envia' name='proximo' value='proximo'>Continuar</button>";
            echo "</form>";
            echo "</section>";
        }
    ?>

<!-- FORM PARA CONTINUAR AGENDANDO ATENDIMENTO -->
    <?php
        if (isset($_POST['proximo']) && isset($_POST['selecionar'])) {
            if($_POST['selecionar'] == "alguns") {
                echo "<section class='fundo-escuro'>";
                echo "<form method='post' class='escolher-participantes'>";
                echo "<input type='hidden' name='ano-turminha' value='".$_POST['ano-turminha']."'>";
                echo "<input type='hidden' name='eixo-turminha' value='".$_POST['eixo-turminha']."'>";
                // mostra os alunos que podem participar do atendimento
                $sql = "SELECT nome, email, ID_usuario FROM tbusuarios 
                            WHERE deletado = false 
                            AND ID_usuario IN (SELECT ID_aluno FROM tbturma_aluno 
                                WHERE deletado = false 
                                AND ID_turma IN (SELECT ID_turma FROM tbturmas 
                                    WHERE ano = ".$_POST['ano-turminha']." 
                                    AND eixo = '".$_POST['eixo-turminha']."'
                                )
                            )";
                $pega_alunos = mysqli_query($conexao, $sql);
                if ($pega_alunos) {
                    if(mysqli_num_rows($pega_alunos) > 0) {
                        while ($recebe_alunos = mysqli_fetch_array($pega_alunos)) {
                            echo "<label><input type='checkbox' name='aluno[]' value='".$recebe_alunos[2]."'>";
                            echo $recebe_alunos[0]."</label>";
                        }
                        echo "<button type='submit' class='envia' name='continue' value='continue'>Continuar</button>";
                    } else {
                        echo "ainda não há alunos nesta turma";
                    }
                }
                echo "<button type='submit' class='envia' name='cancelar' value='sim'>Cancelar</button>";
                echo "</form>";
                echo "</section>";
            } else {
                $sql = "SELECT ID_usuario FROM tbusuarios 
                            WHERE deletado = false 
                            AND ID_usuario IN (SELECT ID_aluno FROM tbturma_aluno 
                                WHERE deletado = false 
                                AND ID_turma IN (SELECT ID_turma FROM tbturmas 
                                    WHERE ano = ".$_POST['ano-turminha']." 
                                    AND eixo = '".$_POST['eixo-turminha']."'
                                )
                            )";
                $pega_alunos = mysqli_query($conexao, $sql);
                if ($pega_alunos) {
                    while ($recebe_alunos = mysqli_fetch_array($pega_alunos)) {
                        $_POST['aluno'] []= $recebe_alunos[0];
                        $_POST['ano-turminha'] = $_POST['ano-turminha'];
                        $_POST['eixo-turminha'] = $_POST['eixo-turminha'];
                        $_POST['continue'] = 'continue';
                    }
                }
                
            }
        }
    ?>

<!-- FORM PARA ESCOLHER A DATA E O DIA DO ATENDIMENTO -->
    <?php
        if (isset($_POST['continue'])) {
            echo "<section class='fundo-escuro'>";
            echo "<form method='post' class='infos'>";
            echo "<h3>Selecione a data de atendimento</h3>";
            echo "<h5>* A data deve ser compatível com o dia da semana do horário escolhido</h5>";
            echo "<input type='date' name='date' required class='data'>";
            // mostra os dias e horarios disponiveis cadastrados pelo professor
            $sql = "SELECT DISTINCT dia FROM tbhor_livres 
                        WHERE deletado = false 
                        AND ID_prof = ".$_SESSION['ID_usuario'];
            $pega_infos_dia = mysqli_query($conexao, $sql);
            if ($pega_infos_dia) {
                while ($recebe_infos_dia = mysqli_fetch_array($pega_infos_dia)) {
                    echo "<h6> ".$recebe_infos_dia[0]."</h6> ";
                    $sql = "SELECT horario_ini, horario_fim, ID_hor_livre FROM tbhor_livres 
                                WHERE deletado = false 
                                AND ID_prof = ".$_SESSION['ID_usuario']." 
                                AND dia = '".$recebe_infos_dia[0]."'";
                                $pega_infos_horarios = mysqli_query($conexao, $sql);
                                if ($pega_infos_horarios) {
                                    while ($recebe_infos_horarios = mysqli_fetch_array($pega_infos_horarios)) {
                                        echo "<label class='horario'><input type='radio' name='horario-atend' value='".$recebe_infos_horarios[2]."'>".$recebe_infos_horarios[0]." - ".$recebe_infos_horarios[1]."</label>";
                                    }
                                }
                }
            }
            foreach ($_POST['aluno'] as $key => $value) {
                echo "<input type='hidden' name='aluno[]' value='".$value."'>";
            }
            echo "<input type='hidden' name='ano-turminha' value='".$_POST['ano-turminha']."'>";
            echo "<input type='hidden' name='eixo-turminha' value='".$_POST['eixo-turminha']."'>";
            echo "<button type='submit' class='envia' name='cadastra-atend' value='cadastra'>Continuar</button>";
            echo "</form>";
            echo "</section>";
        }
    ?>

<!-- FORM DE OBSERVAÇÕES PARA O ATENDIMENTO -->
    <?php
        if (isset($_POST['cadastra-atend'])) {
            if(!isset($_POST['horario-atend'])) {
                echo "<script> alert('Não foi possível continuar, o horário não foi selecionado')</script>";
                header('Refresh: 0');
            } else {
                echo "<section class='fundo-escuro'>";
                echo "<form method='post' class='observ'>";
                echo "<textarea name='obs' class='text-area' placeholder='Observações sobre local, materiais a serem levados, etc.' maxlength='100'></textarea>";
                echo "<input type='hidden' name='date' value='".$_POST['date']."'>";
                echo "<input type='hidden' name='horario-atend' value='".$_POST['horario-atend']."'>";
                echo "<input type='hidden' name='ano-turminha' value='".$_POST['ano-turminha']."'>";
                echo "<input type='hidden' name='eixo-turminha' value='".$_POST['eixo-turminha']."'>";
                foreach ($_POST['aluno'] as $key => $value) {
                    echo "<input type='hidden' name='aluno[]' value='".$value."'>";
                }
                echo "<button type='submit' class='envia' name='cadastrar-atendimento' value='cadastra'>Continuar</button>";
                echo "</form>";
                echo "</section>";
            }
            
        }
    ?>

<!-- RECEBE INFORMACOES PARA CADASTRAR ATENDIMENTO E VALIDA-AS -->
    <?php
        if (isset($_POST['cadastrar-atendimento'])) {
            if(isset($_POST['aluno'])) {
                $data = $_POST['date'];
            
                // COMPARA COM AS DO BANCO
                $sql = "SELECT data FROM tbalunoprofessor 
                            WHERE deletado = false  
                            AND ID_prof = ".$_SESSION['ID_usuario'];
                $pegaData = mysqli_query($conexao, $sql);
                if ($pegaData) {
                    
                    // se ja tiver esta data em atendimentos
                    $j = 0;
                    if (mysqli_num_rows($pegaData) != 0) {
                        
                        while ($recebeData = mysqli_fetch_array($pegaData)) {

                            $recebeDatas = $recebeData[0];
                            if ($recebeDatas == $data) {
                                // SE EXISTE UM ATENDIMENTO NO MESMO DIA, COMPARA OS HORARIOS
                                $sql = "SELECT DISTINCT ID_hor_livre FROM tbalunoprofessor 
                                            WHERE deletado = false 
                                            AND ID_prof = ".$_SESSION['ID_usuario']." 
                                            AND data = '".$recebeData[0]."'";
                                $pegaidHorLivre = mysqli_query($conexao, $sql);
                                if ($pegaidHorLivre) {
                                    while ($recebeidhor = mysqli_fetch_array($pegaidHorLivre)) {
                                        if ($recebeidhor[0] == $_POST['horario-atend']) {
                                            $j += 1;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($j == 0) {
                        // se nao tem horario igual na mesma data, cadastra

                        // ve se data e horario batem
                        $sql = "SELECT dia, ID_hor_livre FROM tbhor_livres 
                                    WHERE deletado = false 
                                    AND ID_prof = ".$_SESSION['ID_usuario'];
                        $pegaDia = mysqli_query($conexao, $sql);
                        if ($pegaDia) {
                            $horariosBatem = 1;
                            while ($recebeDia = mysqli_fetch_array($pegaDia)) {
                                $dia = $recebeDia[0];
                                $dia_pt = $dia;
                                // muda pro ingles para comparar com os dias da funcao date
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
                                $da = $data;
                                $data = explode('-', $data);

                                if ($dia == date('D', mktime(00,00,00,$data[1],$data[2],$data[0])) && $_POST['horario-atend'] == $recebeDia[1]) {
                                    //cadastra atendimento com todos os alunos envolvidos
                                    $horariosBatem = 0;
                                    
                                    if (!isset($_POST['obs'])) {
                                        $_POST['obs'] = " Peço seu comparecimento. ";
                                    }

                                    foreach ($_POST['aluno'] as $key => $value) {

                                        //Pega o nome do aluno
                                        $sql = "SELECT nome FROM tbusuarios
                                                    WHERE ID_usuario = ". $value ."";
                                        $pega_nome = mysqli_query($conexao, $sql);
                                        while($nome_aluno = mysqli_fetch_array($pega_nome)) {
                                            $nome = $nome_aluno[0];
                                            
                                            //Vê se o aluno tem atendimento marcado para o mesmo dia
                                            $sql = "SELECT ID_hor_livre FROM tbalunoprofessor 
                                                        WHERE ID_aluno = ". $value ." 
                                                        AND data = '". $da ."' 
                                                        AND deletado = false 
                                                        AND ocorreu = 'aindaN'";

                                            $cons = mysqli_query($conexao, $sql);
                                            if(mysqli_num_rows($cons) > 0) {
                                                // se tiver, vê se tem para o mesmo horário
                                                $sql = "SELECT horario_ini, horario_fim FROM tbhor_livres
                                                            WHERE ID_hor_livre = ". $recebeDia[1] ."";
                                                
                                                $pega_hor_agendar = mysqli_query($conexao, $sql);
                                                while($rec_hor_agendar = mysqli_fetch_array($pega_hor_agendar)) {
                                                    
                                                    $sql = "SELECT ID_hor_livre FROM tbhor_livres
                                                            WHERE horario_ini = '". $rec_hor_agendar[0] ."' 
                                                            AND horario_fim = '". $rec_hor_agendar[1] ."'";
                                                    $pega_hor_ja_agendado = mysqli_query($conexao, $sql);
                                                    if(mysqli_num_rows($pega_hor_ja_agendado) > 0) {
                                                        echo "<script> alert('O aluno ". $nome ." já tem um atendimento agendado para este horário. Não foi possivel agendar para ele')</script>";
                                                    } else {
                                                        $sql = "INSERT INTO tbalunoprofessor (ID_aluno, ID_prof, ID_hor_livre, data, confirmado, obs, assunto, ocorreu, deletado) VALUES (".$value.",".$_SESSION['ID_usuario'].", ".$recebeDia[1].", '".$da."', 'sim', '".$_POST['obs']."', '', 'aindaN', false)";
                                                        $insere = mysqli_query($conexao, $sql);
                                                        echo "<script> alert('Atendimento(s) agendado(s) com sucesso.')</script>";
                                                    }
                                                } 
                                            } else {
                                                $sql = "INSERT INTO tbalunoprofessor (ID_aluno, ID_prof, ID_hor_livre, data, confirmado, obs, assunto, ocorreu, deletado) VALUES (".$value.",".$_SESSION['ID_usuario'].", ".$recebeDia[1].", '".$da."', 'sim', '".$_POST['obs']."', '', 'aindaN', false)";
                                                $insere = mysqli_query($conexao, $sql);
                                                echo "<script> alert('Atendimento(s) agendado(s) com sucesso.')</script>";
                                            }
                                        }
                                    }
                                }
                            }
                            if($horariosBatem !== 0) {
                                echo "<script> alert('Não foi possível agendar. Data escolhida não bate com o dia da semana escolhido.')</script>";
                            }
                        }
                    } else {
                        echo "<script> alert('Não foi possivel cadastrar atendimento. Você já possui atendimento marcado para este horário.')</script>";
                    }
                } 
            } else {
                echo "<script> alert('Não foi possivel cadastrar atendimento. Nenhum aluno foi selecionado.')</script>";

            }
        }
    ?>   