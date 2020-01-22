<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
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
    public function __construct()
    {
        $this->module = Pi::service('module')->current();
    }

    /**
     * Get list of attach files
     */
    public function attachList($id, $parent = 'story')
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Set info
        $file  = [];
        $where = ['item_id' => $id, 'status' => 1, 'item_table' => $parent];
        $order = ['time_create ASC', 'id ASC'];
        // Get all attach files
        $select = Pi::model('attach', $this->getModule())->select()->where($where)->order($order);
        $rowset = Pi::model('attach', $this->getModule())->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $file[$row->type][$row->id]                     = $row->toArray();
            $file[$row->type][$row->id]['time_create_view'] = _date($file[$row->type][$row->id]['time_create']);
            if ($file[$row->type][$row->id]['type'] == 'image') {
                // Set image original url
                $file[$row->type][$row->id]['originalUrl'] = Pi::url(
                    sprintf(
                        'upload/%s/original/%s/%s',
                        $config['image_path'],
                        $file[$row->type][$row->id]['path'],
                        $file[$row->type][$row->id]['file']
                    )
                );
                // Set image large url
                $file[$row->type][$row->id]['largeUrl'] = Pi::url(
                    sprintf(
                        'upload/%s/large/%s/%s',
                        $config['image_path'],
                        $file[$row->type][$row->id]['path'],
                        $file[$row->type][$row->id]['file']
                    )
                );
                // Set image medium url
                $file[$row->type][$row->id]['mediumUrl'] = Pi::url(
                    sprintf(
                        'upload/%s/medium/%s/%s',
                        $config['image_path'],
                        $file[$row->type][$row->id]['path'],
                        $file[$row->type][$row->id]['file']
                    )
                );
                // Set image thumb url
                $file[$row->type][$row->id]['thumbUrl'] = Pi::url(
                    sprintf(
                        'upload/%s/thumb/%s/%s',
                        $config['image_path'],
                        $file[$row->type][$row->id]['path'],
                        $file[$row->type][$row->id]['file']
                    )
                );
            } else {
                $file[$row->type][$row->id]['fileUrl'] = Pi::url(
                    sprintf(
                        'upload/%s/%s/%s/%s',
                        $config['file_path'],
                        $file[$row->type][$row->id]['type'],
                        $file[$row->type][$row->id]['path'],
                        $file[$row->type][$row->id]['file']
                    )
                );
            }
        }
        // return
        return $file;
    }

    public function filePreview($type, $path, $file)
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());

        if ($type == 'image') {
            $image   = sprintf('upload/%s/thumb/%s/%s', $config['image_path'], $path, $file);
            $preview = Pi::url($image);
        } else {
            $image   = sprintf('image/%s.png', $type);
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