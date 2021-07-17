<?php

namespace App\Helpers;

class FileHelper
{
    /**
     * @param $file
     * @param null $customName
     * @return string
     */
    public static function getFileName($file, $customName = NULL)
    {
        $fileName = time();
        if ($customName) {
            $fileName .= '-' . $customName . '.' . $file->getClientOriginalExtension();
        } else {
            $fileName .= '.' . $file->getClientOriginalExtension();
        }
        return $fileName;
    }
}
