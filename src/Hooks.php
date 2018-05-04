<?php

namespace Hofff\Contao\CommentsLatest;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\NewsModel;
use Contao\PageModel;
use Hofff\Contao\CommentsLatest\Util\ContaoNewsUtil;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class Hooks {

	/**
	 * @param array $items
	 * @return array
	 */
	public static function hookCommentsCompilePage(array $items) {
		$compiled = [];

		foreach($items as $item) {
			if($item['comment']->source != 'tl_page') {
				$compiled[] = $item;
				continue;
			}

			try {
				$page = PageModel::findById($item['comment']->parent);

				$item['href'] = $page->getFrontendUrl();
				$item['title'] = $page->pageTitle ?: $page->title;
			} catch(\Exception $e) {
				continue;
			}

			$compiled[] = $item;
		}

		return $compiled;
	}

	/**
	 * @param array $items
	 * @return array
	 */
	public static function hookCommentsCompileContent(array $items) {
		$compiled = [];

		foreach($items as $item) {
			if($item['comment']->source != 'tl_content') {
				$compiled[] = $item;
				continue;
			}

			try {
				$content = ContentModel::findById($item['comment']->parent);
				$article = ArticleModel::findById($content->pid);
				$page = PageModel::findById($article->pid);

				$item['href'] = $page->getFrontendUrl();
				$item['title'] = $page->pageTitle ?: $page->title;
			} catch(\Exception $e) {
				continue;
			}

			$compiled[] = $item;
		}

		return $compiled;
	}

	/**
	 * @param array $items
	 * @return array
	 */
	public static function hookCommentsCompileNews(array $items) {
		$compiled = [];

		foreach($items as $item) {
			if($item['comment']->source != 'tl_news') {
				$compiled[] = $item;
				continue;
			}

			try {
				$news = NewsModel::findById($item['comment']->parent);

				$item['href'] = ContaoNewsUtil::getNewsURL($news);
				$item['title'] = $news->headline;
			} catch(\Exception $e) {
				continue;
			}

			$compiled[] = $item;
		}

		return $compiled;
	}

}
