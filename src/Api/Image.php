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

/*
 * Pi::api('image', 'news')->rename($image, $prefix, $path);
 * Pi::api('image', 'news')->process($image, $path, $part);
 */

class Image extends AbstractApi
{  
    public function rename($image = '', $prefix = 'image_', $path = '')
    {
        $config = Pi::service('registry')->config->read($this->getModule(), 'image');
        
        // Check image name
        if (empty($image)) {
            return $prefix . '%random%';
        }
        // Separating image name and extension
        $name = pathinfo($image, PATHINFO_FILENAME);
        $extension = pathinfo($image, PATHINFO_EXTENSION);
        $extension = strtolower($extension);
        // strip name
        $name = _strip($name);
        $name = strtolower(trim($name));
        $name = preg_replace("/[^a-zA-Z0-9 ]+/", "", $name);
        $name = array_filter(explode(' ', $name));
        $name = implode('-', $name) . '.' . $extension;
        // Check text length
        if (mb_strlen($name,'UTF-8') < 8) {
            $name = $prefix . '%random%';
        }
        // Set original path
        $original = Pi::path(
            sprintf('upload/%s/original/%s/%s', $config['image_path'], $path, $name)
        );
        // Check file exist
        if (Pi::service('file')->exists($original)) {
            return $prefix . '%random%';
        }
        // return
        return $name;
    }

	public function process($image, $path, $part = 'story')
	{
        $config = Pi::service('registry')->config->read($this->getModule(), 'image');
        
        // Set original path
        $original = Pi::path(
            sprintf('upload/%s/original/%s/%s', $config['image_path'], $path, $image)
        );
        
        // Set large path
        $large = Pi::path(
            sprintf('upload/%s/large/%s/%s', $config['image_path'], $path, $image)
        );

        // Set medium path
        $medium = Pi::path(
            sprintf('upload/%s/medium/%s/%s', $config['image_path'], $path, $image)
        );

        // Set thumb path
        $thumb = Pi::path(
            sprintf('upload/%s/thumb/%s/%s', $config['image_path'], $path, $image)
        );

        // Set options
        $options = array(
            'quality' => empty($config['image_quality']) ? 100 : $config['image_quality'],
        );

        // Set config size
        switch ($part) {
            case 'story':
                $sizeLarg = array($config['image_largew'], $config['image_largeh'], true);
                $sizeMedium = array($config['image_largew'], $config['image_largeh'], true);
                $sizeThumb = array($config['image_largew'], $config['image_largeh'], true);
                break;

            case 'topic':
                $sizeLarg = array($config['image_topic_largew'], $config['image_topic_largeh'], true);
                $sizeMedium = array($config['image_topic_largew'], $config['image_topic_largeh'], true);
                $sizeThumb = array($config['image_topic_largew'], $config['image_topic_largeh'], true);
                break;

            case 'author':
                $sizeLarg = array($config['image_author_largew'], $config['image_author_largeh'], true);
                $sizeMedium = array($config['image_author_largew'], $config['image_author_largeh'], true);
                $sizeThumb = array($config['image_author_largew'], $config['image_author_largeh'], true);
                break;
        }

        // Resize to large
        Pi::service('image')->resize(
            $original, 
            $sizeLarg,
            $large,
            '',
            $options
        );

        // Resize to medium
        Pi::service('image')->resize(
            $original, 
            $sizeMedium,
            $medium,
            '',
            $options
        );

        // Resize to thumb
        Pi::service('image')->resize(
            $original, 
            $sizeThumb,
            $thumb,
            '',
            $options
        );

        // Watermark
        if ($config['image_watermark']) {
            // Set watermark image
            $watermarkImage = (empty($config['image_watermark_source'])) ? '' : Pi::path($config['image_watermark_source']);
            if (empty($watermarkImage) || !file_exists($watermarkImage)) {
                $logoFile = Pi::service('asset')->logo();
                $watermarkImage = Pi::path($logoFile);
            }
            
            // Watermark large
            Pi::service('image')->watermark(
                $large,
                '',
                $watermarkImage,
                $config['image_watermark_position']
            );

            // Watermark medium
            Pi::service('image')->watermark(
                $medium,
                '',
                $watermarkImage,
                $config['image_watermark_position']
            );

            // Watermark thumb
            Pi::service('image')->watermark(
                $thumb,
                '',
                $watermarkImage,
                $config['image_watermark_position']
            );
        }
	}
}