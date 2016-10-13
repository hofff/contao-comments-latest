<?php

namespace Hofff\Contao\CommentsLatest\Frontend;

use Contao\CommentsModel;
use Contao\ContentModel;
use Contao\Module;
use Contao\NewsModel;
use Contao\PageModel;
use Hofff\Contao\CommentsLatest\Util\ContaoNewsUtil;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class ModuleCommentsLatest extends Module {

	/**
	 * @var array<integer>
	 */
	private $ids;

	/**
	 * @see \Contao\Module::generate()
	 */
	public function generate() {
		$sql = <<<SQL
SELECT
	id
FROM
	tl_comments
WHERE
	published = 1
	AND source IN ('tl_news', 'tl_page', 'tl_content')
ORDER BY
	tstamp DESC
SQL;
		$this->ids = \Database::getInstance()->prepare($sql)->limit($this->numberOfItems)->execute()->fetchEach('id');

		if(!$this->ids) {
			return '';
		}

		return parent::generate();
	}

	/**
	 * @see \Contao\Module::compile()
	 */
	protected function compile() {
		$comments = CommentsModel::findMultipleByIds($this->ids);

		foreach($comments as $comment) {
			if($comment->source == 'tl_page') {
				$comment->href = PageModel::findById($comment->parent)->getFrontendUrl();
			} elseif($comment->source == 'tl_content') {
				$comment->href = ContentModel::findById($comment->parent)->getRelated('pid')->getRelated('pid')->getFrontendUrl();
			} else {
				$comment->href = ContaoNewsUtil::getNewsURL(NewsModel::findById($comment->parent));
			}
		}

		$this->Template->comments = $comments;
	}

}
