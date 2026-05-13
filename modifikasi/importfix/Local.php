<?php
/**lib/Filesystems/Providers/Local.php
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2022-11-01 10:16:14
 * @modify date 2023-08-17 18:53:15
 * @license GPLv3
 * @desc [description]
 */

namespace SLiMS\Filesystems\Providers;

use closure;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToWriteFile;
use SLiMS\Filesystems\{Guard,Utils,Stream};
use utility;

class Local extends Contract
{
    use Guard,Utils,Stream;
    
    private $uploadedFile = '';
    private $uploadStatus = true;
    
    /**
     * Define adapter and filesystem
     *
     * @param string $root
     */
    public function __construct(string $root, string $diskName)
    {
        $this->adapter = new LocalFilesystemAdapter($root, visibility: PortableVisibilityConverter::fromArray(permissionMap: [], defaultForDirectories: 'public'));
        $this->filesystem = new Filesystem($this->adapter);
        $this->diskName = $diskName;
        $this->path = $root;
    }

    /**
     * Upload file process with stream
     *
     * @param string $fileToUpload
     * @param closure $validation
     * @return object
     */
    public function upload(string $fileToUpload, closure $validation)
    {
        $resource = null;

        try {
            $file = $_FILES[$fileToUpload] ?? null;

            if (!is_array($file)) {
                $this->uploadStatus = false;
                $this->error = __('No uploaded file data received.');
                return $this;
            }

            if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                $this->uploadStatus = false;
                $this->error = $this->resolveUploadError((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE));
                return $this;
            }

            if (empty($file['tmp_name'])) {
                $this->uploadStatus = false;
                $this->error = __('Temporary uploaded file is missing.');
                return $this;
            }

            // create new random file name
            $this->uploadedFile = md5(date('this') . utility::createRandomString(64)) . $this->getExt($fileToUpload);

            // file resource
            $resource = fopen($file['tmp_name'], 'r');

            if ($resource === false) {
                $this->uploadStatus = false;
                $this->error = __('Unable to read uploaded file.');
                return $this;
            }

            // Write file 
            $this->filesystem->writeStream($this->uploadedFile, $resource);

            // Make a validation
            $validation($this);

        } catch (\ValueError | FilesystemException | UnableToWriteFile $e) {
            $this->uploadStatus = false;
            $this->error = $e->getMessage();
        } finally {
            if (is_resource($resource)) {
                fclose($resource);
            }
        }
        
        return $this;
    }

    /**
     * Convert PHP upload error code to a readable message.
     */
    private function resolveUploadError(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => __('Uploaded file exceeds the server upload limit.'),
            UPLOAD_ERR_PARTIAL => __('Uploaded file was only partially received.'),
            UPLOAD_ERR_NO_FILE => __('No file was uploaded.'),
            UPLOAD_ERR_NO_TMP_DIR => __('Upload temporary directory is not available.'),
            UPLOAD_ERR_CANT_WRITE => __('Server failed to write the uploaded file.'),
            UPLOAD_ERR_EXTENSION => __('A PHP extension stopped the file upload.'),
            default => __('File upload failed.'),
        };
    }

    /**
     * Rename uploaded file with new name
     *
     * @param string $newName
     * @return object
     */
    public function as(string $newName)
    {
        if ($this->uploadStatus)
        {
            $this->filesystem->move($this->uploadedFile, $newName . $this->getExt($this->uploadedFile));
            $this->uploadedFile = $newName . $this->getExt($this->uploadedFile);
        }
        
        return $this;
    }
}
