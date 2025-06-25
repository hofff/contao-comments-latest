<?php

declare(strict_types=1);

namespace Hofff\Contao\CommentsLatest\EventListener;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\NewsModel;
use Contao\PageModel;
use Hofff\Contao\CommentsLatest\Util\ContaoNewsUtil;
use Throwable;

final class HookSubscriber
{
    /**
     * Active bundles.
     */
    private array $bundles;

    public function __construct(array $bundles)
    {
        $this->bundles = $bundles;
    }

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

                $item['href']  = $page->getFrontendUrl();
                $item['title'] = $page->pageTitle ?: $page->title;
            } catch (Throwable) {
                continue;
            }

            $compiled[] = $item;
        }

        return $compiled;
    }

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
                $article = ArticleModel::findById($content->pid);
                $page    = PageModel::findById($article->pid);

                $item['href']  = $page->getFrontendUrl();
                $item['title'] = $page->pageTitle ?: $page->title;
            } catch (Throwable) {
                continue;
            }

            $compiled[] = $item;
        }

        return $compiled;
    }

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
