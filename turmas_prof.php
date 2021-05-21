<?php ob_start(); ?>
<!-- MOSTRA O NOME DA TURMA -->
<section class="fora">

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

<!-- MOSTRA OS ALUNOS DA TURMA -->
    <?php
        include('bd/conexao.php');
        $sql = "SELECT ID_aluno FROM tbturma_aluno 
                    WHERE deletado = false 
                    AND ID_turma IN ( SELECT ID_turma FROM tbturmas
                        WHERE eixo = '".$_SESSION['eixo']."' 
                        AND ano = ".$_SESSION['ano']."
        )";
        $pega_id_alunos = mysqli_query($conexao, $sql);
        if ($pega_id_alunos) {
            if(mysqli_num_rows($pega_id_alunos) > 0) {
                while ($recebe_id_alunos = mysqli_fetch_array($pega_id_alunos)) {
    
                    $sql = "SELECT nome, email FROM tbusuarios 
                                WHERE deletado = false 
                                AND ID_usuario = ".$recebe_id_alunos[0];
                    $pega_infos_user = mysqli_query($conexao, $sql);
                    if ($pega_infos_user) {
                        while ($recebe_infos_user = mysqli_fetch_array($pega_infos_user)) {
                            echo "<section class='alns'><i class='fas fas2 fa fa-user'></i>".$recebe_infos_user[0]." <br> <r class='email'> ".$recebe_infos_user[1]."</r>";
    
                            echo "<form method='post'>";
                            echo "<input type='hidden' name='ID-aluno' value='".$recebe_id_alunos[0]."'>";
                            echo "<button type='submit' class='ver-relatorio ' name='ver_relatorio' value='ver_relatorio'>Ver Relatório</button>";
                            echo "</form>";
                            echo "</section>";
                        }
                    }
                }    
            } else {
                echo "<h4>Ainda não há alunos nesta turma.</h4>";
            }
        }
    ?>

<!-- REDIRECIONA PARA A PEGINA DE VER RELATORIO DE ALUNO QUANDO CLICADO -->
    <?php
        if (isset($_POST['ver_relatorio'])) {
            $_SESSION['aluno-relatorio'] = $_POST['ID-aluno'];
            header('Location: index_logado.php?pagina=ver_relatorio');
        }
    ?>
</section>