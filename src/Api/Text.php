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
use Pi\Application\AbstractApi;

/*
 * Pi::api('news', 'text')->keywords($keywords);
 * Pi::api('news', 'text')->description($description);
 * Pi::api('news', 'text')->title($title);
 * Pi::api('news', 'text')->slug($slug);
 */

class Text extends AbstractApi
{         
   /**
     * Invoke as a functor
     *
     * Make meta keywords from phrase
     *
     * @param  string $keywords
     * @param  number
     * @param  number
     * @return string
     */
	public function keywords($keywords, $number = '6') 
	{
		$keywords = _strip($keywords);
		$keywords = strtolower(trim($keywords));
		$keywords = array_unique(array_filter(explode(' ', $keywords)));
		$keywords = array_slice($keywords, 0, $number);
		$keywords = implode(',', $keywords);
		return $keywords;
	}	
	 
    /**
     * Invoke as a functor
     *
     * Make meta description from phrase
     *
     * @param  string $description
     * @return string
     */
    public function description($description) 
    {
        $description = _strip($description); 
        $description = strtolower(trim($description));
        $description = preg_replace('/[\s]+/', ' ', $description);
        return $description;
    }   

    /**
     * Invoke as a functor
     *
     * Make meta title from phrase
     *
     * @param  string $title
     * @return string
     */
    public function title($title) 
    {
        $title = _strip($title); 
        $title = strtolower(trim($title));
        $title = preg_replace('/[\s]+/', ' ', $title);
        return $title;
    }   
	
	/**
     * Returns the slug
     *
     * @return boolean
     */
	public function slug($slug)
	{
		$slug = _strip($slug);
        $slug = strtolower(trim($slug));
        $slug = array_filter(explode(' ', $slug));
        $slug = implode('-', $slug);
		return $slug;
	}              
}