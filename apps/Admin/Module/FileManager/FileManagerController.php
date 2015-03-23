<?php
/**
 * User: Francesco
 * Date: 07/03/15
 * Time: 14:07
 */

namespace App\Admin\Module\FileManager;


use Silex\Application;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FileManagerController {

    public function render(Application $app) {
        return $app['twig']->render('App\Admin\Module\FileManager\home.twig', array());
    }

    public function getDirectoryTree(Application $app) {

        $basePath = $app['admin.FileManager.folderPath'];

        if (substr($basePath, -1) != DIRECTORY_SEPARATOR) {
            $basePath .= DIRECTORY_SEPARATOR;
        }

        $rootSubdirs = $this->findSubdirectories($basePath);
        if ($rootSubdirs === FALSE)
        {
            return $app->json(array(
                'status' => false,
                'exception' => "Can't read directory."
            ), 500);
        }


        return $app->json(array(
            'status' => true,
            'root' => array(
                'name' => basename($basePath),
                'subdirectories' => $rootSubdirs
            )
        ));

    }

    private function findSubdirectories($directoryPath) {

        $dirContent = scandir($directoryPath);
        $data = array();

        if ($dirContent === FALSE) return FALSE;

        foreach ($dirContent as $file) {
            //If file is a directory and is not hidden
            if (is_dir($directoryPath.$file) && substr($file, 0, 1) != ".") {
                //Search if it has subdirectories
                $subdirs = $this->findSubdirectories($directoryPath.$file.DIRECTORY_SEPARATOR);
                //Check function result
                if ($subdirs === FALSE) return FALSE;
                //then adds it to data array
                array_push($data, array(
                    'name' => $file,
                    'subdirectories' => $subdirs
                ));
            }
        }
        return $data;
    }

    public function listContent(Application $app, Request $request) {
        $relativePath = $request->get('folderPath');

        $basePath = $app['admin.FileManager.folderPath'];

        if (strlen($relativePath) == 0)
            return $app->json(array(
                'status' => false,
                'exception' => "Folder path not provided."
            ), 400);

        //FIXME Use DIRECTORY_SEPARATOR instead of /

        if (substr($basePath, -1) == '/')
            $fullPath = $basePath.substr($relativePath, 1);
        else
            $fullPath = $basePath.$relativePath;

        $dirContent = scandir($fullPath);

        if ($dirContent === FALSE)
            return $app->json(array(
                'status' => false,
                'exception' => "Can't open directory."
            ), 500);

        $fileList = array();
        foreach ($dirContent as $fileName) {
            $fileAbsolutePath = $fullPath.DIRECTORY_SEPARATOR.$fileName;

            if (is_file($fileAbsolutePath)) {
                $fileSize = filesize($fileAbsolutePath);
                $lastUpdate = filemtime($fileAbsolutePath);

                if ($fileSize === FALSE || $lastUpdate === FALSE) {
                    return $app->json(array(
                        'status' => false,
                        'exception' => "Can't read file properties."
                    ), 500);
                }

                array_push($fileList, array(
                    'name' => $fileName,
                    'size' => $fileSize,
                    'lastUpdate' => $lastUpdate
                ));
            }

        }

        return $app->json(array(
            'status' => true,
            'fileList' => $fileList
        ));
    }

    public function uploadFile(Application $app, Request $request) {
        /** @var UploadedFile $file */
        $file = $request->files->get('file');

        $folder = $request->get('folderPath');

        $filename = $file->getClientOriginalName();

        $path = $app['admin.FileManager.folderPath'].$folder;

        try {
            $file->move($path, $filename);
        } catch (\Exception $e) {
            return $app->json(array(
                'status' => false,
                'exception' => $e->getMessage()
            ), 500);
        }

        return $app->json(array(
            'status' => true
        ), 200);
    }

    public function deleteFile(Application $app, Request $request) {
        $fileRelativePath = $request->get('filePath');

        $absPath = $app['admin.FileManager.folderPath'].$fileRelativePath;

        try {
            $result = unlink($absPath);

            if (!$result)
                throw new \Exception(error_get_last()['message']);

        } catch (\Exception $e) {
            return $app->json(array(
                'status' => false,
                'exception' => $e->getMessage()
            ), 500);
        }

        return $app->json(array(
            'status' => true
        ), 200);

    }

    public function copyFile(Application $app, Request $request) {
        $sourceRelativePath = $request->get('sourceFilePath');
        $destinationRelativePath = $request->get('destinationFilePath');

        $sourceAbsPath = $app['admin.FileManager.folderPath'].$sourceRelativePath;
        $destinationAbsPath = $app['admin.FileManager.folderPath'].$destinationRelativePath;

        try {
            $result = copy($sourceAbsPath, $destinationAbsPath);

            if (!$result)
                throw new \Exception(error_get_last()['message']);

        } catch (\Exception $e) {
            return $app->json(array(
                'status' => false,
                'exception' => $e->getMessage()
            ), 500);
        }

        return $app->json(array(
            'status' => true
        ), 200);
    }

    public function moveFile(Application $app, Request $request) {
        $sourceRelativePath = $request->get('sourceFilePath');
        $destinationRelativePath = $request->get('destinationFilePath');

        $sourceAbsPath = $app['admin.FileManager.folderPath'].$sourceRelativePath;
        $destinationAbsPath = $app['admin.FileManager.folderPath'].$destinationRelativePath;

        try {
            $result = rename($sourceAbsPath, $destinationAbsPath);

            if (!$result)
                throw new \Exception(error_get_last()['message']);

        } catch (\Exception $e) {
            return $app->json(array(
                'status' => false,
                'exception' => $e->getMessage()
            ), 500);
        }

        return $app->json(array(
            'status' => true
        ), 200);

    }

    public function moveDirectory(Application $app, Request $request) {
        $sourceRelativePath = $request->get('sourcePath');
        $destinationRelativePath = $request->get('destinationPath');

        $sourceAbsPath = $app['admin.FileManager.folderPath'].$sourceRelativePath;
        $destinationAbsPath = $app['admin.FileManager.folderPath'].$destinationRelativePath;

        //Checks if sourcePath exists, if not, directory is new
        if (!file_exists($sourceAbsPath)) {
            try {
                $result = mkdir($destinationAbsPath);

                if (!$result)
                    throw new \Exception(error_get_last()['message']);

                return $app->json(array(
                    'status' => true
                ), 200);

            } catch (\Exception $e) {
                return $app->json(array(
                    'status' => false,
                    'exception' => $e->getMessage()
                ), 500);
            }
        } else {
            //Else move directory from source to destination
            try {
                $result = rename($sourceAbsPath, $destinationAbsPath);

                if (!$result)
                    throw new \Exception(error_get_last()['message']);

                return $app->json(array(
                    'status' => true
                ), 200);

            } catch (\Exception $e) {
                return $app->json(array(
                    'status' => false,
                    'exception' => $e->getMessage()
                ), 500);
            }
        }

    }

    public function downloadFile(Application $app, Request $request) {
        $relativeFilePath = $request->get('filePath');
        $absFilePath = $app['admin.FileManager.folderPath'].$relativeFilePath;

        return $app->sendFile($absFilePath)
            ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, basename($absFilePath));
    }

    public function deleteDirectory(Application $app, Request $request) {

        $directoryRelativePath = $request->get('directoryPath');

        $absPath = $app['admin.FileManager.folderPath'].$directoryRelativePath;

        try {
            $result = $this->removeDirectory($absPath);

            if (!$result) {
                throw new \Exception(error_get_last()['message']);
            }
        } catch (\Exception $e) {
            return $app->json(array(
                'status' => false,
                'exception' => $e->getMessage()
            ), 500);
        }

        return $app->json(array(
            'status' => true
        ), 200);

    }

    private function removeDirectory($dir) {
        if (!file_exists($dir))
            return true;
        if (!is_dir($dir))
            return unlink($dir);

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..')
                continue;
            if (!$this->removeDirectory($dir . DIRECTORY_SEPARATOR . $item))
                return false;
        }

        return rmdir($dir);
    }

} 