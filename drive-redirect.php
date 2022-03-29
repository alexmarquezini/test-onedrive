<?php

if ($_GET['code']) {
    echo "<h1>{$_GET['code']}</h1>";
} else {
    echo '<h1>Nenhum código recebido!</h1>';
}
