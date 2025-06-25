<?php

declare(strict_types=1);

namespace Hofff\Contao\CommentsLatest\EventListener;

use Contao\ArticleModel;
use Contao\CommentsModel;
use Contao\ContentModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\NewsModel;
use Contao\PageModel;
use Hofff\Contao\CommentsLatest\Util\ContaoNewsUtil;
use Throwable;

final class HookSubscriber
{
    /** @param array<string, string> $bundles Active bundles. */
    public function __construct(private readonly array $bundles)
    {
    }

    /**
     * @param list<array{comment: CommentsModel}> $items
     *
     * @return list<array{comment: CommentsModel, href?: string, title?: string}>
     */
    #[AsHook('hofff_comments_compile')]
    public function hookCommentsCompilePage(array $items): array
    {
        $compiled = [];

        foreach ($items as $item) {
            if ($item['comment']->source !== 'tl_page') {
                $compiled[] = $item;
                continue;
            }

            try {
                $page = PageModel::findById($item['comment']->parent);
                if ($page === null) {
                    continue;
                }

                $item['href']  = $page->getFrontendUrl();
                $item['title'] = $page->pageTitle ?: $page->title;
            } catch (Throwable) {
                continue;
            }

            $compiled[] = $item;
        }

        return $compiled;
    }

    /**
     * @param list<array{comment: CommentsModel}> $items
     *
     * @return list<array{comment: CommentsModel, href?: string, title?: string}>
     */
    #[AsHook('hofff_comments_compile')]
    public function hookCommentsCompileContent(array $items): array
    {
        $compiled = [];

        foreach ($items as $item) {
            if ($item['comment']->source !== 'tl_content') {
                $compiled[] = $item;
                continue;
            }

            try {
                $content = ContentModel::findById($item['comment']->parent);
                if ($content === null) {
                    continue;
                }

                $article = ArticleModel::findById($content->pid);
                if ($article === null) {
                    continue;
                }

                $page = PageModel::findById($article->pid);
                if ($page === null) {
                    continue;
                }

                $item['href']  = $page->getFrontendUrl();
                $item['title'] = $page->pageTitle ?: $page->title;
            } catch (Throwable) {
                continue;
            }

            $compiled[] = $item;
        }

        return $compiled;
    }

    /**
     * @param list<array{comment: CommentsModel}> $items
     *
     * @return list<array{comment: CommentsModel, href?: string, title?: string}>
     */
    #[AsHook('hofff_comments_compile')]
    public function hookCommentsCompileNews(array $items): array
    {
        if (! isset($this->bundles['ContaoNewsBundle'])) {
            return $items;
        }

        $compiled = [];

        foreach ($items as $item) {
            if ($item['comment']->source !== 'tl_news') {
                $compiled[] = $item;
                continue;
            }

            try {
                $news = NewsModel::findById($item['comment']->parent);
                if ($news === null) {
                    continue;
                }

                $item['href']  = ContaoNewsUtil::getNewsURL($news);
                $item['title'] = $news->headline;
            } catch (Throwable) {
                continue;
            }

            $compiled[] = $item;
        }

        return $compiled;
    }
}
