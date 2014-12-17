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
use Pi\Application\Api\AbstractBreadcrumbs;

class Breadcrumbs extends AbstractBreadcrumbs
{
    /**
     * {@inheritDoc}
     */
    public function load()
    {
        // Get params
        $params = Pi::service('url')->getRouteMatch()->getParams();
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Check breadcrumbs
        if ($config['view_breadcrumbs']) {
        	// Set module link
        	$moduleData = Pi::registry('module')->read($this->getModule());
        	// Make tree
        	if (!empty($params['controller']) && $params['controller'] != 'index') {
                // Set index
                $result = array(
                    array(
                        'label' => $moduleData['title'],
                        'href'  => Pi::service('url')->assemble('news', array(
                            'module' => $this->getModule(),
                        )),
                    ),
                );

                // Set
        		switch ($params['controller']) {
                    case 'author':

                        break;

                    case 'favourite':

                        break;

                    case 'story':
                        $story = Pi::api('story', 'news')->getStory($params['slug'], 'slug');
                        $result[] = array(
                            'label' => $story['title'],
                        );
                        break;

                    case 'tag':
                        if ($params['slug']) {
                            $result[] = array(
                                'label' => __('Tag list'),
                                'href'  => Pi::service('url')->assemble('news', array(
                                    'controller' => 'tag',
                                    'action'     => 'list',
                                )),
                            );
                            $result[] = array(
                                'label' => $params['slug'],
                            );
                        } else {
                            $result[] = array(
                                'label' => __('Tag list'),
                            );
                        }
                        break;

                    case 'topic':
                        if ($params['slug']) {
                            $topic = Pi::api('topic', 'news')->getTopic($params['slug'], 'slug');
                            $result[] = array(
                                'label' => $topic['title'],
                            );
                        } else {
                            $result[] = array(
                                'label' => __('Topic list'),
                            );
                        }
                        break;
        		}
        	} else {
                $result = array(
                    array(
                        'label' => $moduleData['title'],
                    ),
                );
            }
        	return $result;
        } else {
        	return '';
        }
    }
}