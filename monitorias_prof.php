<?php ob_start(); ?>
<!-- MONITORIAS CADASTRADAS ----------------------------------------------------------------------------------------------------------------------------------------------------------->
    <section class="monitorias fora">
        <section class="monit">
            <h1>Monitorias cadastradas:</h1>
            <?php
                include('bd/conexao.php');

                // Lista as monitorias cadastradas pelo professor
                $sql = "SELECT data, horario, nome_aluno, local, ID_monitoria, disciplina FROM tbmonitorias 
                            WHERE deletado = false 
                            AND ID_prof = ".$_SESSION['ID_usuario']."";
                $pega_monitoria = mysqli_query($conexao, $sql);
                if ($pega_monitoria) {
                    if (mysqli_num_rows($pega_monitoria) > 0) {
                        while ($recebe_monitoria = mysqli_fetch_array($pega_monitoria)) {
                            echo "<section class='moni'>";
                            echo "Monitoria de ".$recebe_monitoria[5]." a ser realizada em ".$recebe_monitoria[0];
                            echo ", começa às ".$recebe_monitoria[1];
                            echo " com o monitor ".$recebe_monitoria[2];
                            echo ". <br>Local: ".$recebe_monitoria[3];
    
                            //FORM PARA ALTERAR/EXCLUIR MONITORIA
                            echo "<form method='post'>";
                            echo "<input type='hidden' name='id-monitoria' value='".$recebe_monitoria[4]."'>";
                            //BOTAO PARA ALTERAR INFORMACOES DA MONITORIA
                            echo "<button type='submit' name='alterar-monitoria' class='cancelar duplo' value='alterar'>Alterar</button>";
                            //BOTAO PARA EXCLUIR MONITORIA
                            echo "<button type='submit' name='excluir-monitoria' class='excluir duplo' value='excluir'>Excluir</button>";
                            
                            echo "</form>";
                            echo "</section>";
                        }
                    } else {
                        echo "<h6>Nenhuma monitoria cadastrada por você até momento</h6>";
                    }
                }
                // BOTAO PARA CADASTRAR MONITORIA
                echo "<form method='post'>";
                echo "<button class='cadastrar-monitoria' name='c_mon' value='sim'>Cadastrar Monitoria</button>";
                echo "</form>";
            ?>
        </section>
    </section>

<!-- FORM PARA CADASTRAR MONITORIA -->
    <?php
    if (isset($_POST['c_mon'])) {
        echo "<section class=' form-cadastrar-horarios'>";
        echo "<form method='post' class='form-cad-mon'>";
        echo "<input type='text' required='' class='texto' name='local' placeholder='local'>";
        echo "<input type='text' required='' class='texto' name='aluno' placeholder='Nome do aluno/monitor'>";
        //lista as disciplinas desse professor
        $sql = "SELECT distinct ID_disc FROM tbturmadisciplinaprofessor 
                    WHERE deletado = false 
                    AND ID_prof = ".$_SESSION['ID_usuario']." ";
        $pega_discs = mysqli_query($conexao, $sql);
        if ($pega_discs) {
            if(mysqli_num_rows($pega_discs) == 0) {
                echo "Você nao está em nenhuma disciplina, impossível criar monitoria!";
            } else {
                while ($recebe_discs = mysqli_fetch_array($pega_discs)) {
                    $sql = "SELECT nome, ID_disc FROM tbdisciplinas 
                                WHERE deletado = false 
                                AND ID_disc = ".$recebe_discs[0];
                    $pega_nome_disc = mysqli_query($conexao, $sql);
                    if ($pega_nome_disc) {
                        while ($recebe_nome_disc = mysqli_fetch_array($pega_nome_disc)) {
                            echo "<label class='rad'><input type='radio' required='' name='disc' value='".$recebe_nome_disc[0]."'> ".$recebe_nome_disc[0]."</option></label>";
                        }
                    }
                }
            }
            
        }
        
        echo "<textarea required='' name='descricao' placeholder='Descreva a monitoria' maxlength='250'></textarea>";
        echo "<button type='submit' class='envia' name='cadastrar-mon' value='cadastrar'>Continuar</button>";
        echo "</form>";
        echo "</section>";
    }
    ?>

