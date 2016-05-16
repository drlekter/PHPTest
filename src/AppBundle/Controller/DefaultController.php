<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Product;
use AppBundle\Entity\Image;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="_homepage")
     */
    public function indexAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $products = $entityManager->getRepository('AppBundle:Product')->findLatest();

        return $this->render('pages/index.html.twig', array('products' => $products));
    }

    /**
     * @Route("/product/create", name="_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $entity  = new Product();
        $form = $this->createForm('AppBundle\Form\ProductType', $entity)
            ->add('saveAndCreateNew', 'Symfony\Component\Form\Extension\Core\Type\SubmitType');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            if($form->get('uploaded_image')->getData())
            {
                $image = new Image();
                $uploaded_image = $form->get('uploaded_image')->getData();
                $image->setFilename($uploaded_image->getClientOriginalName());
                $image->setPath('original');
                $uploaded_image->move($image->getAbsolutePath(), $uploaded_image->getClientOriginalName());
                $entity->setImage($image);
                $entityManager->persist($image);
            }

            $this->get('cm_tag_assistant')->processTags($entity, "AppBundle\Entity\Tag");

            $entityManager->persist($entity);
            $entityManager->flush();
            $id = $entity->getId();

            $this->addFlash('success', 'Prodotto inserito con successo!');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('_new');
            }

            return $this->redirectToRoute('_edit', array('id' => $id));
        }

        return $this->render('pages/new.html.twig', array(
            'product' => $entity,
            'form' => $form->createView(),
        ));

    }

    /**
     * @Route("/product/list", name="_list")
     */
    public function listAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        if($request->query->get('page')) { $page = $request->query->get('page'); } else { $page = 1; }

        if($request->query->get('search') !== null)
        {
            $products = $entityManager->getRepository('AppBundle:Product')->findByTag($request->query->get('search'));
        }
        else
        {
            $products = $entityManager->getRepository('AppBundle:Product')->findBy(array(), array('created' => 'DESC'));
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($products, $page, 5 );

        return $this->render('pages/show.html.twig', array('products' => $pagination));
    }

    /**
     * @Route("/product/{id}/edit", name="_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Product $product, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $editForm = $this->createForm('AppBundle\Form\ProductType', $product);
        $deleteForm = $this->createDeleteForm($product);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid())
        {
            if($editForm->get('uploaded_image')->getData())
            {
                $image = new Image();
                $uploaded_image = $editForm->get('uploaded_image')->getData();
                $image->setFilename($uploaded_image->getClientOriginalName());
                $image->setPath('original');
                $uploaded_image->move($image->getAbsolutePath(), $uploaded_image->getClientOriginalName());
                $product->setImage($image);
                $entityManager->persist($image);
            }

            $this->get('cm_tag_assistant')->processTags($product, "AppBundle\Entity\Tag");

            $entityManager->persist($product);
            $entityManager->flush();


            $this->addFlash('success', 'Prodotto aggiornato con successo!');
            return $this->redirectToRoute('_edit', array('id' => $product->getId()));
        }

        return $this->render('pages/edit.html.twig', array(
            'product'       => $product,
            'edit_form'     => $editForm->createView(),
            'delete_form'   => $deleteForm->createView(),
        ));
    }

    /**
     *
     * @Route("/product/{id}/delete", name="_delete")
     * @Method("DELETE")
     *
     */
    public function deleteAction(Request $request, Product $product)
    {
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('success', 'Prodotto cancellato con successo!');
        }

        return $this->redirectToRoute('_list');
    }

    /**
     * @param Product $product
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Product $product)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('_delete', array('id' => $product->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

}
