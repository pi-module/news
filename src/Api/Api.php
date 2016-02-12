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
use Pi\Filter;
use Pi\Application\Api\AbstractApi;
use Pi\Paginator\Paginator;
use Pi\File\Transfer\Upload;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Json\Json;

/*
 * Pi::api('api', 'news')->addStory($values);
 * Pi::api('api', 'news')->editStory($values);
 * Pi::api('api', 'news')->setupLink($link);
 * Pi::api('api', 'news')->uploadImage($file, $prefix);
 * Pi::api('api', 'news')->removeImage($id);
 * Pi::api('api', 'news')->getStorySingle($parameter, $field, $type);
 * Pi::api('api', 'news')->getStoryList($where, $order, $offset, $limit, $type, $table);
 * Pi::api('api', 'news')->getStoryPaginator($template, $where, $page, $limit, $table);
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
    public function addStory($values)
    {
        // Check type
        if (!isset($values['type']) || !in_array($values['type'], array(
                'text', 'post', 'article', 'magazine', 'event',
                'image', 'gallery', 'media', 'download', 'feed'
            ))) {
            return false;
        }
        // Get config
        $config = Pi::service('registry')->config->read('news');
        // Set seo_title
        $title = ($values['seo_title']) ? $values['seo_title'] : $values['title'];
        $filter = new Filter\HeadTitle;
        $values['seo_title'] = $filter($title);
        // Set seo_keywords
        $keywords = ($values['seo_keywords']) ? $values['seo_keywords'] : $values['title'];
        $filter = new Filter\HeadKeywords;
        $filter->setOptions(array(
            'force_replace_space' => (bool)$config['force_replace_space'],
        ));
        $values['seo_keywords'] = $filter($keywords);
        // Set seo_description
        $description = ($values['seo_description']) ? $values['seo_description'] : $values['title'];
        $filter = new Filter\HeadDescription;
        $values['seo_description'] = $filter($description);
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
        // Topics
        $values['topic'] = Json::encode($values['topic']);
        // Save story
        $story = Pi::model('story', $this->getModule())->createRow();
        $story->assign($values);
        $story->save();
        $story = Pi::api('story', 'news')->canonizeStoryLight($story);
        // Return
        return $story;
    }

    public function editStory($values)
    {
        // Get config
        $config = Pi::service('registry')->config->read('news');
        // Set seo_title
        $title = ($values['seo_title']) ? $values['seo_title'] : $values['title'];
        $filter = new Filter\HeadTitle;
        $values['seo_title'] = $filter($title);
        // Set seo_keywords
        $keywords = ($values['seo_keywords']) ? $values['seo_keywords'] : $values['title'];
        $filter = new Filter\HeadKeywords;
        $filter->setOptions(array(
            'force_replace_space' => (bool)$config['force_replace_space'],
        ));
        $values['seo_keywords'] = $filter($keywords);
        // Set seo_description
        $description = ($values['seo_description']) ? $values['seo_description'] : $values['title'];
        $filter = new Filter\HeadDescription;
        $values['seo_description'] = $filter($description);
        // Check time_update
        if (!isset($values['time_update']) || empty($values['time_update'])) {
            $values['time_update'] = time();
        }
        // Topics
        $values['topic'] = Json::encode($values['topic']);
        // Save story
        $story = Pi::model('story', $this->getModule())->find($values['id']);
        $story->assign($values);
        $story->save();
        $story = Pi::api('story', 'news')->canonizeStoryLight($story);
        // Return
        return $story;
    }

    public function uploadImage($file = array(), $prefix = '')
    {
        // Set result
        $result = array(
            'path' => '',
            'image' => '',
        );
        // upload image
        if (!empty($file['image']['name'])) {
            $config = Pi::service('registry')->config->read('news');
            // Set upload path
            $result['path'] = sprintf('%s/%s', date('Y'), date('m'));
            $originalPath = Pi::path(sprintf('upload/%s/original/%s', $config['image_path'], $result['path']));
            // Image name
            $imageName = Pi::api('image', 'news')->rename($file['image']['name'], $prefix, $result['path']);
            // Upload
            $uploader = new Upload;
            $uploader->setDestination($originalPath);
            $uploader->setRename($imageName);
            $uploader->setExtension($config['image_extension']);
            $uploader->setSize($config['image_size']);
            if ($uploader->isValid()) {
                $uploader->receive();
                // Get image name
                $result['image'] = $uploader->getUploaded('image');
                // process image
                Pi::api('image', 'news')->process($result['image'], $result['path']);
            }
        }
        return $result;
    }

    public function removeImage($id = 0)
    {
        // Set result
        $result = array(
            'status' => 0,
            'message' => '',
        );
        // Check id
        if (isset($id) && intval($id) > 0) {
            // Get story
            $story = Pi::model('story', $this->getModule())->find($id);
            if ($story) {
                // clear DB
                $story->image = '';
                $story->path = '';
                // Save
                $story->save();
                // Check
                if ($story->path == '' && $story->image == '') {
                    $result['message'] = sprintf(__('Image of %s removed'), $story->title);
                    $result['status'] = 1;
                } else {
                    $result['message'] = __('Image not remove');
                    $result['status'] = 0;
                }
            } else {
                $result['message'] = __('Please select story');
                $result['status'] = 0;
            }
        }
        return $result;
    }

    public function setupLink($link)
    {
        // Remove
        Pi::model('link', $this->getModule())->delete(array(
            'story' => $link['story']
        ));
        // process link module
        foreach ($link['module'] as $module) {
            // Check module
            if (isset($module['controller'])
                && !empty($module['controller'])
                && is_array($module['controller'])
                && isset($module['name'])
                && !empty($module['name'])
            ) {
                // process module controller
                foreach ($module['controller'] as $controller) {
                    // Check module controller
                    if(isset($controller['topic'])
                        && !empty($controller['topic'])
                        && is_array($controller['topic'])
                        && isset($controller['name'])
                        && !empty($controller['name'])) {
                        // process controller topic
                        foreach ($controller['topic'] as $topic) {
                            // Check controller topic
                            if (isset($topic) && intval($topic) > 0) {
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
            }
        }
    }

    public function getStorySingle($parameter, $field, $type = 'full')
    {
        switch ($type) {
            case 'json':
                $story = Pi::api('story', 'news')->getStoryJson($parameter, $field);
                break;

            case 'light':
                $story = Pi::api('story', 'news')->getStoryLight($parameter, $field);
                break;

            default:
            case 'full':
                $story = Pi::api('story', 'news')->getStory($parameter, $field);
                break;
        }
        return $story;
    }

    public function getStoryList($where = array(), $order = array(), $offset = '', $limit = 10, $type = 'full', $table = 'link')
    {
        switch ($table) {
            case 'story':
                // Select from story table
                $select = Pi::model('story', $this->getModule())->select();
                if (!empty($where)) {
                    $select->where($where);
                }
                if (!empty($order)) {
                    $select->order($order);
                }
                if (!empty($offset)) {
                    $select->offset($offset);
                }
                if (!empty($limit)) {
                    $select->limit($limit);
                }
                $rowSet = Pi::model('story', $this->getModule())->selectWith($select);
                break;

            default:
            case 'link':
                // Select from link table
                $select = Pi::model('link', $this->getModule())->select();
                if (!empty($where)) {
                    $select->where($where);
                }
                if (!empty($order)) {
                    $select->order($order);
                }
                if (!empty($offset)) {
                    $select->offset($offset);
                }
                if (!empty($limit)) {
                    $select->limit($limit);
                }
                $columns = array('story' => new Expression('DISTINCT story'));
                $select->columns($columns);
                $rowSetLink = Pi::model('link', $this->getModule())->selectWith($select);
                foreach ($rowSetLink as $id) {
                    $storyId[] = $id['story'];
                }
                // Select from story table
                $whereStory = array('id' => $storyId);
                $select = Pi::model('story', $this->getModule())->select()->where($whereStory)->order($order);
                $rowSet = Pi::model('story', $this->getModule())->selectWith($select);
                break;
        }

        // Make list
        $list = array();
        $topicList = Pi::registry('topicList', 'news')->read();
        foreach ($rowSet as $row) {
            switch ($type) {
                case 'json':
                    $list[$row->id] = Pi::api('story', 'news')->canonizeStoryJson($row);
                    break;

                case 'light':
                    $list[$row->id] = Pi::api('story', 'news')->canonizeStoryLight($row);
                    break;

                default:
                case 'full':
                    $list[$row->id] = Pi::api('story', 'news')->canonizeStory($row, $topicList, array(), false);
                    break;
            }
        }
        return $list;
    }

    public function getStoryPaginator($template, $where = array(), $page = 1, $limit = 10, $table = 'link')
    {
        // Set count
        switch ($table) {
            case 'story':
                $columns = array('count' => new Expression('count(*)'));
                $select = Pi::model('story', $this->getModule())->select()->where($where)->columns($columns);
                $count = Pi::model('story', $this->getModule())->selectWith($select)->current()->count;
                break;

            default:
            case 'link':
                $columns = array('count' => new Expression('count(DISTINCT `story`)'));
                $select = Pi::model('link', $this->getModule())->select()->where($where)->columns($columns);
                $count = Pi::model('link', $this->getModule())->selectWith($select)->current()->count;
                break;
        }
        // Check template
        $template['module'] = (isset($template['module'])) ? $template['module'] : 'news';
        $template['controller'] = (isset($template['controller'])) ? $template['controller'] : 'index';
        $template['action'] = (isset($template['action'])) ? $template['action'] : 'index';
        $template['slug'] = (isset($template['slug'])) ? $template['slug'] : '';
        $template['id'] = (isset($template['id'])) ? $template['id'] : '';
        $template['status'] = (isset($template['status'])) ? $template['status'] : '';
        $template['topic'] = (isset($template['topic'])) ? $template['topic'] : '';
        $template['uid'] = (isset($template['uid'])) ? $template['uid'] : '';
        $template['title'] = (isset($template['title'])) ? $template['title'] : '';
        // Set paginator
        $paginator = Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(array(
            //'router' => $this->getEvent()->getRouter(),
            //'route' => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'params' => array_filter(array(
                'module' => $template['module'],
                'controller' => $template['controller'],
                'action' => $template['action'],
                'slug' => $template['slug'],
                'id' => $template['id'],
                'status' => $template['status'],
                'topic' => $template['topic'],
                'uid' => $template['uid'],
                'title' => $template['title'],
            )),
        ));

        return $paginator;
    }
}