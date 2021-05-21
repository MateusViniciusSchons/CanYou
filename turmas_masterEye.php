<?php ob_start(); ?>
<section class='fora'>
    <section class="dentro">
<!-- MOSTRA O NOME DA TURMA -->
<h1 class='titulo_turma'>
    <?php 
    
        echo $_SESSION['ano']."º ";
        
        switch ($_SESSION['eixo']) {
            case 'Adm':
                echo "Administração";
                break;
            case 'Agro':
                echo "Agropecuária";
                break;
            case 'Alm':
                echo "Alimentos";
                break;
            case 'Info':
                echo "Informática";
                break;
        } 
    ?>
</h1>
<!--============================================================     ALUNOS     ============================================================-->

<!--MOSTRA OS ALUNOS DA TURMA-->
<section class="alunos">
    <h2>Alunos</h2>
    <?php
        require('bd/conexao.php');
        $sql = "SELECT ID_usuario, nome, email FROM tbusuarios AS usu 
                    WHERE deletado = false 
                    AND usu.ID_usuario IN (SELECT ID_aluno FROM tbturma_aluno AS TA
                        WHERE deletado = false 
                        AND TA.ID_turma IN (SELECT ID_turma FROM tbturmas AS turm
                            WHERE deletado = false 
                            AND turm.eixo = '".$_SESSION['eixo']."' AND turm.ano = '".$_SESSION['ano']."'
                        )
                    ) AND tipo='aluno'";
        $aluno = mysqli_query($conexao, $sql);
        if ($aluno) {
            if (mysqli_num_rows($aluno) > 0) {
                while ($recebe_aluno = mysqli_fetch_array($aluno)) {
                    echo "<section class='aluno'>";
                    echo "<section class='aluno1'>";
                    echo "<div class='infos-aluno'>";
                    echo "<ul class='infos_aluno'>";
                    echo "<div class='square'><i class='fas fa-user'></i></div>";
    
                    echo "<ul class='opcoes_aluno'>";
                    echo"</ul>";
                    echo "<li>".$recebe_aluno[1]."</li>";
                    echo "<li><r class='email'>".$recebe_aluno[2]."</r></li>";
                    echo "</ul>";
                    echo "</div>";
                    
                    
                    echo "<li><form class='infos-alterar-aluno' method='post'>";
                    echo "<input type='hidden' name='nome' value='".$recebe_aluno[1]."'>";
                    echo "<input type='hidden' name='email' value='".$recebe_aluno[2]."'>";
                    echo "<input type='hidden' name='ID_aluno' value='".$recebe_aluno[0]."'>";
                    echo "<button class='alterar-inf-usu' type='submit' name='ver-relatorio-aluno' value='sim'><i class='far fa-arrow-alt-circle-right'></i></button>";
                    echo "</form></li>";

                    echo "</section>";
                    echo "</section>";
                }
            } else {
                echo "<h3>Nenhum aluno criado nesta turma até o momento</h3>";
            }
            
        }

        
    ?>
</section>
<!--============================================================     OPCOES ALUNOS     ============================================================-->
<?php
    // ver relatórios de alunos<!-- REDIRECIONA PARA A PEGINA DE VER RELATORIO DE ALUNO QUANDO CLICADO -->
    if (isset($_POST['ver-relatorio-aluno'])) {
        $_SESSION['aluno-relatorio'] = $_POST['ID_aluno'];
        header('Location: index_logado.php?pagina=ver_relatorio_aluno_masterEye');
    }
?>
<!--============================================================     DISCIPLINAS     ============================================================-->
<h2 class='discc'>Disciplinas</h2>
<section class="disciplinas" style=" float: left">

    <?php
        require('bd/conexao.php');
//      SELECIONA AS DISCIPLINAS DA TURMA E O PROFESSOR LIGADO A CADA UMA
        $sql = "SELECT nome, ID_disc FROM tbdisciplinas AS disc 
                    WHERE deletado = false 
                    AND ID_disc IN ( SELECT ID_disc FROM tbturmadisciplinaprofessor AS TDP
                        WHERE deletado = false 
                        AND ID_turma IN ( SELECT ID_turma FROM tbturmas AS turm
                            WHERE ano = ".$_SESSION['ano']." 
                            AND eixo = '".$_SESSION['eixo']."'
                        )
                    )";
        $pega_discs = mysqli_query($conexao, $sql);
        if ($pega_discs) {
            if (mysqli_num_rows($pega_discs) > 0) {
            while ($recebe_discs = mysqli_fetch_array($pega_discs)) {
//              SELECIONA O PROFESSOR LIGADO A CADA DISCIPLINA
                $sql = "SELECT nome, email, ID_usuario FROM tbusuarios 
                            WHERE deletado = false 
                            AND ID_usuario IN ( SELECT ID_prof FROM tbturmadisciplinaprofessor AS TDP
                            WHERE deletado = false 
                            AND ID_disc = ".$recebe_discs[1]." 
                            AND TDP.ID_turma IN ( SELECT ID_turma FROM tbturmas AS turm
                                WHERE turm.eixo = '".$_SESSION['eixo']."' 
                                AND turm.ano = ".$_SESSION['ano'].")
                        );";
                $pega_prof = mysqli_query($conexao, $sql);
                if ($pega_prof) {
                    while ($recebe_prof = mysqli_fetch_array($pega_prof)) {
                        
                        echo "<section class='disciplina' style='float: left;'>";
                        echo "<div class='esquerda'>";
                        echo "<i class='fas fas3 fa-book'></i><h3>".$recebe_discs[0]."</h3>";
                        echo "<i class='fas fas3 fa-chalkboard-teacher'></i><h4>".$recebe_prof[0]."</h4>";
                        echo "</div>";

                        echo "<ul class='opcoes_disc'>";
                        echo "<i class='far fa-arrow-alt-circle-right'></i>";
                        echo "</ul>";
                        echo "</section>";
                    }
                }
            }
            } else {
                echo "<h2>Nenhuma matéria adicionada até o momento</h2>";
            }
        }
    ?>
<!--============================================================     OPCOES DISCIPLINAS     ============================================================-->

        <!--Ver relatório de toda a disciplina?-->
</section>
</section>
</section>