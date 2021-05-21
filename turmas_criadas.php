<?php ob_start(); ?>
<section class="barra-admin">
    <section class="fora">
    
        <?php
            include('bd/conexao.php');

            // Lista todas as turmas criadas do banco de dados
            echo "<section class='turmas'>";
            echo "<form method='post' class='centro form-entrar-turma'>";
            echo "<h1>Selecione uma turma para ver</h1>";
            $sql = "SELECT DISTINCT ano FROM tbturmas ORDER BY ano";
            $consulta = mysqli_query($conexao, $sql);
            if ($consulta) {
                echo "<select name='ano_turma_clicada'>";
                while ($recebe = mysqli_fetch_array($consulta)) {
                    echo "<option value='".$recebe[0]."'>".$recebe[0]."º </option>";
                }
                echo "</select>";
            }

            $sql = "SELECT DISTINCT eixo FROM tbturmas ORDER BY eixo";
            $consulta = mysqli_query($conexao, $sql);
            if ($consulta) {
                echo "<select name='eixo_turma_clicada' class='eixo_turma_clicada'>";
                while ($recebe = mysqli_fetch_array($consulta)) {
                    echo "<option value='".$recebe[0]."'>".$recebe[0]."</option>";
                }
                echo "</select>";
            }

            
            echo "<button type='submit' class='envia'>Ver Turma</button>";
            echo "</form>";
            echo "</section>";
                
            // Valida turma que vai mandar
            if(isset($_POST['eixo_turma_clicada']) && isset($_POST['ano_turma_clicada'])) {
                $_SESSION['ano'] = $_POST['ano_turma_clicada'];
                $_SESSION['eixo'] = $_POST['eixo_turma_clicada'];

                // Leva a pagina para onde clicado
                Header('Location: index_logado.php?pagina=turmas_admin');
            }
        ?>
    </section>
</section>