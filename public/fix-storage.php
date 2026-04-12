<?php

/**
 * Script para corrigir o link de storage em hospedagem compartilhada
 */

$target = __DIR__ . '/../storage/app/public';
$link = __DIR__ . '/storage';

echo "<h2>Corrigindo Link de Storage</h2>";
echo "Alvo: $target <br>";
echo "Link: $link <br><br>";

if (file_exists($link)) {
    if (is_link($link)) {
        echo "O link já existe e é um atalho. <br>";
    } else {
        echo "Existe uma PASTA física onde deveria ser o atalho. Tentando renomear para 'storage_old'... <br>";
        rename($link, $link . '_old');
    }
}

try {
    if (symlink($target, $link)) {
        echo "<b style='color:green'>Sucesso! O atalho foi criado corretamente.</b><br>";
    } else {
        echo "<b style='color:red'>Falha ao criar o atalho usando symlink().</b><br>";
    }
} catch (Exception $e) {
    echo "<b style='color:red'>Erro: " . $e->getMessage() . "</b><br>";
}

echo "<br><br><a href='/admin/products'>Voltar para o Painel</a>";
