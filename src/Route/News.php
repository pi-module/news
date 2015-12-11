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
namespace Module\News\Route;

use Pi;
use Pi\Mvc\Router\Http\Standard;

class News extends Standard
{
    /**
     * Default values.
     * @var array
     */
    protected $defaults = array(
        'module' => 'news',
        'controller' => 'index',
        'action' => 'index'
    );

    protected $controllerList = array(
        'author', 'favourite', 'index', 'json', 'media', 'story', 'tag', 'topic', 'microblog'
    );

    /**
     * {@inheritDoc}
     */
    protected $structureDelimiter = '/';

    /**
     * {@inheritDoc}
     */
    protected function parse($path)
    {
        $matches = array();
        $parts = array_filter(explode($this->structureDelimiter, $path));

        // Set controller
        $matches = array_merge($this->defaults, $matches);
        if (isset($parts[0]) && in_array($parts[0], $this->controllerList)) {
            $matches['controller'] = $this->decode($parts[0]);
        } elseif (isset($parts[0]) && !in_array($parts[0], $this->controllerList)) {
            if (in_array($parts[0], array('index', 'filter'))) {
                $matches['controller'] = 'index';
            } else {
                return '';
            }
        }

        // Make Match
        if (isset($matches['controller'])) {
            switch ($matches['controller']) {
                case 'story':
                    $matches['slug'] = $this->decode($parts[1]);
                    break;

                case 'topic':
                    if (isset($parts[1]) && !empty($parts[1])) {
                        $slug = $this->decode($parts[1]);
                        $topicList = Pi::registry('topic', 'news')->read();
                        foreach ($topicList as $topic) {
                            if ($topic['slug'] == $slug) {
                                $action = $topic['name'];
                            }
                        }
                        $matches['action'] = $action;
                        $matches['slug'] = $slug;
                    } else {
                        $matches['action'] = 'list';
                    }
                    break;

                case 'author':
                    if (isset($parts[1]) && !empty($parts[1])) {
                        $matches['action'] = 'index';
                        $matches['slug'] = $this->decode($parts[1]);
                    } else {
                        $matches['action'] = 'list';
                    }
                    break;

                case 'tag':
                    if ($parts[1] == 'term') {
                        $matches['action'] = 'term';
                        $matches['slug'] = urldecode($parts[2]);
                    } elseif ($parts[1] == 'list') {
                        $matches['action'] = 'list';
                    }
                    break;

                case 'json':
                    $matches['action'] = $this->decode($parts[1]);

                    if ($parts[1] == 'filterSearch') {
                        $keyword = _get('keyword');
                        if (isset($keyword) && !empty($keyword)) {
                            $matches['keyword'] = $keyword;
                        }
                    }

                    if (isset($parts[2]) && $parts[2] == 'id') {
                        $matches['id'] = intval($parts[3]);
                    }

                    if (isset($parts[2]) && $parts[2] == 'update') {
                        $matches['update'] = intval($parts[3]);
                    } elseif (isset($parts[4]) && $parts[4] == 'update') {
                        $matches['update'] = intval($parts[5]);
                    }

                    if (isset($parts[4]) && $parts[4] == 'password') {
                        $matches['password'] = $this->decode($parts[5]);
                    } elseif (isset($parts[6]) && $parts[6] == 'password') {
                        $matches['password'] = $this->decode($parts[7]);
                    }

                    break;

                case 'media':
                    $matches['action'] = $this->decode($parts[1]);
                    $matches['id'] = intval($parts[2]);
                    break;

                case 'index':
                    if (isset($parts[0]) && $parts[0] == 'filter') {
                        $matches['action'] = 'filter';
                    } else {
                        $matches['action'] = 'index';
                        if (isset($_GET) && !empty($_GET)) {
                            foreach ($_GET as $key => $value) {
                                if ($key != 'page') {
                                    $matches['q'][$key] = $value;
                                }
                            }
                        }
                    }
                    break;

                case 'microblog':
                    if (isset($parts[1]) && !empty($parts[1])) {
                        $matches['action'] = 'index';
                        $matches['id'] = intval($parts[1]);
                    } else {
                        $matches['action'] = 'list';
                        if (isset($parts[1]) && $parts[1] == 'uid') {
                            $matches['uid'] = intval($parts[2]);
                        } elseif (isset($parts[1]) && $parts[1] == 'topic') {
                            $matches['topic'] = intval($parts[2]);
                        }
                    }
                    break;
            }
        }

        echo '<pre>';
        print_r($matches);
        print_r($parts);
        echo '</pre>';

        return $matches;
    }

    /**
     * assemble(): Defined by Route interface.
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return string
     */
    public function assemble(
        array $params = array(),
        array $options = array()
    )
    {
        $mergedParams = array_merge($this->defaults, $params);
        if (!$mergedParams) {
            return $this->prefix;
        }

        // Set module
        if (!empty($mergedParams['module'])) {
            $url['module'] = $mergedParams['module'];
        }

        if (!empty($mergedParams['controller']) && $mergedParams['controller'] != 'index') {
            $url['controller'] = $mergedParams['controller'];
        }

        if (!empty($mergedParams['action']) && $mergedParams['action'] != 'index') {
            $url['action'] = $mergedParams['action'];
        }

        if (!empty($mergedParams['slug'])) {
            $url['slug'] = $mergedParams['slug'];
        }

        // Set slug
        if (!empty($mergedParams['q'])) {
            $url['q'] = $mergedParams['q'];
        }

        if (!empty($mergedParams['id']) && $mergedParams['controller'] == 'json') {
            $url['id'] = 'id' . $this->paramDelimiter . $mergedParams['id'];
        } elseif (!empty($mergedParams['id'])) {
            $url['id'] = $mergedParams['id'];
        }

        // Set update
        if (!empty($mergedParams['update'])) {
            $url['update'] = 'update' . $this->paramDelimiter . $mergedParams['update'];
        }

        if (!empty($mergedParams['start'])) {
            $url['start'] = 'start' . $this->paramDelimiter . $mergedParams['start'];
        }

        if (!empty($mergedParams['limit'])) {
            $url['limit'] = 'limit' . $this->paramDelimiter . $mergedParams['limit'];
        }

        if (!empty($mergedParams['uid']) && $mergedParams['controller'] == 'microblog') {
            $url['uid'] = 'uid' . $this->paramDelimiter . $mergedParams['uid'];
        }

        if (!empty($mergedParams['topic']) && $mergedParams['controller'] == 'microblog') {
            $url['topic'] = 'topic' . $this->paramDelimiter . $mergedParams['topic'];
        }

        // Set password
        if (!empty($mergedParams['password'])) {
            $url['password'] = 'password' . $this->paramDelimiter . $mergedParams['password'];
        }

        // Make url
        $url = implode($this->paramDelimiter, $url);

        if (empty($url)) {
            return $this->prefix;
        }
        return $this->paramDelimiter . $url;
    }
}