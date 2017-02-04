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
use Pi\Search\AbstractSearch;

class Search extends AbstractSearch
{
    /**
     * {@inheritDoc}
     */
    protected $table = array(
        'story',
        'topic',
    );

    /**
     * {@inheritDoc}
     */
    protected $searchIn = array(
        'title',
        'text_summary',
        'text_description',
    );

    /**
     * {@inheritDoc}
     */
    protected $meta = array(
        'id' => 'id',
        'title' => 'title',
        'text_summary' => 'content',
        'time_create' => 'time',
        //'uid' => 'uid',
        'slug' => 'slug',
        'image' => 'image',
        'path' => 'path',
    );

    /**
     * {@inheritDoc}
     */
    protected $condition = array(
        'status' => 1,
    );

    /**
     * {@inheritDoc}
     */
    protected $order = array(
        'time_publish DESC',
        'id DESC'
    );

    /**
     * {@inheritDoc}
     */
    protected function buildUrl(array $item, $table = '')
    {
        switch ($table) {
            case 'story':
                $link = Pi::url(Pi::service('url')->assemble('news', array(
                    'module' => $this->getModule(),
                    'controller' => 'story',
                    'slug' => $item['slug'],
                )));
                break;

            case 'topic':
                $link = Pi::url(Pi::service('url')->assemble('news', array(
                    'module' => $this->getModule(),
                    'controller' => 'topic',
                    'slug' => $item['slug'],
                )));
                break;
        }

        return $link;
    }

    /**
     * {@inheritDoc}
     */
    protected function buildImage(array $item, $table = '')
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());

        $image = '';
        if (isset($item['image']) && !empty($item['image'])) {
            $image = Pi::url(
                sprintf('upload/%s/thumb/%s/%s',
                    $config['image_path'],
                    $item['path'],
                    $item['image']
                ));
        }

        return $image;
    }

    /**
     * {@inheritDoc}
     */
    protected function buildCondition(array $terms, array $condition = array(), array $columns = array(), $table = '')
    {
        switch ($table) {
            case 'story':
                $condition['type'] = array(
                    'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download'
                );
                break;

            case 'topic':
                $condition['type'] = array(
                    'general'
                );
                break;
        }

        return Parent::buildCondition($terms, $condition, $columns, $table);
    }
}