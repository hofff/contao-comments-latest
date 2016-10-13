<?php

namespace Hofff\Contao\CommentsLatest\Util;

use Contao\ModuleNews;
use Contao\NewsArchiveModel;
use Contao\NewsModel;
use Contao\PageModel;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class ContaoNewsUtil extends ModuleNews {

	/**
	 */
	public function __construct() {
	}

	/**
	 * @param NewsModel $news
	 * @return string
	 */
	public static function getNewsURL(NewsModel $news) {
		static $instance;
		$instance || $instance = new self;
		return $instance->generateNewsUrl($news);
	}

	/**
	 * @param array $ids
	 * @return void
	 */
	public static function prefetchNewsModels(array $ids) {
		$archives = [];
		foreach(NewsModel::findMultipleByIds(array_values($ids)) as $news) {
			$archives[] = $news->pid;
		}

		$pages = [];
		foreach(NewsArchiveModel::findMultipleByIds($archives) as $archive) {
			$archive->jumpTo && $pages[] = $archive->jumpTo;
		}

		PageModel::findMultipleByIds($pages);
	}

	/**
	 * @see \Contao\Module::compile()
	 */
	protected function compile() {
	}

}