<!-- FORM PARA ADICIONAR UM HORARIO À MONITORIA -->
    <?php
        
        if (isset($_POST['cadastrar-mon'])) {
            if(!$_POST['disc']) {
                echo "<script> window.alert('Nenhuma disciplina selecionada, impossível cadastrar monitoria') </script>";
                header('Refresh: 0');
            }
            $sql = "SELECT disciplina FROM tbmonitorias 
                        WHERE deletado = false 
                        AND ID_prof = ".$_SESSION['ID_usuario']." 
                        AND disciplina = '".$_POST['disc']."'";
            $pega_disc_igual = mysqli_query($conexao, $sql);
            if ($pega_disc_igual && mysqli_num_rows($pega_disc_igual) < 1) {
                echo "<section class='form-cadastrar-horarios'>";
                echo "<form method='post' class='infos-cad-mon'>";
                echo "<h1>Escolha os horarios</h1>";
                echo "data(s)<input type='text' required='' class='data' name='data' placeholder='ex: 19/02/2019 ou terça-feira'>";
                echo "<br>horario<input required='' class='horario' type='time' name='horario'>";
                echo "<input type='hidden' name='local' value='".$_POST['local']."'>";
                echo "<input type='hidden' name='aluno' value='".$_POST['aluno']."'>";
                echo "<input type='hidden' name='descricao' value='".$_POST['descricao']."'>";
                echo "<input type='hidden' name='disc' value='".$_POST['disc']."'>";
                echo "<button type='submit' class='envia' name='cadastrar-monit' value='cadastrar'>Continuar</button>";
                echo "</form>";
                echo "</section>";
            } else {
                echo "<script> window.alert('Ja existe monitoria criada por voce com esta materia') </script>";
            }
        }
        
    ?>

<!-- FORM PARA LISTAR AS TURMAS QUE O PROFESSOR PODE COMPARTILHAR A MONITORIA -->
    <?php
        if (isset($_POST['cadastrar-monit'])) {
            
            echo "<section class='form-cadastrar-horarios'>";
            echo "<form method='post' class='select-turmas'>";
            echo "<h1>Escolha as turmas para compartilhar</h1>";
            
            $sql = "SELECT DISTINCT ID_turma FROM tbturmadisciplinaprofessor 
                        WHERE deletado = false 
                        AND ID_prof = ".$_SESSION['ID_usuario']." 
                        AND ID_disc IN (SELECT ID_disc FROM tbdisciplinas 
                            WHERE deletado = false 
                            AND nome = '".$_POST['disc']."'
                        )";
            $pega_turmas = mysqli_query($conexao, $sql);
            if ($pega_turmas) {
                while ($recebe_turmas = mysqli_fetch_array($pega_turmas)) {
                    $sql = "SELECT eixo, ano FROM tbturmas 
                                WHERE ID_turma = ".$recebe_turmas[0];
                    $pega_infos_turma = mysqli_query($conexao, $sql);
                    if ($pega_infos_turma) {
                        while ($recebe_infos_turma = mysqli_fetch_array($pega_infos_turma)) {
                            $pega_infos_turma = mysqli_query($conexao, $sql);
                            if ($pega_infos_turma) {
                                if (mysqli_num_rows($pega_turmas) > 0) {
                                    while ($recebe_infos_turma = mysqli_fetch_array($pega_infos_turma)) {
                                        echo "<label><input type='checkbox' name='turmas[]' value='".$recebe_turmas[0]."'> ".$recebe_infos_turma[1]."º ".$recebe_infos_turma[0]."</label>";
                                    }
                                } else {
                                    echo "<h3>Não será possivel cadastrar a monitoria pois a matéria selecionada não está em nenhuma turma, clique na logo para sair da tela de cadastro.</h3>";
                                }
                            }
                        }
                    }
                }
            }
            echo "<input type='hidden' name='data' value='".$_POST['data']."'>";
            echo "<input type='hidden' name='horario' value='".$_POST['horario']."'>";
            echo "<input type='hidden' name='local' value='".$_POST['local']."'>";
            echo "<input type='hidden' name='aluno' value='".$_POST['aluno']."'>";
            echo "<input type='hidden' name='descricao' value='".$_POST['descricao']."'>";
            echo "<input type='hidden' name='disc' value='".$_POST['disc']."'>";
            echo "<button type='submit' class='envia' name='cadastrar-m' value='cadastrar'>Continuar</button>";
            echo "</form>";
            echo "</section>";
        }
    ?>

