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
		return array_filter($items, function(array &$item) {
			if($item['comment']->source != 'tl_page') {
				return true;
			}

			try {
				$page = PageModel::findById($item['comment']->parent);

				$item['href'] = $page->getFrontendUrl();
				$item['title'] = $page->pageTitle ?: $page->title;
			} catch(\Exception $e) {
				return false;
			}

			return true;
		});
	}

	/**
	 * @param array $items
	 * @return array
	 */
	public static function hookCommentsCompileContent(array $items) {
		return array_filter($items, function(array &$item) {
			if($item['comment']->source != 'tl_content') {
				return true;
			}

			try {
				$content = ContentModel::findById($item['comment']->parent);
				$article = ArticleModel::findById($content->pid);
				$page = PageModel::findById($article->pid);

				$item['href'] = $page->getFrontendUrl();
				$item['title'] = $page->pageTitle ?: $page->title;
			} catch(\Exception $e) {
				return false;
			}

			return true;
		});
	}

	/**
	 * @param array $items
	 * @return array
	 */
	public static function hookCommentsCompileNews(array $items) {
		return array_filter($items, function(array &$item) {
			if($item['comment']->source != 'tl_news') {
				return true;
			}

			try {
				$news = NewsModel::findById($item['comment']->parent);

				$item['href'] = ContaoNewsUtil::getNewsURL($news);
				$item['title'] = $news->headline;
			} catch(\Exception $e) {
				return false;
			}

			return true;
		});
	}

}
