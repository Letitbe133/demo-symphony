<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// A utiliser avec php bin/console make:form
// use App\Form\ArticleType;

// a utiliser en cas de modif des types d'input par ex
// use Symfony\Component\Form\Extension\Core\Type\TextType;

// injection de dépendance
// use App\Repository\ArticleRepository;

/**
 * @Route("/")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig', [
            'title' => 'Bienvenue sur mon blog',
            'page_title' => 'Blog | Accueil'
        ]);
    }

    /**
     * @Route("/blog", name="blog")
     */
    // public function index(ArticleRepository $repo)
    public function index()
    {
        // supprimer ligne 20 qui n'est plus utile en cas d'injection de dépendance
        $repo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $repo->findAll();

        if(!$articles)
        {
            throw $this->createNotFoundException('Aucun article n\'a été trouvé');
        }

        return $this->render('blog/index.html.twig', [
            'title' => 'Articles publiés',
            'page_title' => 'Blog | Liste des articles',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/edit/{id}", name="blog_edit")
     */
    public function form(Article $article = null, Request $request, ObjectManager $manager)
    {
        // si on ne reçoit pas d'article on crée une nouvelle instance
        if(!$article)
        {
            $article = new Article();
        }

        // on construit le formulaire à partir de la classe article
        $form = $this->createForm(ArticleType::class, $article);

        dump($form);

        // traitement de la requête
        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            // s'il s'agit d'un nouvel article, on ajoute de DateTime
            if(!$article->getId())
            {
                $article->setCreatedAt(new \DateTime());
            }

            // préparation à l'écriture en db
            $manager->persist($article);
            // écriture
            $manager->flush();

            // redirection vers la page de l'article créé
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }


        // affichage du formulaire dans la vue
        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            // si on reçoit un article en paramètre, on passe editMode à false
            'editMode' => $article->getId() !== null,
            'page_title' => ($article->getId()) ? 'Blog | Modification de l\'article ' . $article->getTitle() : 'Blog | Création d\'un article',
            'variable_test' => 'ceci est un test'
        ]);
    }


    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article, Request $request, ObjectManager $manager)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/show.html.twig', [
            'page_title' => 'Blog | ' . $article->getTitle(),
            'article' => $article,
            'commentForm' => $form->createView()
        ]);

    }

}