<!-- RECEBE INFORMACOES PARA CADASTRAR MONITORIA -->
    <?php
    
        if (isset($_POST['cadastrar-m'])) {
            if (isset($_POST['turmas'])) {
                // CADASTRA MONITORIA
                $sql = "INSERT INTO tbmonitorias (ID_prof, descricao, nome_aluno, local, disciplina, horario, data, deletado) VALUES (".$_SESSION['ID_usuario'].", '".$_POST['descricao']."', '".$_POST['aluno']."', '".$_POST['local']."', '".$_POST['disc']."', '".$_POST['horario']."', '".$_POST['data']."', false)";
                $insere = mysqli_query($conexao, $sql);
                // CADASTRA MONITORIA NAS TURMAS ESCOLHIDAS
                $sql = "SELECT ID_monitoria FROM tbmonitorias 
                            WHERE deletado = false
                            AND ID_prof = ".$_SESSION['ID_usuario']." 
                            AND nome_aluno = '".$_POST['aluno']."' 
                            AND disciplina = '".$_POST['disc']."'";
                $pegaIDmonitoria = mysqli_query($conexao, $sql);
                if ($pegaIDmonitoria) {
                    while ($recebeIDmonitoria = mysqli_fetch_array($pegaIDmonitoria)) {
                        foreach ($_POST['turmas'] as $key => $value) {
                            $sql = "INSERT INTO tbmonitoriaturma (ID_monitoria, ID_turma, deletado) values (".$recebeIDmonitoria[0].", ".$value.", false)";
                            $insere = mysqli_query($conexao, $sql);
                            header('Refresh: 0');
                        }
                    }
                }
            } else {
                echo "<script> window.alert('Não foi possível cadastrar, pois nenhuma turma foi selecionada')</script>";
            }
        }
    
    ?>

<!-- FORM PARA ALTERAR INFORMACOES DA MONITORIA -->
    <?php
        if (isset($_POST['alterar-monitoria'])) {
            $sql = "SELECT ID_monitoria, descricao, nome_aluno, local, disciplina, horario, data FROM tbmonitorias 
                        WHERE deletado = false 
                        AND ID_monitoria = ".$_POST['id-monitoria']."";
            $pega_infos_monitoria = mysqli_query($conexao, $sql);
            if ($pega_infos_monitoria) {
                while ($recebe_infos_monitoria = mysqli_fetch_array($pega_infos_monitoria)) {
                    echo "<section class='fundo-escuro'>";
                    echo "<form method='post' class='alt-mon'>";
                    echo "<label> local";
                    echo "<input class='inp' type='text' name='local' value='".$recebe_infos_monitoria[3]."'></label>";
                    echo "<label> monitor";
                    echo "<input type='text' class='inp' name='aluno' value='".$recebe_infos_monitoria[2]."'></label>";
                    $sql = "SELECT distinct ID_disc FROM tbturmadisciplinaprofessor 
                                WHERE deletado = false 
                                AND ID_prof = ".$_SESSION['ID_usuario']." 
                                AND ID_turma IS NOT NULL";
                    $pega_discs = mysqli_query($conexao, $sql);
                    if ($pega_discs) {
                        echo "<label class='disc'>Disciplina</label>";
                        while ($recebe_discs = mysqli_fetch_array($pega_discs)) {
                            $sql = "SELECT nome, ID_disc FROM tbdisciplinas 
                                        WHERE deletado = false 
                                        AND ID_disc = ".$recebe_discs[0];
                            $pega_nome_disc = mysqli_query($conexao, $sql);
                            if ($pega_nome_disc) {
                                while ($recebe_nome_disc = mysqli_fetch_array($pega_nome_disc)) {
                                    $checked_or_no = $recebe_nome_disc[0] == $recebe_infos_monitoria[4]? "checked=''": "";
                                    echo "<label class='rad'><input type='radio' name='disc' value='".$recebe_nome_disc[0]."' $checked_or_no > ".$recebe_nome_disc[0]."</label>";
                                }
                            }
                        }
                    }
                    echo "<label class='datas'> data";
                    echo "<input type='text' class='inp' name='data' value='".$recebe_infos_monitoria[6]."'></label>";
                    echo "<label class='datas'> horario";
                    echo "<input type='time' class='inp' name='horario' value='".$recebe_infos_monitoria[5]."'></label>";
                    echo "<label class='datas'> descrição";
                    echo "<textarea name='descricao' maxlength='250'>".$recebe_infos_monitoria[1]."</textarea>";
                    
                    echo "<input type='hidden' name='id-monitoria' value='".$recebe_infos_monitoria[0]."'>";
                    echo "<button type='submit' class='cancelar duplo' name='cancelar' value='cancelar'>Cancelar</button>";
                    echo "<button type='submit' class='confirmar duplo' name='continue' value='continue'>Continuar</button>";
                    echo "<input type='hidden' name='disc-anterior' value='".$recebe_infos_monitoria[4]."'>";
                    echo "</form>";
                    echo "</section>";
                }
            }
            
        }
    ?>

