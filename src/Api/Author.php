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

namespace Module\News\Api;

use Pi;
use Pi\Application\Api\AbstractApi;

/*
 * Pi::api('author', 'news')->getAuthor($parameter, $field);
 * Pi::api('author', 'news')->getAuthorList($ids);
 * Pi::api('author', 'news')->getFormAuthor();
 * Pi::api('author', 'news')->getFormRole();
 * Pi::api('author', 'news')->setFormValues($story);
 * Pi::api('author', 'news')->getStoryList($author);
 * Pi::api('author', 'news')->getStorySingle($story);
 * Pi::api('author', 'news')->setAuthorStory($story, $time_publish, $status, $authors);
 * Pi::api('author', 'news')->canonizeAuthor($author);
 * Pi::api('author', 'news')->sitemap();
 * Pi::api('author', 'news')->regenerateImage();
 */

class Author extends AbstractApi
{
    public function __construct()
    {
        $this->module = Pi::service('module')->current();
    }

    public function getAuthor($parameter, $field = 'id')
    {
        // Get product
        $author = Pi::model('author', 'news')->find($parameter, $field);
        $author = $this->canonizeAuthor($author);
        return $author;
    }

    public function getAuthorList($ids)
    {
        // set info
        $where = ['id' => $ids, 'status' => 1];
        $order = ['title ASC', 'id ASC'];
        $list  = [];
        // Get attach count
        $select = Pi::model('author', 'news')->select()->where($where)->order($order);
        $rowset = Pi::model('author', 'news')->selectWith($select);
        foreach ($rowset as $row) {
            $list[$row->id] = $this->canonizeAuthor($row);
        }
        return $list;
    }

    public function getFormAuthor()
    {
        // set info
        $where   = ['status' => 1];
        $columns = ['id', 'title'];
        $order   = ['title DESC', 'id DESC'];
        $list    = [0 => ''];
        // Get attach count
        $select = Pi::model('author', 'news')->select()->columns($columns)->where($where)->order($order);
        $rowset = Pi::model('author', 'news')->selectWith($select);
        foreach ($rowset as $row) {
            $list[$row->id] = $row->title;
        }
        return $list;
    }

    public function getFormRole()
    {
        // set info
        $where   = ['status' => 1];
        $columns = ['id', 'title'];
        $order   = ['title DESC', 'id DESC'];
        $list    = [];
        // Get attach count
        $select = Pi::model('author_role', 'news')->select()->columns($columns)->where($where)->order($order);
        $rowset = Pi::model('author_role', 'news')->selectWith($select);
        foreach ($rowset as $row) {
            $list[$row->id]         = $row->toArray();
            $list[$row->id]['name'] = sprintf('role_%s', $row->id);
        }
        return $list;
    }

    public function setFormValues($story)
    {
        // set info
        $where = ['story' => $story['id']];
        // Get attach count
        $select = Pi::model('author_story', 'news')->select()->where($where);
        $rowset = Pi::model('author_story', 'news')->selectWith($select);
        foreach ($rowset as $row) {
            $name         = sprintf('role_%s', $row->role);
            $story[$name] = $row->author;
        }
        return $story;
    }

    public function setAuthorStory($story, $time_publish, $status, $authors = [])
    {
        if (!empty($authors)) {
            //Remove
            Pi::model('author_story', 'news')->delete(['story' => $story]);
            // Add
            foreach ($authors as $author) {
                if ($author['author'] > 0) {
                    // Set array
                    $values['story']        = $story;
                    $values['time_publish'] = $time_publish;
                    $values['status']       = $status;
                    $values['author']       = $author['author'];
                    $values['role']         = $author['role'];
                    // Save
                    $row = Pi::model('author_story', 'news')->createRow();
                    $row->assign($values);
                    $row->save();
                }
            }
        }
    }

    public function getStoryList($author, $roles)
    {
        $story = [];
        foreach ($roles as $role) {
            // set info
            $id    = [];
            $where = ['status' => 1, 'author' => $author, 'role' => $role['id']];
            $order = ['time_publish DESC', 'id DESC'];
            // Get author
            $select = Pi::model('author_story', 'news')->select()->where($where)->order($order);
            $rowset = Pi::model('author_story', 'news')->selectWith($select);
            foreach ($rowset as $row) {
                $id[] = $row->story;
            }
            // Check and get
            if (!empty($id)) {
                $story[$role['id']] = Pi::api('story', 'news')->getListFromIdLight($id);
            }
        }
        return $story;
    }

    public function getStorySingle($story)
    {
        // Get roles
        $roles = $this->getFormRole();
        // set info
        $list  = [];
        $where = ['status' => 1, 'story' => $story,];
        $order = ['time_publish DESC', 'id DESC'];
        // Get author
        $select = Pi::model('author_story', 'news')->select()->where($where)->order($order);
        $rowset = Pi::model('author_story', 'news')->selectWith($select);
        foreach ($rowset as $row) {
            $author                      = Pi::model('author', 'news')->find($row->author)->toArray();
            $list[$row->id]              = [];
            $list[$row->id]['role']      = $roles[$row->role]['title'];
            $list[$row->id]['name']      = $author['title'];
            $list[$row->id]['authorUrl'] = Pi::service('url')->assemble(
                'news', [
                    'module'     => 'news',
                    'controller' => 'author',
                    'slug'       => $author['slug'],
                ]
            );;
        }
        return $list;
    }

