<?php

namespace Shopsys\ShopBundle\Model\Article;

use DateTime;
use Shopsys\FrameworkBundle\Model\Article\Article;
use Shopsys\FrameworkBundle\Model\Article\ArticleData as BaseArticleData;
use Shopsys\FrameworkBundle\Model\Article\ArticleDataFactory as BaseArticleDataFactory;

class ArticleDataFactory extends BaseArticleDataFactory
{
    /**
     * @param \Shopsys\ShopBundle\Model\Article\Article $article
     * @return \Shopsys\ShopBundle\Model\Article\ArticleData
     */
    public function createFromArticle(Article $article): BaseArticleData
    {
        $articleData = new ArticleData();
        $this->fillFromArticle($articleData, $article);

        $articleData->createdAt = $article->getCreatedAt() ?? new DateTime();

        return $articleData;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Article\ArticleData
     */
    public function create(): BaseArticleData
    {
        $articleData = new ArticleData();
        $this->fillNew($articleData);

        return $articleData;
    }
}
