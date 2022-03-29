<?php

namespace App\Services;

require __DIR__ . '/../../vendor/autoload.php';

use App\Support\MimeTypes;
use Exception;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_Drive;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_Permission;

class GoogleDrive
{
    public $service;

    const BASE_LINK = 'https://drive.google.com/open?id=';

    public $client;

    public $config;

    /**
     * Essa instância do Google Drive deve ter como parâmetro um array com as configurações
     * necessárias para a autenticação.
     *
     * @param array $config
     * @link https://console.cloud.google.com/apis/credentials
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = $this->getClient();
        $this->service = new Google_Service_Drive($this->client);
    }

    /**
     * Cria um cliente com as credenciais especificadas na configuração
     */
    private function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('Teste Google Drive');
        $client->setScopes(Google_Service_Drive::DRIVE_FILE);
        $client->setAuthConfig($this->config);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        $client->setAccessToken($this->config);

        if ($client->isAccessTokenExpired()) {
            if ($client->getRefreshToken()) {
                $this->config['access_token'] = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken())['access_token'];
            }
        }

        return $client;
    }

    /**
     * Cria uma pasta no Google Drive.
     *
     * @param string $name
     * @param string $color
     * @return Google_Service_Drive_DriveFile O id da pasta recém criada
     */
    public function createFolder($name, $color = '#0F0', $shared = false)
    {
        $folder = new Google_Service_Drive_DriveFile();

        $folder->setMimeType(MimeTypes::GOOGLE_DRIVE_FOLDER);
        $folder->setName($name);
        $folder->setFolderColorRgb($color);
        $folder = $this->service->files->create($folder, ['supportsTeamDrives' => false]);
        if (!$shared) {
            $this->makePrivate($folder);
        }
        return $folder;
    }

    /**
     * Busca arquivo pelo nome exato.
     *
     * @param string $name
     * @param integer $pageSize
     * @param string $operator Veja mais operadores em: https://developers.google.com/drive/api/v3/search-files
     * @return array Os arquivos encontrados ou vazio se nada encontrado.
     * @param Google_Service_Drive_DriveFile $folder
     */
    public function findFile(
        $name,
        $pageSize = 15,
        $operator = '=',
        $mimeType = '',
        Google_Service_Drive_DriveFile $folder = null
    ) {
        $q = '';
        if (!empty($name)) {
            $q .= "name $operator '$name' ";
        }
        if (!empty($mimeType)) {
            !empty($q) ? $q .= 'and ' : $q .= '';
            $q .= "mimeType = '$mimeType' ";
        }
        if ($folder != null) {
            $folderId = $folder->getId();
            !empty($q) ? $q .= 'and ' : $q .= '';
            $q .= "'$folderId' in parents ";
        }
        $optParams = array(
            'q' => $q,
            'pageSize' => $pageSize,
            'fields' => 'nextPageToken, files(id, name, mimeType, shared, permissions)'
        );
        echo $q . PHP_EOL;
        return $this->service->files->listFiles($optParams)->getFiles();
    }

    /**
     * Busca pasta pelo nome exato.
     *
     * @param string $name
     * @param integer $pageSize
     * @param string $operator
     * @return array As pastas encontradas ou vazio se nada encontrado.
     */
    public function findFolder($name, $pageSize = 15, $operator = '=')
    {
        return $this->findFile($name, $pageSize, $operator, MimeTypes::GOOGLE_DRIVE_FOLDER);
    }

    /**
     * Busca as pasta que contém no nome a string informada.
     *
     * @param string $name
     * @param integer $pageSize
     * @return array As pastas encontradas ou vazio se nada encontrado.
     */
    public function findFolderNameLike($name, $pageSize = 15)
    {
        return $this->findFolder($name, $pageSize, 'contains');
    }

    /**
     * Lista todas as pastas.
     *
     * @param integer $pageSize
     * @return array As pastas encontradas ou vazio se nenhuma pasta encontrada.
     */
    public function listFolders($pageSize = 15)
    {
        return $this->findFolderNameLike('', $pageSize);
    }

    /**
     * Torna o arquivo ou pasta Restrito ao usuário configurado previamente.
     *
     * @param Google_Service_Drive_DriveFile $target
     * @return void
     */
    public function makePrivate(Google_Service_Drive_DriveFile $target)
    {
        $permissions = $target->getPermissions();
        foreach ($permissions as $permission) {
            if ($permission->getEmailAddress() !== $this->config['user_email']) {
                $this->service->permissions->delete($target->id, $permission->id);
            }
        }
    }

    /**
     * Faz com que pasta e contidos nela arquivos sejam configurados como restritos recursivamente.
     *
     * @param Google_Service_Drive_DriveFile $folder
     * @return void
     */
    public function makeFolderFilesPrivate(Google_Service_Drive_DriveFile $folder)
    {
        $refreshedFolder = $this->findFolder($folder->getName())[0];
        $files = $this->listFilesInFolder($refreshedFolder);
        $this->makePrivate($refreshedFolder);
        foreach ($files as $file) {
            $this->makePrivate($file);
        }
    }

    /**
     * Cria arquivo com opção de incluí-lo em uma pasta.
     *
     * @param Google_Service_Drive_DriveFile $folder
     * @param string $name
     * @param string $content
     * @param string $mimeType
     * @return Google_Service_Drive_DriveFile O arquivo recém criado.
     */
    public function createFile($folder = null, $name = 'Desconhecido', $content = null, $mimeType = null)
    {
        $file = new Google_Service_Drive_DriveFile();
        $file->setMimeType($mimeType);
        $file->setName($name);

        if ($folder != null) {
            $file->setParents(array($folder->getId()));
        }

        $optParams['data'] = '';
        if ($content != null) {
            $optParams = [
                'data' => file_get_contents($content)
            ];
        }

        $file = $this->service->files->create($file, $optParams);
        return $file;
    }

    /**
     * Lista arquivos pertencentes a uma pasta
     *
     * @param Google_Service_Drive_DriveFile $folder
     * @return object
     */
    public function listFilesInFolder(Google_Service_Drive_DriveFile $folder)
    {
        return $this->findFile('', 100, '=', '', $folder);
    }

    /**
     * Fornece o link do recurso: pasta, arquivo...
     *
     * @param Google_Service_Drive_DriveFile $target
     * @return string Link da pasta ou arquivo.
     */
    public function getLink(Google_Service_Drive_DriveFile $target)
    {
        return self::BASE_LINK . $target->getId();
    }

    /**
     * Excluí pasta e arquivos enviando o nome da pasta como parâmetro.
     *
     * @param string $folderName
     * @return void
     */
    public function deleteFolderAndFiles($folderName)
    {
        $folder = $this->findFolder($folderName)[0];
        $files = $this->listFilesInFolder($folder);
        foreach ($files as $file) {
            $this->service->files->delete($file->getId());
        }
        $this->service->files->delete($folder->getId());
    }

    /**
     * Atribuí permissão de acesso a um usuário.
     *
     * @param Google_Service_Drive_DriveFile $folder
     * @param string $userEmail
     * @param string $role
     * @return void
     */
    public function shareFolderWith($folder, $userEmail, $role = 'reader')
    {
        $permission = new Google_Service_Drive_Permission();
        $permission->setType("user");
        $permission->setRole($role);
        $permission->setEmailAddress($userEmail);
        $this->service->permissions->create($folder->getId(), $permission);
    }
}
