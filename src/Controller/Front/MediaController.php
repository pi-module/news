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

namespace Module\News\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;

class MediaController extends ActionController
{
    public function explorerAction()
    {
        // Get info from url
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Check deactivate view
        if ($config['admin_deactivate_view']) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Page not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Get info from url
        $id = $this->params('id');

        // Check id
        if (!isset($id) || empty($id)) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The media not set.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // find attach
        $attach = $this->getModel('attach')->find($id)->toArray();

        // find item
        switch ($attach['item_table']) {
            case 'story':
                $item        = $this->getModel('story')->find($attach['item_id']);
                $item        = Pi::api('story', 'news')->canonizeStoryLight($item);
                $item['url'] = $item['storyUrl'];
                break;

            case 'topic':
                $item        = $this->getModel('topic')->find($attach['item_id']);
                $item        = Pi::api('topic', 'news')->canonizeTopic($item);
                $item['url'] = $item['topicUrl'];
                break;

            case 'author':
                $item        = $this->getModel('author')->find($attach['item_id']);
                $item        = Pi::api('author', 'news')->canonizeAuthor($item);
                $item['url'] = $item['authorUrl'];
                break;
        }

        // update
        $this->getModel('attach')->increment('hits', ['id' => $attach['id']]);

        // Save statistics
        if (Pi::service('module')->isActive('statistics')) {
            Pi::api('log', 'statistics')->save('news', 'explorer', $attach['id']);
        }

        // redirect
        return $this->redirect()->toUrl($item['url']);
    }

    public function downloadAction()
    {
        // Get info from url
        $id     = $this->params('id');
        $module = $this->params('module');

        // Get Module Config
        $config = Pi::service('registry')->config->read($module);

        // Check deactivate view
        if ($config['admin_deactivate_view']) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Page not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Check id
        if (!isset($id) || empty($id)) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The media not set.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // find attach
        $attach = $this->getModel('attach')->find($id)->toArray();

        // update
        $this->getModel('attach')->increment('hits', ['id' => $attach['id']]);

        // Save statistics
        if (Pi::service('module')->isActive('statistics')) {
            Pi::api('log', 'statistics')->save('news', 'download', $attach['id']);
        }

        // Make download link
        if ($attach['type'] == 'link') {
            $url = $attach['url'];
        } elseif ($attach['type'] == 'other') {
            $url = sprintf(
                '%s?%s/%s/%s/%s/%s',
                Pi::url('www/script/download.php'),
                'upload',
                $config['file_path'],
                'file',
                $attach['path'],
                $attach['file']
            );
        } else {
            $url = sprintf(
                '%s?%s/%s/%s/original/%s/%s',
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
        $id     = $this->params('id');
        $module = $this->params('module');

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Check deactivate view
        if ($config['admin_deactivate_view']) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('Page not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Check id
        if (!isset($id) || empty($id)) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The media not set.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // find topic and update hint
        $topic                            = $this->getModel('topic')->find($id);
        $setting                          = json_decode($topic->setting, true);
        $setting['attach_download_count'] = $setting['attach_download_count'] + 1;
        $topic->setting                   = json_encode($setting);
        $topic->save();

        // redirect
        return $this->redirect()->toUrl($setting['attach_link']);
    }
}
