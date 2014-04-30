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
namespace Module\News\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;

class MediaController extends ActionController
{
    public function explorerAction()
    {
        // Get info from url
        $id = $this->params('id');
        $module = $this->params('module');
        // Check id
        if (!isset($id) || empty($id)) {
            $url = array('', 'module' => $module, 'controller' => 'index', 'action' => 'index');
            $this->jump($url, __('The media not set.'), 'error');
        }
        // find attach and story
        $attach = $this->getModel('attach')->find($id)->toArray();
        $story = $this->getModel('story')->find($attach['story']);
        $story = Pi::api('story', 'news')->canonizeStoryLight($story);
        // update
        $this->getModel('attach')->increment('hits', array('id' => $attach['id']));
        // redirect
        return $this->redirect()->toUrl($story['storyUrl']);
    }
}