<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Twig\TokenParser\SetTokenParser;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        //create 3 categories
        for($i = 1;$i <=3; $i++){
            $category = new Category();
            $category->setTitle('title'.$i);
            $category->setDescription('description'.$i);

            $manager->persist($category);

        //create 3 -> 6 articles
        for($j = 1; $j <= 6; $j++){
            $article = new Article(1);
            $article->setTitle("Title of the article n°$j")
                    ->setContent("<p>Content of the article n°$j</p>")
                    ->setImage("http://placehold.it/350x150")
                    ->setCreatedAt(new \DateTime())
                    ->setCategory($category);
    
            $manager->persist($article);

            //the comments of the article
            for($k =1; $k <= mt_rand(3, 6); $k++){
                $comment = new Comment();
                $comment->setAuthor("Name of the author")
                        ->setContent("<p>Content of the article n°kj</p>")
                        ->setCreatedAt(new \DateTime())
                        ->setArticle($article);

                $manager->persist($comment);
            }
        }
    }

        $manager->flush();
    }
}
