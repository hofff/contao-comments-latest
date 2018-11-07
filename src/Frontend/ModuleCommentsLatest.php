<?php

declare(strict_types=1);

namespace Hofff\Contao\CommentsLatest\Frontend;

use Contao\CommentsModel;
use Contao\Module;
use function call_user_func;
use function is_array;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
final class ModuleCommentsLatest extends Module
{

    /**
     * @see \Contao\Module::generate()
     */
    public function generate()
    {
        if (TL_MODE === 'BE') {
            $tpl           = new \BackendTemplate('be_wildcard');
            $tpl->wildcard = '### LATEST COMMENTS ###';
            $tpl->title    = $this->headline;
            $tpl->id       = $this->id;
            $tpl->link     = $this->name;
            $tpl->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $tpl->parse();
        }

        return parent::generate();
    }

    /**
     * @see \Contao\Module::compile()
     */
    protected function compile()
    {
        $items = [];
        foreach ($this->fetchComments() as $comment) {
            $item            = [];
            $item['comment'] = $comment;

            $items[] = $item;
        }

        $items = $this->executeHook($items);

        $this->Template->items = $items;
    }

    /**
     * @return array<CommentsModel>
     */
    public function fetchComments(): array
    {
        $comments = CommentsModel::findByPublished(
            1,
            [
                'limit' => $this->numberOfItems,
                'order' => 'tl_comments.date DESC',
            ]
        );

        return $comments ? $comments->getModels() : [];
    }

    /**
     * @param array
     *
     * @return array
     */
    protected function executeHook(array $items): array
    {
        $hooks = &$GLOBALS['TL_HOOKS']['hofff_comments_compile'];

        if (!isset($hooks) || !is_array($hooks)) {
            return $items;
        }

        foreach ($hooks as $callback) {
            $items = call_user_func(
                [static::importStatic($callback[0]), $callback[1]],
                $items,
                $this
            );
        }

        return $items;
    }

}
