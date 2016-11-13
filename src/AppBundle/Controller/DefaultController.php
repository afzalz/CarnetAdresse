<?php

namespace AppBundle\Controller;

use AppBundle\Entity\carnet_adresse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            ]);
    }
      /**
     * @Route("/add", name="ajouter")
     */

      public function addAction(Request $request)
      {


        $carnet = new carnet_adresse();
        $user = $this->getUser();
        $userId = $user->getId();


        $carnet->setIduser($userId);



        
        $form = $this->createFormBuilder($carnet)
        ->add('nom', TextType::class)
        ->add('prenom', TextType::class)
        ->add('adresseEmail', TextType::class)
        ->add('telephone', NumberType::class)
        ->add('site', TextType::class)
        ->add('save', SubmitType::class, array('label' => 'Valider'))
        ->getForm();

        if ($request->isMethod('POST'))
        {

          $form->handleRequest($request);

          if ($form->isValid())
          {

            $em = $this->getDoctrine()->getManager();
            $em->persist($carnet);
            $em->flush();

            
            return $this->redirectToRoute('contact');




        }
    }



    return $this->render('carnetadd.html.twig', array(
        'form' => $form->createView(),
        ));

}


     /**
     * @Route("/contact", name="contact")
     */

     public function showAction()
     {
        if ($this->getUser())
        {
            $carnet = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:carnet_adresse');

            $contact = $carnet->findBy(array('iduser'=>$this->getUser()));

            if(!$carnet)
            {
                throw $this->createNotFoundExcetion('aucun contact trouvé');
            }

            return $this->render('view.html.twig',
             array(
              "count"=>count($contact),
              "contact"=>$contact));
        }
        
        else
        {
            return $this->redirectToRoute('homepage');

        }


    }

    /**
     * @Route("/supprimer/{id}", name="supprimer")
     */

    public function removeAction($id)
    {
        $carnet = $this->getDoctrine()->getManager();
        
        $contact = $carnet->find('AppBundle:carnet_adresse', $id);


        $carnet->remove($contact);
        $carnet->flush();
        return $this->redirectToRoute('contact');


    }

    /**
     * @Route("/modifier/{id}", name="modifier")
     */

    public function updateAction($id, Request $request)
    {
     $carnet = $this->getDoctrine()->getManager();

     $contact = $carnet->find('AppBundle:carnet_adresse', $id);


     if (!$carnet)
     {
        throw $this->createNotFoundException(
            'Le contact n\'existe pas'
            );
    }




    $form = $this->createFormBuilder($contact)
    ->add('nom', TextType::class)
    ->add('prenom', TextType::class)
    ->add('adresseEmail', TextType::class)
    ->add('telephone', NumberType::class)
    ->add('site', TextType::class)
    ->add('save', SubmitType::class, array('label' => 'Valider'))
    ->getForm();

    if ($request->isMethod('POST'))
    {

      $form->handleRequest($request);


      if ($form->isValid())
      {
         $carnet->flush();



         return $this->redirectToRoute('contact');

     }




 }




 return $this->render('carnetupdate.html.twig', array(
    'form' => $form->createView(),
    ));


}



}
