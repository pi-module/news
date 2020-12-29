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

namespace Module\News\Controller\Feed;

use Pi;
use Pi\Mvc\Controller\FeedController;
use Pi\Feed\Model as DataModel;

class IndexController extends FeedController
{
    public function indexAction()
    {
        $feed    = $this->getDataModel(
            [
                'title'        => __('News feed'),
                'description'  => __('Recent News.'),
                'date_created' => time(),
            ]
        );
        $columns = ['id', 'title', 'slug', 'text_summary', 'text_description', 'time_publish'];
        $order   = ['time_publish DESC', 'id DESC'];
        $where   = [
            'status' => 1,
            'type'   => [
                'text', 'article', 'magazine', 'image', 'gallery', 'media', 'download',
            ],
        ];
        $limit   = intval($this->config('feed_num'));
        $select  = $this->getModel('story')->select()->columns($columns)->where($where)->order($order)->limit($limit);
        $rowset  = $this->getModel('story')->selectWith($select);
        foreach ($rowset as $row) {
            $entry                  = [];
            $entry['title']         = $row->title;
            $description            = (empty($row->text_summary)) ? $row->text_description : $row->text_summary;
            $entry['description']   = strtolower(trim($description));
            $entry['date_modified'] = (int)$row->time_publish;
            $entry['link']          = Pi::url(
                Pi::service('url')->assemble(
                    'news',
                    [
                        'module'     => 'news',
                        'controller' => 'story',
                        'slug'       => $row->slug,
                    ]
                )
            );
            $feed->entry            = $entry;
        }
        return $feed;
    }
}
