<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Helper;

use Jitamin\Foundation\Base;

/**
 * File helpers.
 */
class FileHelper extends Base
{
    /**
     * Get file icon.
     *
     * @param string $filename Filename
     *
     * @return string Font-Awesome-Icon-Name
     */
    public function icon($filename)
    {
        switch ($this->getFileExtension($filename)) {
            case 'jpeg':
            case 'jpg':
            case 'png':
            case 'gif':
                return 'fa-file-image-o text-warning';
            case 'xls':
            case 'xlsx':
                return 'fa-file-excel-o text-success';
            case 'doc':
            case 'docx':
                return 'fa-file-word-o text-info';
            case 'ppt':
            case 'pptx':
                return 'fa-file-powerpoint-o text-warning';
            case 'zip':
            case 'rar':
            case 'tar':
            case 'bz2':
            case 'xz':
            case 'gz':
                return 'fa-file-archive-o text-success';
            case 'mp3':
                return 'fa-file-audio-o text-primary';
            case 'avi':
            case 'mov':
                return 'fa-file-video-o text-primary';
            case 'php':
            case 'html':
            case 'css':
                return 'fa-file-code-o text-success';
            case 'pdf':
                return 'fa-file-pdf-o text-danger';
        }

        return 'fa-file-o';
    }

    /**
     * Return the image mimetype based on the file extension.
     *
     * @param string $filename
     *
     * @return string
     */
    public function getImageMimeType($filename)
    {
        switch ($this->getFileExtension($filename)) {
            case 'jpeg':
            case 'jpg':
                return 'image/jpeg';
            case 'png':
                return 'image/png';
            case 'gif':
                return 'image/gif';
            default:
                return 'image/jpeg';
        }
    }

    /**
     * Return the browser view mimetype based on the file extension.
     *
     * @param string $filename
     *
     * @return string
     */
    public function getBrowserViewType($filename)
    {
        switch ($this->getFileExtension($filename)) {
            case 'pdf':
                return 'application/pdf';
            default:
                return;
        }
    }

    /**
     * Get the preview type.
     *
     * @param string $filename
     *
     * @return string
     */
    public function getPreviewType($filename)
    {
        switch ($this->getFileExtension($filename)) {
            case 'md':
            case 'markdown':
                return 'markdown';
            case 'txt':
            case 'log':
                return 'text';
            default:
                return;
        }
    }

    /**
     * Get file extension.
     *
     * @param string $filename
     *
     * @return string
     */
    protected function getFileExtension($filename)
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
}
