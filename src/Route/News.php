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

use Pi\Mvc\Router\Http\Standard;

class News extends Standard
{
    /**
     * Default values.
     * @var array
     */
    protected $defaults = array(
        'module'        => 'news',
        'controller'    => 'index',
        'action'        => 'index'
    );

    protected $controllerList = array(
        'archive', 'index', 'json', 'story', 'tag', 'topic', 'writer'
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
        }

        // Make Match
        if (isset($matches['controller']) && !empty($parts[1])) {
            switch ($matches['controller']) {
                case 'story':
                    $matches['slug'] = $this->decode($parts[1]);
                    break;

                case 'topic':
                    if ($parts[1] == 'list') {
                        $matches['action'] = 'list';
                    } else {
                        $matches['action'] = $this->decode($parts[1]);
                    }
                    break;

                case 'writer':
                    if ($parts[1] == 'list') {
                        $matches['action'] = 'list';
                    } else {
                        $matches['id'] = intval($parts[1]);
                    }
                    break;

                case 'archive':
                    if (2100 > $parts[1] || 1900 < $parts[1]) {
                        $matches['year'] = intval($parts[1]);
                        if (isset($parts[2]) && in_array($parts[2], 
                            array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12))
                        ) {
                            $matches['month'] = intval($parts[2]);
                        }
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
                    $matches['topic'] = $this->decode($parts[1]);
                    break;
            }
        }
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
    ) {
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
        if (!empty($mergedParams['year'])) {
            $url['year'] = $mergedParams['year'];
        }
        if (!empty($mergedParams['month'])) {
            $url['month'] = $mergedParams['month'];
        }
        if (!empty($mergedParams['slug'])) {
            $url['slug'] = $mergedParams['slug'];
        }
        if (!empty($mergedParams['id'])) {
            $url['id'] = $mergedParams['id'];
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

        // Make url
        $url = implode($this->paramDelimiter, $url);

        if (empty($url)) {
            return $this->prefix;
        }
        return $this->paramDelimiter . $url;
    }
}