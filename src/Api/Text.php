<?php
/**
 * News module Text class
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
 * @author          Hossein Azizabadi <azizabadi@faragostaresh.com>
 * @since           3.0
 * @package         Module\News
 * @version         $Id$
 */

namespace Module\News\Api;

use Pi;
use Pi\Application\AbstractApi;

/*
 * Pi::service('api')->news(array('Text', 'keywords'), $keywords);
 * Pi::service('api')->news(array('Text', 'description'), $description);
 * Pi::service('api')->news(array('Text', 'slug'), $slug, $id, $model);
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
     * Returns the slug
     *
     * @return boolean
     */
	public function slug($slug, $id, $model)
	{
		$slug = _strip($slug);
        $slug = strtolower(trim($slug));
        $slug = array_filter(explode(' ', $slug));
        $slug = implode('-', $slug);
        $slug = $this->checkSlug($slug, $id, $model);
		return $slug;
	}    
	
	public function checkSlug($slug, $id, $model)
	{
		if (empty($id)) {
			$where = array('slug' => $slug);
		} else {
			$where = array('slug' => $slug, 'id != ?' => $id);
		}	
		$columns = array('id', 'slug');
		$select = $model->select()->columns($columns)->where($where);
		$rowset = $model->selectWith($select);
		if($rowset->count()) {
			$slug = $this->slug($slug . ' ' . rand(1, 9999), $id, $model);
		}
		return $slug;	
	}	                  
}