<?php

declare(strict_types=1);

namespace Hofff\Contao\CommentsLatest\Frontend;

use Contao\BackendTemplate;
use Contao\CommentsModel;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\FrontendTemplate;
use Contao\Model\Collection;
use Contao\Module;
use Override;
use Symfony\Component\HttpFoundation\RequestStack;

use function assert;
use function call_user_func;
use function is_array;

/**
 * @property FrontendTemplate $Template
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class ModuleCommentsLatest extends Module
{
    /** @var string */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    protected $strTemplate = 'mod_hofff_comments_latest';

    /** {@inheritDoc} */
    #[Override]
    public function generate(): string
    {
        $requestStack = self::getContainer()->get('request_stack');
        assert($requestStack instanceof RequestStack);
        $request      = $requestStack->getCurrentRequest();
        $scopeMatcher = self::getContainer()->get('contao.routing.scope_matcher');
        assert($scopeMatcher instanceof ScopeMatcher);

        if ($request && $scopeMatcher->isBackendRequest($request)) {
            $tpl           = new BackendTemplate('be_wildcard');
            $tpl->wildcard = '### LATEST COMMENTS ###';
            $tpl->title    = $this->headline;
            $tpl->id       = $this->id;
            $tpl->link     = $this->name;
            $tpl->href     = 'contao?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $tpl->parse();
        }

        return parent::generate();
    }

    /** {@inheritDoc} */
    #[Override]
    protected function compile(): void
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
     *
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress MoreSpecificReturnType
     */
    public function fetchComments(): array
    {
        $comments = CommentsModel::findByPublished(
            1,
            [
                'limit' => $this->numberOfItems,
                'order' => 'tl_comments.date DESC',
            ],
        );

        if ($comments instanceof Collection) {
            return $comments->getModels();
        }

        return [];
    }

    /**
     * @param list<array{comment: CommentsModel}> $items
     *
     * @return list<array{comment: CommentsModel, ...}>
     *
     * @psalm-suppress MixedReturnStatement
     */
    protected function executeHook(array $items): array
    {
        /** @psalm-suppress MixedArrayAccess */
        $hooks = &$GLOBALS['TL_HOOKS']['hofff_comments_compile'];

        if (! isset($hooks) || ! is_array($hooks)) {
            return $items;
        }

        /** @psalm-var array{0: string|object, 1: string} $callback */
        foreach ($hooks as $callback) {
            /** @psalm-var list<array{comment: CommentsModel, ...}> $items */
            $items = call_user_func(
                [self::importStatic($callback[0]), $callback[1]],
                $items,
                $this,
            );
        }

        return $items;
    }
}
