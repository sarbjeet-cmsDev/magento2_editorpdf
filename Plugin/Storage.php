<?php

namespace Expert\Wysiwyg\Plugin;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;

class Storage {

	public function aroundResizeFile(
		\Magento\Cms\Model\Wysiwyg\Images\Storage $subject,
		\Closure $proceed,
		$source,
		$keepRatio = true
	) {

		$filesystem = ObjectManager::getInstance()->get(\Magento\Framework\Filesystem::class);
		$_directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);

		$path_parts = pathinfo($source);
		if (in_array($path_parts['extension'], ['pdf', 'mp4'])) {
			$icon = $path_parts['extension'];

			$realPath = $_directory->getAbsolutePath("icons/$icon-icon.png");
			if (!$_directory->isFile($realPath)) {

				if (!$_directory->isExist($_directory->getAbsolutePath("icons"))) {
					$_directory->create($_directory->getAbsolutePath("icons"));
				}

				$dir = ObjectManager::getInstance()->get(\Magento\Framework\Module\Dir::class);
				copy($dir->getDir('Expert_Wysiwyg', 'view') . "/adminhtml/web/images/$icon-icon.png", $realPath);
			}
			//$source = $realPath;
			$return = $proceed($realPath);

		} else if (in_array($path_parts['extension'], ['svg', 'webp'])) {
			$targetDir = $subject->getThumbsPath($source);

			$pathTargetDir = $_directory->getRelativePath($targetDir);
			if (!$_directory->isExist($pathTargetDir)) {
				$_directory->create($pathTargetDir);
			}

			copy($source, $targetDir . "/" . $path_parts['basename']);

			return $targetDir . $path_parts['basename'];

		} else {
			$return = $proceed($source, $keepRatio);
		}
		return $return;
	}
}
