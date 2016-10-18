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
		foreach($items as &$item) {
			if($item['comment']->source != 'tl_page') {
				continue;
			}

			$page = PageModel::findById($item['comment']->parent);

			$item['href'] = $page->getFrontendUrl();
			$item['title'] = $page->pageTitle ?: $page->title;
		}

		return $items;
	}

	/**
	 * @param array $items
	 * @return array
	 */
	public static function hookCommentsCompileContent(array $items) {
		foreach($items as &$item) {
			if($item['comment']->source != 'tl_content') {
				continue;
			}

			$content = ContentModel::findById($item['comment']->parent);
			$article = ArticleModel::findById($content->pid);
			$page = PageModel::findById($article->pid);

			$item['href'] = $page->getFrontendUrl();
			$item['title'] = $page->pageTitle ?: $page->title;
		}

		return $items;
	}

	/**
	 * @param array $items
	 * @return array
	 */
	public static function hookCommentsCompileNews(array $items) {
		foreach($items as &$item) {
			if($item['comment']->source != 'tl_news') {
				continue;
			}

			$news = NewsModel::findById($item['comment']->parent);

			$item['href'] = ContaoNewsUtil::getNewsURL($news);
			$item['title'] = $news->headline;
		}

		return $items;
	}

}
