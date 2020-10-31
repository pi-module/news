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
 * Pi::api('spotlight', 'news')->getSpotlight($topic);
 * Pi::api('spotlight', 'news')->isSpotlight($story);
 */

class Spotlight extends AbstractApi
{
    public function getSpotlight($topic = -1)
    {
        // Get config
        $config = Pi::service('registry')->config->read('news');
        if (!$config['show_spotlight']) {
            return false;
        }

        $where1  = ['status' => 1, 'time_publish < ?' => time(), 'time_expire > ?' => time()];
        $where2  = ['topic' => 0];
        $where3  = ['topic' => $topic];
        $order   = ['id DESC', 'time_publish DESC'];
        $limit   = intval($config['spotlight_number']);
        $columns = ['story'];

        $select = Pi::model('spotlight', 'news')->select()
            ->columns($columns)->where($where1)->where($where2)->where($where3, 'OR')->order($order)->limit($limit);
        $rowset = Pi::model('spotlight', 'news')->selectWith($select);
        foreach ($rowset as $row) {
            $row           = $row->toArray();
            $spotlightId[] = $row['story'];
        }
        if (!empty($spotlightId)) {
            $columns = ['id', 'title', 'slug', 'text_summary', 'image', 'path'];
            $order   = ['time_publish DESC', 'id DESC'];
            $where   = ['status' => 1, 'id' => $spotlightId];
            $select  = Pi::model('story', 'news')->select()->columns($columns)->where($where)->order($order);
            $rowset  = Pi::model('story', 'news')->selectWith($select);
            foreach ($rowset as $row) {
                $story[$row->id]                 = $row->toArray();
                $story[$row->id]['text_summary'] = (mb_strlen($story[$row->id]['text_summary'], 'utf-8') > 140)
                    ?
                    mb_substr($story[$row->id]['text_summary'], 0, 140, 'utf-8') . "..."
                    :
                    $story[$row->id]['text_summary'];
                // Set story url
                $story[$row->id]['storyUrl'] = Pi::url(
                    Pi::service('url')->assemble(
                        'news', [
                            'module'     => 'news',
                            'controller' => 'story',
                            'slug'       => $story[$row->id]['slug'],
                        ]
                    )
                );
                // Set image url
                if ($story[$row->id]['image']) {
                    // Set image medium url
                    $story[$row->id]['mediumUrl'] = Pi::url(
                        sprintf(
                            'upload/%s/medium/%s/%s',
                            $config['image_path'],
                            $story[$row->id]['path'],
                            $story[$row->id]['image']
                        )
                    );
                    // Set image thumb url
                    $story[$row->id]['thumbUrl'] = Pi::url(
                        sprintf(
                            'upload/%s/thumb/%s/%s',
                            $config['image_path'],
                            $story[$row->id]['path'],
                            $story[$row->id]['image']
                        )
                    );
                }
            }
            $spotlight['top']  = array_shift($story);
            $spotlight['list'] = $story;
            return $spotlight;
        }
        return false;
    }

    public function isSpotlight($story)
    {
        $where   = ['story' => $story];
        $columns = ['id', 'story'];
        $select  = Pi::model('spotlight', 'news')->select()->where($where)->columns($columns);
        $rowset  = Pi::model('spotlight', 'news')->selectWith($select)->toArray();
        if (count($rowset) > 0) {
            return true;
        } else {
            return false;
        }
    }
}
