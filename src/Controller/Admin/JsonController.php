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

namespace Module\News\Controller\Admin;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Module\Guide\Form\SitemapForm;
use Module\Guide\Form\RegenerateImageForm;

class JsonController extends ActionController
{
    public function indexAction()
    {
        // Get info from url
        $module = $this->params('module');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get config
        $links = [];

        $links['storyAll'] = Pi::url(
            $this->url(
                'news', [
                'module'     => $module,
                'controller' => 'json',
                'action'     => 'storyAll',
                'update'     => strtotime("11-12-10"),
                'password'   => (!empty($config['json_password'])) ? $config['json_password'] : '',
            ]
            )
        );

        $links['storyTopic'] = Pi::url(
            $this->url(
                'news', [
                'module'     => $module,
                'controller' => 'json',
                'action'     => 'storyTopic',
                'id'         => 1,
                'update'     => strtotime("11-12-10"),
                'password'   => (!empty($config['json_password'])) ? $config['json_password'] : '',
            ]
            )
        );

        $links['storySingle'] = Pi::url(
            $this->url(
                'news', [
                'module'     => $module,
                'controller' => 'json',
                'action'     => 'storySingle',
                'id'         => 1,
                'password'   => (!empty($config['json_password'])) ? $config['json_password'] : '',
            ]
            )
        );

        // Set template
        $this->view()->setTemplate('json-index');
        $this->view()->assign('links', $links);
    }
}