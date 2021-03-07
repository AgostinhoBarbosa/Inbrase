<?php
    session_start();
    $token       = "0e770eb2861f3be266de2190b8526d9f";
    $senhaAcesso = 'dl09f3a';
    $tokenValido = FALSE;
    if ( $_REQUEST[ 'token' ] == $token ) {
        $tokenValido = TRUE;
    }

    if ( $_REQUEST[ 'senhaAcesso' ] == $senhaAcesso && empty($_SESSION[ 'usuarioValido' ]) ) {
        $_SESSION[ 'usuarioValido' ] = TRUE;
    }

    if ( $_REQUEST[ 'sair' ] ) {
        unset($_SESSION[ 'usuarioValido' ]);
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8"/>
        <title>Usando GIT para atualizar arquivos no servidor de hospedagem</title>
    </head>
    <body>
        <pre>
            <?php
                if ( $tokenValido ) {
                    $exec = shell_exec("git pull origin master 2>&1");
                    echo $exec;

                    $textoLog = PHP_EOL."Data: ".date("d/m/Y - H:i:s");
                    $textoLog .= PHP_EOL.$exec;

                    $arquivoLog = fopen('log.txt', 'a+');
                    fwrite($arquivoLog, $textoLog);
                    fclose($arquivoLog);
                } elseif ( $_SESSION[ 'usuarioValido' ] ) {
                    ?>
                    <form action="deploy.php" method="post">
                        <input type="hidden" name="token" value="0e770eb2861f3be266de2190b8526d9f">
                        <input type="submit" value="Atualizar">
                    </form>
                    <?php
                    if ( $_SESSION[ 'usuarioValido' ] ) {
                        echo '<p><a href="deploy.php?sair=true">Sair</a></p>';
                    }
                    $texto = file('log.txt');
                    foreach ( $texto as $linha ) {
                        echo $linha;
                    }
                } else {
                    ?>
                    <form action="deploy.php" method="post">
                        <div>
                            <input type="password" placeholder="Senha" name="senhaAcesso">
                        </div>
                        <input type="submit" value="Acessar Sistema">
                    </form>
                    <?php
                }
            ?>
        </pre>
    </body>
</html>
