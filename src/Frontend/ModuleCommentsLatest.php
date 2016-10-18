<?php

namespace Hofff\Contao\CommentsLatest\Frontend;

use Contao\CommentsModel;
use Contao\Module;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
class ModuleCommentsLatest extends Module {

	/**
	 * @see \Contao\Module::compile()
	 */
	protected function compile() {
		$items = [];
		foreach($this->fetchComments() as $comment) {
			$item = [];
			$item['comment'] = $comment;

			$items[] = $item;
		}

		$items = $this->executeHook($items);

		$this->Template->items = $items;
	}

	/**
	 * @return array<CommentsModel>
	 */
	public function fetchComments() {
		$comments = CommentsModel::findByPublished(1, [
			'limit'	=> $this->numberOfItems,
			'order'	=> 'date DESC',
		]);

		return $comments ? $comments->getModels() : [];
	}

	/**
	 * @param array
	 * @return array
	 */
	protected function executeHook(array $items) {
		$hooks = &$GLOBALS['TL_HOOKS']['hofff_comments_compile'];

		if(!isset($hooks) || !is_array($hooks)) {
			return $items;
		}

		foreach($hooks as $callback) {
			$items = call_user_func(
				[ \System::importStatic($callback[0]), $callback[1] ],
				$items,
				$this
			);
		}

		return $items;
	}

}
