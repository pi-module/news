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
 * Pi::api('api', 'news')->addStory($values, $link);
 * Pi::api('api', 'news')->editStory($values, $link);
 * Pi::api('api', 'news')->setupLink($link);
 * Pi::api('api', 'news')->getSingleStory($parameter, $type);
 */

/*
 * Sample link array
 * $link = array(
    'story' => 1,
    'time_publish' => time(),
    'time_update' => time(),
    'status' => 1,
    'uid' => 1,
    'type' => 'event',
    'module' => array(
        1 => array(
            'name' => 'event',
            'controller' => array(
                1 => array(
                    'name' => 'topic',
                    'topic' => array(
                        1, 2, 3, 4
                    ),
                ),
            ),
        ),
        2 => array(
            'name' => 'guide',
            'controller' => array(
                1 => array(
                    'name' => 'category',
                    'topic' => array(
                        1, 2, 3, 4
                    ),
                ),
                2 => array(
                    'name' => 'location',
                    'topic' => array(
                        1, 2, 3, 4
                    ),
                ),
                3 => array(
                    'name' => 'item',
                    'topic' => array(
                        1, 2, 3, 4
                    ),
                ),
                4 => array(
                    'name' => 'owner',
                    'topic' => array(
                        1
                    ),
                ),
            ),
        ),
    ),
);*/

class Api extends AbstractApi
{
    public function addStory($values, $link)
    {
        // Check type
        if (!isset($values['type']) || !in_array($values['type'], array(
                'text', 'post', 'article', 'magazine', 'event',
                'image', 'gallery', 'media', 'download', 'feed'
            ))) {
            return false;
        }
        // Check time_create
        if (!isset($values['time_create']) || empty($values['time_create'])) {
            $values['time_create'] = time();
        }
        // Check time_update
        if (!isset($values['time_update']) || empty($values['time_update'])) {
            $values['time_update'] = time();
        }
        // Check time_publish
        if (!isset($values['time_publish']) || empty($values['time_publish'])) {
            $values['time_publish'] = time();
        }
        // Check uid
        if (!isset($values['uid']) || empty($values['uid'])) {
            $values['uid'] = Pi::user()->getId();
        }
        // Save story
        $story = Pi::model('story', $this->getModule())->createRow();
        $story->assign($values);
        $story->save();
        $story = Pi::api('story', 'news')->canonizeStoryLight($story);
        // Setup link
        $this->setupLink($link);
        // Return
        return $story;
    }

    public function editStory($values, $link)
    {
        // Check time_update
        if (!isset($values['time_update']) || empty($values['time_update'])) {
            $values['time_update'] = time();
        }
        // Save story
        $story = Pi::model('story', $this->getModule())->find($values['id']);
        $story->assign($values);
        $story->save();
        $story = Pi::api('story', 'news')->canonizeStoryLight($story);
        // Setup link
        $this->setupLink($link);
        // Return
        return $story;
    }

    public function setupLink($link)
    {
        // Remove
        Pi::model('link', $this->getModule())->delete(array(
            'story' => $link['story']
        ));
        // Set
        foreach ($link['module'] as $module) {
            foreach ($module['controller'] as $controller) {
                foreach ($controller['topic'] as $topic) {
                    // Set link values
                    $values['story'] = intval($link['story']);
                    $values['time_publish'] = intval($link['time_publish']);
                    $values['time_update'] = intval($link['time_update']);
                    $values['status'] = intval($link['status']);
                    $values['uid'] = intval($link['uid']);
                    $values['type'] = $link['type'];
                    // Set topic / controller / module
                    $values['topic'] = intval($topic);
                    $values['controller'] = $controller['name'];
                    $values['module'] = $module['name'];
                    // Save
                    $row = Pi::model('link', $this->getModule())->createRow();
                    $row->assign($values);
                    $row->save();
                }
            }
        }
    }

    public function getSingleStory($parameter, $type)
    {
        return Pi::api('story', 'news')->getStory($parameter, $type);
    }

    public function getListStory()
    {}
}