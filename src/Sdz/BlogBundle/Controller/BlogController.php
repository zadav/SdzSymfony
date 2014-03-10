<?php

namespace Sdz\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sdz\BlogBundle\Entity\Article;
use Sdz\BlogBundle\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends Controller
{
    public function indexAction($page)
    {
        if ( $page < 1 ){
            throw $this->createNotFoundException('Page inexistante (page= '.$page.'');
        }
        
        $articles = $this->getDoctrine()
                         ->getManager()
                         ->getRepository('SdzBlogBundle:Article')
                         ->getArticles(3, $page);
        
        return $this->render(
                'SdzBlogBundle:Blog:index.html.twig',
                array(
                    'articles' => $articles,
                    'page' => $page,
                    'nombrePage' => ceil(count($articles)/3)
                    )
                );
    }
    
    public function voirAction(Article $article)
    {
        // À ce stade, la variable $article contient une instance de la classe Article
        // Avec l'id correspondant à l'id contenu dans la route !

        // On récupère ensuite les articleCompetence pour l'article $article
        // On doit le faire à la main pour l'instant, car la relation est unidirectionnelle
        // C'est-à-dire que $article->getArticleCompetences() n'existe pas !
        
        $liste_articleCompetence = $em->getRepository('SdzBlogBUndle:ArticleCompetence')
                                      ->findByArticle($article->getId());
        return $this->render(
                "SdzBlogBundle:Blog:voir.html.twig",
                array(
                    'article' => $article,
                    'liste_articleCompetence' => $liste_articleCompetence
                    )
                );
    }
    
    public function voirSlugAction($slug, $annee, $format)
    {
        // Ici le contenu de la méthode
        return new Response("On pourrait afficher l'article correspondant au slug '".$slug."', créé en ".$annee." et au format ".$format.".");
    }
    
    public function ajouterAction(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(new ArticleType, $article);
        
        // Reste de la méthode qu'on avait déjà écrit
        if ($request->getMethod() == 'POST') {
            $form->submit($request);

            if ($form->isValid()) {
                $article->getImage()->upload();
                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                $em->flush();
        $this->get('session')->getFlashBag()->add('info', 'Article bien ajouté');
                return $this->redirect($this->generateUrl('sdzblog_accueil'));
            }
        }
        return $this->render('SdzBlogBundle:Blog:ajouter.html.twig',
                array('form' => $form->createView()));
    }


    public function modifierAction(Article $article)
    {
      // On utiliser le ArticleEditType
      $form = $this->createForm(new ArticleEditType(), $article);

      $request = $this->getRequest();

      if ($request->getMethod() == 'POST') {
        $form->bind($request);

        if ($form->isValid()) {
          // On enregistre l'article
          $em = $this->getDoctrine()->getManager();
          $em->persist($article);
          $em->flush();

          // On définit un message flash
          $this->get('session')->getFlashBag()->add('info', 'Article bien modifié');

          return $this->redirect($this->generateUrl('sdzblog_voir', array('id' => $article->getId())));
        }
      }

      return $this->render('SdzBlogBundle:Blog:modifier.html.twig', array(
        'form'    => $form->createView(),
        'article' => $article
      ));
    }
    

    public function supprimerAction(Article $article)
    {
    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'article contre cette faille
    $form = $this->createFormBuilder()->getForm();

    $request = $this->getRequest();
    if ($request->getMethod() == 'POST') {
      $form->bind($request);

      if ($form->isValid()) {
        // On supprime l'article
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        // On définit un message flash
        $this->get('session')->getFlashBag()->add('info', 'Article bien supprimé');

        // Puis on redirige vers l'accueil
        return $this->redirect($this->generateUrl('sdzblog_accueil'));
      }
    }

    // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
    return $this->render('SdzBlogBundle:Blog:supprimer.html.twig', array(
      'article' => $article,
      'form'    => $form->createView()
    ));
    }
    
    public function menuAction($nombre)
    {
        // On fixe en dur une liste ici, bien entendu par la suite on la récupérera depuis la BDD !
        $liste = $this->getDoctrine()
                      ->getManager()
                      ->getRepository('SdzBlogBundle:Article')
                      ->findBy(
                          array(),
                          array('date' => 'desc'),
                          $nombre,
                          0
                        );

        return $this->render('SdzBlogBundle:Blog:menu.html.twig', array(
          'liste_articles' => $liste // C'est ici tout l'intérêt : le contrôleur passe les variables nécessaires au template !
        ));
    }
}
