<?php

require __DIR__ . '/../vendor/autoload.php';

if (php_sapi_name() != 'cli') {
    throw new Exception('Este aplicativo deve ser executado na linha de comando');
}

$drive = new App\Services\GoogleDrive();

//$drive->createFolder('Pasta sem compartilhamento');

// $folder = $drive->createFolder('202203171238', '#F00');

// var_dump($folder);

// $file = $drive->createFile($folder);

// var_dump($file);

$folder = $drive->findFolder('202203171238')[0];

// $fileList = glob('files\*');

// foreach ($fileList as $value) {
//     $path = __DIR__ . '\\..\\' . $value;
//     $filename = pathinfo($path)['basename'];

//     echo 'Criando arquivo ' . $filename . '...' . PHP_EOL;

//     $drive->createFile($folder, $filename, $path, mime_content_type($path));

//     echo 'Arquivo ' . $filename . 'criado!' . PHP_EOL;
// }

$drive->makePrivate($drive->findFile('q2_r1-emb.mp4')[0]);


// $file = ;

// var_dump($file);

// foreach ($results as $file) {
//     var_dump($drive->makePrivate($file));
// }
// if (count($permissions) == 0) {
//     print "Nenhum arquivo encontrado.\n";
// } else {
//     print "Arquivos:\n";
//     foreach ($permissions as $permission) {
//         printf("%s (%s) [%s] %s\n", $permission->getDisplayName(), $permission->getId());
//     }
// }