<!-- FORM PARA ESCOLHER AS TURMAS COMPARTILHADAS PARA ALTERAR MONITORIA -->
    <?php
        if (isset($_POST['continue'])) {
            
            $sql = "SELECT disciplina FROM tbmonitorias 
                        WHERE deletado = false 
                        AND ID_prof = ".$_SESSION['ID_usuario']." 
                        AND disciplina = '".$_POST['disc']."'";
            $pega_disc_igual = mysqli_query($conexao, $sql);
            /*if ($pega_disc_igual) {
                echo mysqli_fetch_array($pega_disc_igual);
                while ($recebe_disc_igual = mysqli_fetch_array($pega_disc_igual)) {
                    if ($recebe_d == $_POST['disc-anterior']) {
                        if (mysqli_num_rows($pega_disc_igual) <= 1) {
                            $J = 0;
                        }
                    } else {
                        if (mysqli_num_rows($pega_disc_igual) < 1) {
                            $J = 0;
                        }
                    }
                }
            }
                if ($J == 0) {*/
            if (mysqli_num_rows($pega_disc_igual) <= 1) {
                
                echo "<section class='alunos-selecionar'>";
                echo "<form method='post' class='alter-turmas'>";
                $sql = "SELECT DISTINCT ID_turma FROM tbturmadisciplinaprofessor 
                            WHERE deletado = false 
                            AND ID_prof = ".$_SESSION['ID_usuario'];
                $pega_turmas = mysqli_query($conexao, $sql);
                if ($pega_turmas) {
                    echo "<h1> turmas para compartilhar</h1>";
                    while ($recebe_turmas = mysqli_fetch_array($pega_turmas)) {
                        $sql = "SELECT eixo, ano FROM tbturmas WHERE ID_turma = ".$recebe_turmas[0];
                        $pega_infos_turma = mysqli_query($conexao, $sql);
                        if ($pega_infos_turma) {
                            while ($recebe_infos_turma = mysqli_fetch_array($pega_infos_turma)) {
                                $pega_infos_turma = mysqli_query($conexao, $sql);
                                if ($pega_infos_turma) {
                                    while ($recebe_infos_turma = mysqli_fetch_array($pega_infos_turma)) {

                                        //pega as turmas que ja esta compartilhada para deixar checkado
                                        $sql = "SELECT ID_turma FROM tbmonitoriaturma 
                                                    WHERE deletado = false 
                                                    AND ID_monitoria = ".$_POST['id-monitoria'];
                                        $pega_turma = mysqli_query($conexao, $sql);
                                        if ($pega_turma) {
                                            while ($recebe_turma = mysqli_fetch_array($pega_turma)) {
                                                $turmas[] = $recebe_turma[0];
                                            }
                                        }
                                        foreach ($turmas as $key => $value) {
                                            $checkado_ou_nao = $value == $recebe_turmas[0]? "checked=''": "";
                                        }
                                        echo "<label><input type='checkbox' name='turmas[]' value='".$recebe_turmas[0]."' $checkado_ou_nao > ".$recebe_infos_turma[1]."º ".$recebe_infos_turma[0]."</label>";
                                    }
                                }
                            }
                        }
                    }
                }

                // hiddens para novas infos monitoria
                echo "<input type='hidden' name='data' value='".$_POST['data']."'>";
                echo "<input type='hidden' name='horario' value='".$_POST['horario']."'>";
                echo "<input type='hidden' name='descricao' value='".$_POST['descricao']."'>";
                echo "<input type='hidden' name='id-monitoria' value='".$_POST['id-monitoria']."'>";
                echo "<input type='hidden' name='disc' value='".$_POST['disc']."'>";
                echo "<input type='hidden' name='local' value='".$_POST['local']."'>";
                echo "<input type='hidden' name='aluno' value='".$_POST['aluno']."'>";
                //fecha hiddens
                
                echo "<button type='submit' name='cancelar' class='cancel cancelar duplo' value='cancelar'>Cancelar</button>";
                echo "<button type='submit' class='confirmar duplo' name='alterar-infos-monitoria' value='alterar'>Alterar Dados</button>";

                echo "</form>";
                echo "</section>";
            } else {
                echo "<script> window.alert('Ja existe monitoria criada por voce com esta materia') </script>";
            }
        }
    ?>