    public function canonizeAuthor($author)
    {
        // Check
        if (empty($author)) {
            return '';
        }
        // Get config
        $config = Pi::service('registry')->config->read('news');
        // object to array
        $author = $author->toArray();
        // Set description
        $author['text_description'] = Pi::service('markup')->render($author['text_description'], 'html', 'html');
        // Set times
        $author['time_create_view'] = _date($author['time_create']);
        $author['time_update_view'] = _date($author['time_update']);
        // Set story url
        $author['authorUrl'] = Pi::service('url')->assemble(
            'news', [
                'module'     => 'news',
                'controller' => 'author',
                'slug'       => $author['slug'],
            ]
        );
        // Set image url
        if ($author['image']) {
            // Set image original url
            $author['originalUrl'] = Pi::url(
                sprintf(
                    'upload/%s/original/%s/%s',
                    $config['image_path'],
                    $author['path'],
                    $author['image']
                )
            );
            // Set image large url
            $author['largeUrl'] = Pi::url(
                sprintf(
                    'upload/%s/large/%s/%s',
                    $config['image_path'],
                    $author['path'],
                    $author['image']
                )
            );
            // Set image medium url
            $author['mediumUrl'] = Pi::url(
                sprintf(
                    'upload/%s/medium/%s/%s',
                    $config['image_path'],
                    $author['path'],
                    $author['image']
                )
            );
            // Set image thumb url
            $author['thumbUrl'] = Pi::url(
                sprintf(
                    'upload/%s/thumb/%s/%s',
                    $config['image_path'],
                    $author['path'],
                    $author['image']
                )
            );
        } else {
            // Set image original url
            $author['originalUrl'] = Pi::url('static/avatar/local/origin.png');
            // Set image large url
            $author['largeUrl'] = Pi::url('static/avatar/local/xxlarge.png');
            // Set image medium url
            $author['mediumUrl'] = Pi::url('static/avatar/local/xlarge.png');
            // Set image thumb url
            $author['thumbUrl'] = Pi::url('static/avatar/local/large.png');
        }
        // return author
        return $author;
    }

    public function sitemap()
    {
        if (Pi::service('module')->isActive('sitemap')) {
            // Remove old links
            Pi::api('sitemap', 'sitemap')->removeAll('news', 'author');
            // find and import
            $columns = ['id', 'slug', 'status'];
            $select  = Pi::model('author', 'news')->select()->columns($columns);
            $rowset  = Pi::model('author', 'news')->selectWith($select);
            foreach ($rowset as $row) {
                // Make url
                $loc = Pi::url(
                    Pi::service('url')->assemble(
                        'news', [
                            'module'     => 'news',
                            'controller' => 'author',
                            'slug'       => $row->slug,
                        ]
                    )
                );
                // Add to sitemap
                Pi::api('sitemap', 'sitemap')->groupLink($loc, $row->status, 'news', 'author', $row->id);
            }
        }
    }

    public function regenerateImage()
    {
        // Get config
        $config = Pi::service('registry')->config->read('news');
        // Set info
        $columns = ['id', 'image', 'path'];
        $order   = ['id ASC'];
        $select  = Pi::model('author', 'news')->select()->columns($columns)->order($order);
        $rowset  = Pi::model('author', 'news')->selectWith($select);
        foreach ($rowset as $row) {
            if (!empty($row->image) && !empty($row->path)) {
                // Set image original path
                $original = Pi::path(
                    sprintf(
                        'upload/%s/original/%s/%s',
                        $config['image_path'],
                        $row->path,
                        $row->image
                    )
                );
                // Set image large path
                $images['large'] = Pi::path(
                    sprintf(
                        'upload/%s/large/%s/%s',
                        $config['image_path'],
                        $row->path,
                        $row->image
                    )
                );
                // Set image medium path
                $images['medium'] = Pi::path(
                    sprintf(
                        'upload/%s/medium/%s/%s',
                        $config['image_path'],
                        $row->path,
                        $row->image
                    )
                );
                // Set image thumb path
                $images['thumb'] = Pi::path(
                    sprintf(
                        'upload/%s/thumb/%s/%s',
                        $config['image_path'],
                        $row->path,
                        $row->image
                    )
                );
                // Check original exist of not
                if (file_exists($original)) {
                    // Remove old images
                    foreach ($images as $image) {
                        if (file_exists($image)) {
                            Pi::service('file')->remove($image);
                        }
                    }
                    // regenerate
                    Pi::api('image', 'news')->process($row->image, $row->path);
                }
            }
        }
    }
}
