<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Directory Image helper.
 */
class TOESImageHelper {

	public static function processImage($img, $width, $height, $crop = 1) {
		if (!$img) {
			return;
		}
		$img = str_replace(JURI::root(), '', $img);
		$img = str_replace("'", '', $img);
		$img = rawurldecode($img);
		$imagesurl = (file_exists(JPATH_SITE . '/' . $img)) ? self::resize($img, $width, $height, $crop) : '';
		return $imagesurl;
	}

	public static function resize($image, $max_width, $max_height, $crop = 1) {
		$path = JPATH_SITE;
		$imgInfo = getimagesize($path . '/' . $image);
		$width = $imgInfo[0];
		$height = $imgInfo[1];
		
		if($max_width > $width){
			$max_width = $width;
		}
		if ($max_height >= $height) {
			$max_height = $height;
		}

		if (!$max_width && !$max_height) {
			$max_width = $width;
			$max_height = $height;
		} else {
			if (!$max_width)
				$max_width = 1000;
			if (!$max_height)
				$max_height = 1000;
		}
		$x_ratio = $max_width / $width;
		$y_ratio = $max_height / $height;
		$dst = new stdClass();
		$src = new stdClass();
		$src->y = $src->x = 0;
		$dst->y = $dst->x = 0;
		if ($crop) {
			$dst->w = $max_width;
			$dst->h = $max_height;
			if (($width <= $max_width) && ($height <= $max_height)) {
				$src->w = $max_width;
				$src->h = $max_height;
			} else {
				if ($x_ratio < $y_ratio) {
					$src->w = ceil($max_width / $y_ratio);
					$src->h = $height;
				} else {
					$src->w = $width;
					$src->h = ceil($max_height / $x_ratio);
				}
			}
			$src->x = floor(($width - $src->w) / 2);
			$src->y = floor(($height - $src->h) / 2);
		} else {
			$src->w = $width;
			$src->h = $height;
			if (($width <= $max_width) && ($height <= $max_height)) {
				$dst->w = $width;
				$dst->h = $height;
			} else if (($x_ratio * $height) < $max_height) {
				$dst->h = ceil($x_ratio * $height);
				$dst->w = $max_width;
			} else {
				$dst->w = ceil($y_ratio * $width);
				$dst->h = $max_height;
			}
		}

		$ext = strtolower(substr(strrchr($image, '.'), 1)); // get the file extension
		$rzname = strtolower(substr($image, 0, strpos($image, '.'))) . "_{$dst->w}_{$dst->h}.{$ext}"; // get the file extension
		//
		$resized = $path . '/media/com_toes/resized' . $rzname;
		if (file_exists($resized)) {
			$smallImg = getimagesize($resized);
			if (($smallImg[0] <= $dst->w && $smallImg[1] == $dst->h) ||
				($smallImg[1] <= $dst->h && $smallImg[0] == $dst->w)) {
				return JURI::root()."media/com_toes/resized" . $rzname;
			}
		}
		if (!file_exists($path . '/media/com_toes/resized/') && !mkdir($path . '/media/com_toes/resized/', 0755)) {
			return '';
		}
		$folders = explode('/', strtolower($image));
		$tmppath = $path . '/media/com_toes/resized/';
		for ($i = 0; $i < count($folders) - 1; $i++) {
			if (!file_exists($tmppath . $folders[$i]) && !mkdir($tmppath . $folders[$i], 0755))
				return '';
			$tmppath = $tmppath . $folders[$i] . '/';
		}


		switch ($imgInfo[2]) {
			case 1: $im = imagecreatefromgif($path . '/' . $image);
				break;
			case 2: $im = imagecreatefromjpeg($path . '/' . $image);
				break;
			case 3: $im = imagecreatefrompng($path . '/' . $image);
				break;
			default: return '';
				break;
		}

		$newImg = imagecreatetruecolor($dst->w, $dst->h);

		/* Check if this image is PNG or GIF, then set if Transparent */
		if (($imgInfo[2] == 1) OR ( $imgInfo[2] == 3)) {
			imagealphablending($newImg, false);
			imagesavealpha($newImg, true);
			$transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
			imagefilledrectangle($newImg, 0, 0, $dst->w, $dst->h, $transparent);
		}
		imagecopyresampled($newImg, $im, $dst->x, $dst->y, $src->x, $src->y, $dst->w, $dst->h, $src->w, $src->h);

		//Generate the file, and rename it to $newfilename
		switch ($imgInfo[2]) {
			case 1: imagegif($newImg, $resized);
				break;
			case 2: imagejpeg($newImg, $resized, 90);
				break;
			case 3: imagepng($newImg, $resized);
				break;
			default: return '';
				break;
		}

		return JURI::root()."media/com_toes/resized" . $rzname;
	}
	
}