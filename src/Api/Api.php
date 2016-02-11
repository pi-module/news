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
 * Pi::api('api', 'news')->saveStory();
 */

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
        // Return
        return $story;
    }

    public function setupLink($link)
    {}

    public function getSingleStory()
    {}

    public function getListStory()
    {}
}