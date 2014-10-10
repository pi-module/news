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

        // get image dimensions
        list($width, $height) = getimagesize($original);
        if ($width > $height) {
            $type = 'landscape';
        } elseif ($height > $width) {
            $type = 'portrait';
        } else {
            $type = 'normal';
        }

        // Get config size
        switch ($part) {
            case 'story':
                $configSize['large']['width'] = $config['image_largew'];
                $configSize['large']['height'] = $config['image_largeh'];
                $configSize['medium']['width'] = $config['image_mediumw'];
                $configSize['medium']['height'] = $config['image_mediumh'];
                $configSize['thumb']['width'] = $config['image_thumbw'];
                $configSize['thumb']['height'] = $config['image_thumbh'];
                break;

            case 'topic':
                $configSize['large']['width'] = $config['image_topic_largew'];
                $configSize['large']['height'] = $config['image_topic_largeh'];
                $configSize['medium']['width'] = $config['image_topic_mediumw'];
                $configSize['medium']['height'] = $config['image_topic_mediumh'];
                $configSize['thumb']['width'] = $config['image_topic_thumbw'];
                $configSize['thumb']['height'] = $config['imag_topic_thumbh'];
                break;

            case 'author':
                $configSize['large']['width'] = $config['image_author_largew'];
                $configSize['large']['height'] = $config['image_author_largeh'];
                $configSize['medium']['width'] = $config['image_author_mediumw'];
                $configSize['medium']['height'] = $config['image_author_mediumh'];
                $configSize['thumb']['width'] = $config['image_author_thumbw'];
                $configSize['thumb']['height'] = $config['image_author_thumbh'];
                break;
        }

        // Set size
        switch ($type) {
            case 'landscape':
            case 'normal':
                if ($width > $configSize['large']['width']) {
                    $sizeLarg = ($configSize['large']['width'] / $width);
                } else {
                    $sizeLarg = 1;
                }

                if ($width > $configSize['medium']['width']) {
                    $sizeMedium = ($configSize['medium']['width'] / $width);
                } else {
                    $sizeMedium = 1;
                }

                if ($width > $configSize['thumb']['width']) {
                    $sizeThumb = ($configSize['thumb']['width'] / $width);
                } else {
                    $sizeThumb = 1;
                }
                break;

            case 'portrait':
                if ($height > $configSize['large']['height']) {
                    $sizeLarg = ($configSize['large']['height'] / $height);
                } else {
                    $sizeLarg = 1;
                }

                if ($height > $configSize['medium']['height']) {
                    $sizeMedium = ($configSize['medium']['height'] / $height);
                } else {
                    $sizeMedium = 1;
                }

                if ($height > $configSize['thumb']['height']) {
                    $sizeThumb = ($configSize['thumb']['height'] / $height);
                } else {
                    $sizeThumb = 1;
                }
                break;
        }

        // Resize to large
        if ($sizeLarg == 1) {
            Pi::service('file')->copy($original, $large);
        } else {
            Pi::service('image')->resize(
                $original, 
                $sizeLarg,
                $large
            );
        }

        // Resize to medium
        if ($sizeMedium == 1) {
            Pi::service('file')->copy($original, $medium);
        } else {
            Pi::service('image')->resize(
                $original, 
                $sizeMedium,
                $medium
            );
        }

        // Resize to thumb
        if ($sizeThumb == 1) {
            Pi::service('file')->copy($original, $thumb);
        } else {
            Pi::service('image')->resize(
                $original, 
                $sizeThumb,
                $thumb
            );
        }

        // Watermark
        if ($config['image_watermark']) {
            // Set watermark image
            $watermarkImage = (empty($config['image_watermark_source'])) ? '' : Pi::path($config['image_watermark_source']);
            $watermarkImage = (file_exists($watermarkImage)) ? $watermarkImage : '';
            
            // Watermark large
            Pi::service('image')->watermark(
                $large,
                '',
                $watermarkImage,
                $config['image_watermark_position']
            );

            // Watermark item
            Pi::service('image')->watermark(
                $item,
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