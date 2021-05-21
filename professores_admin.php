<?php ob_start(); ?>
<!-- LISTA OS PROFESSORES AGUARDANDO CONFIRMAÇÃO -->
<section class="profss fora">

    <?php
        echo "<form method='post' class='profes-aindaN-confirm'>";
        echo "<h1 class='titulo-prof'>Aguardando Confirmação</h1>";
        include('bd/conexao.php');
        $sql = "SELECT nome, email, ID_usuario FROM tbusuarios 
                    WHERE deletado = false 
                    AND prof_confirmado = 'aindaN'";
        $pega_profes = mysqli_query($conexao, $sql);
        if ($pega_profes) {
            if(mysqli_num_rows($pega_profes) > 0) {
                while ($recebe_profes = mysqli_fetch_array($pega_profes)) {
                    echo "<label><i class='fas fas2 fa-chalkboard-teacher'></i>";
                    echo "<r class='direita'><input type='checkbox' class='profes' name='profs[]' value='".$recebe_profes[2]."' > ".$recebe_profes[0]." </r> <r class='email'>".$recebe_profes[1]."</r></label>";
                    
                } 
                echo "<button type='submit' class='n-conf excluir duplo' name='confirmar' value='nao'>Cancelar</button>";
                echo "<button type='submit' class='conf confirmar duplo' name='confirmar' value='confirmar'>Confirmar</button>";
            } else {
                echo "<h5>Nenhum professor aguardando confirmação no momento</h5>";
            }
        }
        
        echo "</form>";

    ?>

    <!-- CONFIRMAR OU NÃO UM PROFESSOR -->
        <?php
            if (isset($_POST['confirmar']) && isset($_POST['profs'])) {
                if ($_POST['confirmar'] == 'confirmar') {
                    foreach ($_POST['profs'] as $key => $value) {
                        $sql = "UPDATE tbusuarios SET prof_confirmado = 'sim' WHERE ID_usuario = ".$value."   AND tipo = 'professor'";
                        $altera_cinfirmacao = mysqli_query($conexao, $sql);
                        header('Refresh: 0');
                    }
                } else {
                    //se o professor for não confirmado, ele é excluido do sistema pois podem ocorrer casos de conta fake e o email é único
                    echo "<section class='n-conf-prof fundo-escuro'>";
                    echo "<form method='post' class='nao-conf-prof'>";
                    echo "<h2>Deseja mesmo excluir este professor?</h2>";
                    foreach ($_POST['profs'] as $key => $value) {
                        echo "<input type='hidden' name='professores[]' value='".$value."'>";
                    }
                    
                    echo "<button type='submit' class='cancelar duplo' name='cancelar' value='cancelar'>Cancelar</button>";
                    echo "<button type='submit' class='excluir duplo' name='excluir-infos-prof' value='sim'>Excluir</button>";
                    echo "</form>";
                    echo "</section>";
                }
            }
        ?>

    <!-- LISTA PROFESSORES CADASTRADOS E CONFIRMADOS -->
    
        <section class="profes-cadast ">
        
            <?php
                echo "<form method='post' class='profes-aindaN-confirm'>";
                echo "<h1 class='profs'>Professores</h1>";
                include('bd/conexao.php');
                $sql = "SELECT nome, email, ID_usuario FROM tbusuarios 
                            WHERE deletado = false 
                            AND prof_confirmado = 'sim' AND verificado = 'sim'";
                $pega_professor = mysqli_query($conexao, $sql);
                if ($pega_professor) {
                    while ($recebe_professor = mysqli_fetch_array($pega_professor)) {
                        echo "<label><i class='fas fas2 fa-chalkboard-teacher'></i><r class='direitaa'><input type='checkbox' class='profes' name='profes[]' value='".$recebe_professor[2]."' > ".$recebe_professor[0]." </r><br> <r class='email'>".$recebe_professor[1]."</r></label>";
                    } 
                    
                }
                echo "<input type='hidden' name='excluir-prof' value='sim'>";
                echo "<button type='submit' class='excluir duplo exc2'>Excluir</button>";
                echo "</form>";
            ?>
        </seciton>

    <!-- FORM DE SEGURANÇA PARA EXCLUIR PROFESSOR -->
    <?php
        if (isset($_POST['excluir-prof'])) {
            echo "<section class='n-conf-prof fundo-escuro'>";
            echo "<form method='post' class='nao-conf-prof'>";
            echo "<h2>Deseja mesmo excluir este(s) professor(es)?</h2>";
            foreach ($_POST['profes'] as $key => $value) {
                echo "<input type='hidden' name='professores[]' value='".$value."'>";
            }
            echo "<button type='submit' class='cancelar duplo cancel_a' name='cancelar' value='cancelar'>Cancelar</button>";
            echo "<button type='submit' class='excluir duplo excluir_a' name='excluir-infos-prof' value='sim'>Excluir</button>";
            echo "</form>";
            echo "</section>";
        }
    ?>
    <!-- RECEBE INFORMACOES PARA EXCLUIR PROFESSOR -->
        <?php
            if (isset($_POST['excluir-infos-prof'])) {
                foreach ($_POST['professores'] as $key => $ID_prof) {
                    $sql = "SELECT ID_usuario FROM tbusuarios 
                                WHERE deletado = false 
                                AND ID_usuario = ".$ID_prof;
                    $pega_usuario = mysqli_query($conexao, $sql);
                    if ($pega_usuario) {
                        while ($recebe_usuario = mysqli_fetch_array($pega_usuario)) {

                            // deleta o usuario
                            $sql = "UPDATE tbusuarios 
                                    SET deletado = true
                                        WHERE ID_usuario = ".$ID_prof;
                            //$sql = "DELETE FROM tbusuarios WHERE ID_usuario = ".$ID_prof;
                            $deleta = mysqli_query($conexao, $sql);

                            // deleta as monitorias
                            $sql = "UPDATE tbmonitorias 
                                    SET deletado = true
                                        WHERE ID_prof = ".$ID_prof;
                            //$sql = "DELETE FROM tbmonitorias WHERE ID_prof = ".$ID_prof;
                            $deleta = mysqli_query($conexao, $sql); 

                            // se tem apenas este professor em uma disciplina, apaga a disciplina junto
                            $sql = "SELECT ID_disc FROM tbturmadisciplinaprofessor AS TDP
                                        WHERE TDP.deletado = false 
                                        AND TDP.ID_prof = ".$ID_prof."";
                            $pega_discs = mysqli_query($conexao, $sql);
                            if ($pega_discs) {
                                while ($recebe_discs = mysqli_fetch_array($pega_discs)) {
                                    $sql = "SELECT ID_prof FROM tbturmadisciplinaprofessor 
                                                WHERE deletado = false 
                                                AND ID_disc = ".$recebe_discs[0]."";
                                    $pega_profes = mysqli_query($conexao, $sql);
                                    if ($pega_profes) {
                                        if (mysqli_num_rows($pega_profes) < 2) {
                                            $sql = "UPDATE tbdisciplinas 
                                                    SET deletado = true
                                                        WHERE ID_disc = ".$recebe_discs[0];
                                            //$sql = "DELETE FROM tbdisciplinas WHERE ID_disc = ".$recebe_discs[0];
                                            $deleta = mysqli_query($conexao, $sql);
                                        }
                                    }
                                }
                            }

                            // deleta a disciplina que ele ministra DA turma
                            $sql = "UPDATE tbturmadisciplinaprofessor 
                                    SET deletado = true
                                        WHERE ID_professor = ".$ID_prof;
                            //$sql = "DELETE FROM tbturmadisciplinaprofessor WHERE ID_professor = ".$ID_prof;
                            $deleta = mysqli_query($conexao, $sql);

                            // deleta as monitorias criadas pelo professor
                            $sql = "SELECT ID_monitoria FROM tbmonitorias 
                                        WHERE deletado = false 
                                        AND ID_prof = ".$ID_prof."";
                            $pega_monitorias = mysqli_query($conexao, $sql);
                            if ($pega_monitorias) {
                                while ($recebe_monitorias = mysqli_fetch_array($pega_monitorias)) {
                                    
                                    // deleta a monitoria das turmas
                                    $sql = "UPDATE tbmonitoriaturma 
                                            SET deletado = true
                                                WHERE ID_monitoria = ".$recebe_monitorias[0];
                                    //$sql = "DELETE FROM tbmonitoriaturma WHERE ID_monitoria = ".$recebe_monitorias[0];
                                    $deleta = mysqli_query($conexao, $sql);

                                    // deleta a monitoria
                                    $sql = "UPDATE tbmonitorias 
                                            SET deletado = true
                                                WHERE ID_monitoria = ".$recebe_monitorias[0];
                                    //$sql = "DELETE FROM tbmonitorias WHERE ID_monitoria = ".$recebe_monitorias[0];
                                    $deleta = mysqli_query($conexao, $sql);

                                }
                            }

                            // deleta os horarios livres
                            $sql = "UPDATE tbhor_livres 
                                    SET deletado = true
                                        WHERE ID_prof = ".$ID_prof;
                            //$sql = "DELETE FROM tbhor_livres WHERE ID_prof = ".$ID_prof;
                            $deleta = mysqli_query($conexao, $sql);

                            // deleta os atendimentos que ainda não ocorreram
                            $sql = "UPDATE tbalunoprofessor 
                                    SET deletado = true
                                        WHERE ID_prof = ".$ID_prof." AND ocorreu <> 'sim' ";
                            //$sql = "DELETE FROM tbalunoprofessor WHERE ID_prof = ".$ID_prof." AND ocorreu <> 'sim' ";
                            $deleta = mysqli_query($conexao, $sql);
                            header('Refresh: 0');
                        }
                    }
                }
            }
        ?>
</section>