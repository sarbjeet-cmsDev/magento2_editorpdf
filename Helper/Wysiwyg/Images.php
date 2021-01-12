<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Expert\Wysiwyg\Helper\Wysiwyg;

/**
 * Wysiwyg Images Helper.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Images extends \Magento\Cms\Helper\Wysiwyg\Images {
	/**
	 * Prepare Image insertion declaration for Wysiwyg or textarea(as_is mode)
	 *
	 * @param string $filename Filename transferred via Ajax
	 * @param bool $renderAsTag Leave image HTML as is or transform it to controller directive
	 * @return string
	 */
	public function getImageHtmlDeclaration($filename, $renderAsTag = false) {
		$fileurl = $this->getCurrentUrl() . $filename;
		$mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$mediaPath = str_replace($mediaUrl, '', $fileurl);
		$directive = sprintf('{{media url="%s"}}', $mediaPath);
		if ($renderAsTag) {
			$path_info = pathinfo($fileurl);
			if ($path_info['extension'] == 'pdf') {

				$path_parts = pathinfo($filename);

				//$size = number_format(filesize($fileurl) / 1024, 2).'MB';

				return sprintf('<a target="_blank" href="%s">%s</a>', $this->isUsingStaticUrlsAllowed() ? $fileurl : $directive, preg_replace("/-./", " ", $path_parts['filename']));
			} else if ($path_info['extension'] == 'mp4') {

				$path_parts = pathinfo($filename);

				//$size = number_format(filesize($fileurl) / 1024, 2).'MB';

				return sprintf("<a href='%s'>%s</a>", htmlentities($this->isUsingStaticUrlsAllowed() ? $fileurl : $directive), preg_replace("/-./", " ", $path_parts['filename']));
			}
			$html = sprintf("<img src='%s' />", htmlentities($this->isUsingStaticUrlsAllowed() ? $fileurl : $directive));
		} else {
			if ($this->isUsingStaticUrlsAllowed()) {
				$html = $fileurl; // $mediaPath;
			} else {
				$directive = $this->urlEncoder->encode($directive);

				$html = $this->_backendData->getUrl(
					'cms/wysiwyg/directive',
					[
						'___directive' => $directive,
						'_escape_params' => false,
					]
				);
			}
		}
		return $html;
	}
}
