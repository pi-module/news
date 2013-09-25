<?php
/**
 * Index route implementation
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Copyright (c) Pi Engine http://www.xoopsengine.org
 * @license         http://www.xoopsengine.org/license New BSD License
 * @author          Taiwen Jiang <taiwenjiang@tsinghua.org.cn>
 * @author          Hossein Azizabadi <azizabadi@faragostaresh.com>
 * @since           3.0
 * @package         Module\News
 * @subpackage      Route
 * @version         $Id$
 */

namespace Module\News\Route;

use Pi\Mvc\Router\Http\Standard;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\RequestInterface as Request;

/**
 * sample url
 *
 */
class News extends Standard
{
    protected $prefix = '/news';

    /**
     * Default values.
     *
     * @var array
     */
    protected $defaults = array(
        'module' => 'news',
        'controller' => 'index',
        'action' => 'index',
    );

    /**
     * match(): defined by Route interface.
     *
     * @see    Route::match()
     * @param  Request $request
     * @return RouteMatch
     */
    public function match(Request $request, $pathOffset = null)
    {
        $result = $this->canonizePath($request, $pathOffset);
        if (null === $result) {
            return null;
        }
        list($path, $pathLength) = $result;
        if (empty($path)) {
            return null;
        }

        // Get controller
        $controller = explode($this->paramDelimiter, $path, 2);

        // Set controller
        $controllerList = array('archive', 'favorite', 'index', 'json', 'management', 'story', 'tag', 'topic', 'writer');
        if (isset($controller[0]) && in_array($controller[0], $controllerList)) {
            $matches['controller'] = urldecode($controller[0]);
        } elseif (isset($controller[0]) && $controller[0] == 'page') {
            $matches['page'] = intval($controller[1]);
            $matches['controller'] = 'index';
        }

        // Make Match
        if (isset($matches['controller'])) {
            switch ($matches['controller']) {
                case 'story':
                    if (!empty($controller[1])) {
                        $storyPath = explode($this->paramDelimiter, $controller[1], 2);
                        if (in_array('print', $storyPath)) {
                            $matches['action'] = 'print';
                            $matches['alias'] = urldecode($storyPath[1]);
                        } else {
                            $matches['alias'] = urldecode($storyPath[0]);
                        }
                    }
                    break;

                case 'topic':
                    if (!empty($controller[1])) {
                        $topicPath = explode($this->paramDelimiter, $controller[1], 3);
                        if ($topicPath[0] == 'list') {
                            $matches['action'] = 'list';
                        } else {
                            $matches['action'] = urldecode($topicPath[0]);
                        }
                        if (isset($topicPath[1]) && $topicPath[1] == 'page') {
                            $matches['page'] = intval($topicPath[2]);
                        }
                    }
                    break;

                case 'writer':
                    if (!empty($controller[1])) {
                        $writerPath = explode($this->paramDelimiter, $controller[1], 4);
                        if ($writerPath[0] == 'list') {
                            $matches['action'] = 'list';
                        } elseif ($writerPath[0] == 'profile') {
                            $matches['action'] = 'profile';
                            $matches['alias'] = urldecode($writerPath[1]);
                            if (isset($writerPath[2]) && $writerPath[2] == 'page') {
                                $matches['page'] = intval($writerPath[3]);
                            }
                        } else {
                            $matches['alias'] = urldecode($writerPath[0]);
                            if (isset($writerPath[1]) && $writerPath[1] == 'page') {
                                $matches['page'] = intval($writerPath[2]);
                            }
                        }
                    }
                    break;

                case 'archive':
                    if (!empty($controller[1])) {
                        $archivePath = explode($this->paramDelimiter, $controller[1], 4);
                        if (3000 > $archivePath[0] || 1000 < $archivePath[0]) {
                            $matches['year'] = intval($archivePath[0]);
                            if (isset($archivePath[1]) && in_array($archivePath[1], array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12))) {
                                $matches['month'] = intval($archivePath[1]);
                                if (isset($archivePath[2]) && $archivePath[2] == 'page') {
                                    $matches['page'] = intval($archivePath[3]);
                                }
                            }
                        }
                    }
                    break;

                case 'management':
                    if (!empty($controller[1])) {
                        $managementPath = explode($this->paramDelimiter, $controller[1]);
                        if ($managementPath[0] == 'submit') {
                            $matches['action'] = 'submit';
                            if (isset($managementPath[1])) {
                                $matches['alias'] = urldecode($managementPath[1]);
                            }
                        } elseif ($managementPath[0] == 'delete') {
                            $matches['action'] = 'delete';
                            $matches['alias'] = urldecode($managementPath[1]);
                        } elseif ($managementPath[0] == 'remove') {
                            $matches['action'] = 'remove';
                            $matches['alias'] = urldecode($managementPath[1]);
                        } elseif ($managementPath[0] == 'page') {
                            $matches['page'] = intval($managementPath[1]);
                            if (isset($managementPath[2]) && $managementPath[2] == 'topic' && isset($managementPath[4]) && $managementPath[4] == 'status') {
                                $matches['topic'] = intval($managementPath[3]);
                                $matches['status'] = intval($managementPath[5]);
                            } elseif (isset($managementPath[2]) && $managementPath[2] == 'topic') {
                                $matches['topic'] = intval($managementPath[3]);
                            } elseif (isset($managementPath[2]) && $managementPath[2] == 'status') {
                                $matches['status'] = intval($managementPath[3]);
                            }
                        } else {
                            if (isset($managementPath[0]) && $managementPath[0] == 'topic' && isset($managementPath[2]) && $managementPath[2] == 'status') {
                                $matches['topic'] = intval($managementPath[1]);
                                $matches['status'] = intval($managementPath[3]);
                            } elseif (isset($managementPath[0]) && $managementPath[0] == 'topic') {
                                $matches['topic'] = intval($managementPath[1]);
                            } elseif (isset($managementPath[0]) && $managementPath[0] == 'status') {
                                $matches['status'] = intval($managementPath[1]);
                            }
                        }
                    }
                    break;

                case 'tag':
                    if (!empty($controller[1])) {
                        $tagPath = explode($this->paramDelimiter, $controller[1]);
                        $matches['alias'] = urldecode($tagPath[0]);
                        if (isset($tagPath[1]) && $tagPath[1] == 'page') {
                            $matches['page'] = intval($tagPath[2]);
                        }
                    }
                    break;
                    
                case 'favorite':
                    if (!empty($controller[1])) {
                        $favoritePath = explode($this->paramDelimiter, $controller[1], 2);
                        if (isset($favoritePath[0]) && $favoritePath[0] == 'page') {
                            $matches['page'] = intval($favoritePath[1]);
                        }
                    }
                    break;

                case 'json':
                    if (!empty($controller[1])) {
                        $jsonPath = explode($this->paramDelimiter, $controller[1], 3);
                        $matches['topic'] = urldecode($jsonPath[0]);
                        if (isset($jsonPath[1]) && $jsonPath[1] == 'page') {
                            $matches['page'] = intval($jsonPath[2]);
                        }
                    }
                    break;
            }
        }
        return new RouteMatch(array_merge($this->defaults, $matches), $pathLength);
    }

    /**
     * assemble(): Defined by Route interface.
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     */
    public function assemble(array $params = array(), array $options = array())
    {
        $mergedParams = array_merge($this->defaults, $params);
        if (!$mergedParams) {
            return $this->prefix;
        }

        if (!empty($mergedParams['module'])) {
            $url['module'] = $mergedParams['module'];
        }
        if (!empty($mergedParams['controller']) && $mergedParams['controller'] != 'index') {
            $url['controller'] = $mergedParams['controller'];
        }
        if (!empty($mergedParams['action']) && $mergedParams['action'] != 'index') {
            $url['action'] = $mergedParams['action'];
        }
        if (!empty($mergedParams['year'])) {
            $url['year'] = $mergedParams['year'];
        }
        if (!empty($mergedParams['month'])) {
            $url['month'] = $mergedParams['month'];
        }
        if (!empty($mergedParams['alias'])) {
            $url['alias'] = $mergedParams['alias'];
        }
        if (!empty($mergedParams['page'])) {
            $url['page'] = 'page' . $this->paramDelimiter . $mergedParams['page'];
        }
        if (!empty($mergedParams['topic'])) {
            $url['topic'] = 'topic' . $this->paramDelimiter . $mergedParams['topic'];
        }
        if (!empty($mergedParams['status'])) {
            $url['status'] = 'status' . $this->paramDelimiter . $mergedParams['status'];
        }
        $url = implode($this->paramDelimiter, $url);

        if (empty($url)) {
            return $this->prefix;
        }
        return $this->paramDelimiter . $url;
    }
}