<!-- RECEBE INFORMACOES PARA ALTERAR DADOS DA MONITORIA -->
    <?php
        if (isset($_POST['alterar-infos-monitoria'])) {
            $sql = "UPDATE tbmonitorias SET data = '".$_POST['data']."', horario = '".$_POST['horario']."', disciplina = '".$_POST['disc']."', local = '".$_POST['local']."', nome_aluno = '".$_POST['aluno']."', descricao ='".$_POST['descricao']."' WHERE ID_monitoria = ".$_POST['id-monitoria']."";
            $altera = mysqli_query($conexao, $sql);
            header('Refresh: 0');
        }
    ?>

<!-- FORM DE SEGURANÇA PARA EXCLUIR PROFESSOR -->
    <?php
        if (isset($_POST['excluir-monitoria'])) {
            echo "<section class='fundo-escuro'>";
            echo "<form method='post' class='exc-mon'>";
            echo "<h1>Deseja mesmo excluir a monitoria?</h1>";
            echo "<input type='hidden' name='id-monitoria' value='".$_POST['id-monitoria']."'>";
            echo "<button type='submit' class='cancelar duplo' name='cancelar' value='cancelar'>Cancelar</button>";
            echo "<button type='submit' class='excluir duplo' name='excluir-monit' value='excluir'>Excluir</button>";
            echo "</form>";
            echo "</section>";
        }
    ?>

<!-- RECEBE INFORMACOES PARA EXCLUIR MONITORIA -->
    <?php
        if (isset($_POST['excluir-monit'])) {
            $sql = "UPDATE tbmonitorias
                        SET deletado = true
                            WHERE ID_monitoria = ".$_POST['id-monitoria'];
            //$sql = "DELETE FROM tbmonitorias WHERE ID_monitoria = ".$_POST['id-monitoria'];
            $exclui = mysqli_query($conexao, $sql);

            $sql = "UPDATE tbmonitoriaturma
                        SET deletado = true
                            WHERE ID_monitoria = ".$_POST['id-monitoria'];
            //$sql = "DELETE FROM tbmonitoriaturma WHERE ID_monitoria = ".$_POST['id-monitoria'];
            $exclui = mysqli_query($conexao, $sql);

            header('Refresh: 0');
        }
    ?>