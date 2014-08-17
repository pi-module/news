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
use Zend\Json\Json;

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

    public function downloadAction()
    {
        // Get info from url
        $id = $this->params('id');
        $module = $this->params('module');
        // Get Module Config
        $config = Pi::service('registry')->config->read($module);
        // Check id
        if (!isset($id) || empty($id)) {
            $url = array('', 'module' => $module, 'controller' => 'index', 'action' => 'index');
            $this->jump($url, __('The media not set.'), 'error');
        }
        // find attach and story
        $attach = $this->getModel('attach')->find($id)->toArray();
        // update
        $this->getModel('attach')->increment('hits', array('id' => $attach['id']));
        // Make download link
        if($attach['type'] == 'other') {
            $url = sprintf('%s?%s/%s/%s/%s/%s',
                Pi::url('www/script/download.php'),
                'upload',
                $config['file_path'],
                'file',
                $attach['path'],
                $attach['file']
            );
        } else {   
            $url = sprintf('%s?%s/%s/%s/%s/%s',
                Pi::url('www/script/download.php'),
                'upload',
                $config['file_path'], 
                $attach['type'],
                $attach['path'],
                $attach['file']
            );
        } 
        // redirect
        return $this->redirect()->toUrl($url);
    }

    public function topicAction()
    {
        // Get info from url
        $id = $this->params('id');
        $module = $this->params('module');
        // Check id
        if (!isset($id) || empty($id)) {
            $url = array('', 'module' => $module, 'controller' => 'index', 'action' => 'index');
            $this->jump($url, __('The media not set.'), 'error');
        }
        // find topic anhd update cint
        $topic = $this->getModel('topic')->find($id);
        $setting = Json::decode($topic->setting, true);
        $setting['attach_download_count'] = $setting['attach_download_count'] + 1;
        $topic->setting = Json::encode($setting);
        $topic->save();
        // redirect
        return $this->redirect()->toUrl($setting['attach_link']);
    }
}