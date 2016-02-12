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
use Pi\Application\Api\AbstractApi;
use Pi\Filter;

/*
 * Pi::api('microblog', 'news')->getMicroblog($parameter, $field);
 * Pi::api('microblog', 'news')->canonizeMicroblog($microblog);
 */

class Microblog extends AbstractApi
{
    public function getMicroblog($parameter, $field = 'id')
    {
        // Get topic
        $microblog = Pi::model('microblog', $this->getModule())->find($parameter, $field);
        $microblog = $this->canonizeMicroblog($microblog);
        return $microblog;
    }

    public function canonizeMicroblog($microblog)
    {
        // Check
        if (empty($microblog)) {
            return '';
        }
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // boject to array
        $microblog = $microblog->toArray();
        // Set text_summary
        $microblog['post'] = Pi::service('markup')->render($microblog['post'], 'html', 'text');
        // Set times
        $microblog['time_create_view'] = _date($microblog['time_create']);
        // Set story url
        $microblog['microblogUrl'] = Pi::url(Pi::service('url')->assemble('news', array(
            'module' => $this->getModule(),
            'controller' => 'microblog',
            'id' => $microblog['id'],
        )));
        // Set user
        $microblog['user'] = Pi::user()->get($microblog['uid'], array(
            'id', 'identity', 'name', 'email'
        ));
        // Set avatar
        $microblog['user']['avatar'] = Pi::service('user')->avatar($microblog['uid'], 'large', array(
            'alt' => $microblog['user']['name'],
            'class' => 'img-circle',
        ));
        // profile url
        $microblog['profileUrl'] = Pi::url(Pi::service('user')->getUrl('profile', array(
            'id' => $microblog['uid'],
        )));
        // Set SEO
        $microblog['seo_title'] = mb_substr(strip_tags($microblog['post']), 0, 60, 'utf-8' ) . " ... ";
        $microblog['seo_description'] = mb_substr(strip_tags($microblog['post']), 0, 160, 'utf-8' ) . " ... ";
        $microblog['seo_keywords'] = mb_substr(strip_tags($microblog['post']), 0, 160, 'utf-8' );
        $filter = new Filter\HeadKeywords;
        $filter->setOptions(array(
            'force_replace_space' => true,
        ));
        $microblog['seo_keywords'] = $filter($microblog['seo_keywords']);
        // return story
        return $microblog;
    }
}