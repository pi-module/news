<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
namespace Module\News\Api;

use Pi;
use Pi\Application\Api\AbstractApi;

/*
 * Pi::api('attach', 'news')->filePreview($type, $path, $file);
 * Pi::api('attach', 'news')->fileView($type, $path, $file);
 */

class Attach extends AbstractApi
{
    public function filePreview($type, $path, $file)
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());

        if ($type == 'image') {
            $image = sprintf('upload/%s/thumb/%s/%s', $config['image_path'], $path, $file);
            $preview = Pi::url($image);
        } else {
            $image = sprintf('image/%s.png', $type);
            $preview = Pi::service('asset')->getModuleAsset($image, $this->getModule());
        }
        return $preview;
    }

    public function fileView($type, $path, $file)
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());

        if ($type == 'image') {
            $file = sprintf('upload/%s/thumb/%s/%s', $config['image_path'], $path, $file);
            $view = Pi::url($file);
        } else {
            $file = sprintf('upload/%s/%s/%s/%s', $this->config('file_path'), $type, $path, $file);
            $view = Pi::url($file);
        }
        return $view;
    }
}