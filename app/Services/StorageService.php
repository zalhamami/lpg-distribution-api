<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class StorageService {
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    private $storage;

    /**
     * @var bool
     */
    private $fromString;

    /**
     * StorageService constructor.
     * @param string $storageType
     * @param bool $fromString
     */
    public function __construct(string $storageType = 'default', bool $fromString = false)
    {
        $this->storage = Storage::disk($storageType);
        $this->fromString = $fromString;
    }

    /**
     * @param string $path
     * @param $file
     * @param string $permission
     * @return mixed
     */
    public function save(string $path, $file, string $permission = 'public')
    {
        if (!$this->fromString) {
            $file = file_get_contents($file);
        }
        $this->storage->put($path, $file, $permission);
        return $this->storage->url($path);
    }

    /**
     * @param string $path
     */
    public function delete(string $path)
    {
        $this->storage->delete($path);
    }

    /**
     * @param string $path
     * @return string
     */
    public function getUrl(string $path)
    {
        return $this->storage->url($path);
    }
}
