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
use Pi\Application\Api\AbstractComment;

class Comment extends AbstractComment
{
    /**
     * Get target data
     *
     * @param int|int[] $item Item id(s)
     *
     * @return array
     */
    public function get($item)
    {

        $result = [];
        $items  = (array)$item;

        // Set options
        $story = Pi::api('story', 'news')->getListFromId($items);

        foreach ($items as $id) {
            $result[$id] = [
                'id'    => $story[$id]['id'],
                'title' => $story[$id]['title'],
                'url'   => $story[$id]['storyUrl'],
                'uid'   => $story[$id]['uid'],
                'time'  => $story[$id]['time_publish'],
            ];
        }

        if (is_scalar($item)) {
            $result = $result[$item];
        }

        return $result;
    }

    /**
     * Locate source id via route
     *
     * @param RouteMatch|array $params
     *
     * @return mixed|bool
     */
    public function locate($params = null)
    {
        if (null == $params) {
            $params = Pi::engine()->application()->getRouteMatch();
        }
        if ($params instanceof RouteMatch) {
            $params = $params->getParams();
        }
        if ('news' == $params['module']
            && !empty($params['slug'])
        ) {
            $story = Pi::api('story', 'news')->getStory($params['slug'], 'slug');
            $item  = $story['id'];
        } else {
            $item = false;
        }
        return $item;
    }

    public function canonize($id)
    {
        $data = Pi::api('story', 'news')->getStory($id);
        return [
            'url'   => $data['storyUrl'],
            'title' => $data['title'],
        ];
    }

}
