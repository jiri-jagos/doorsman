<?php

namespace Globalcom\DoormanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Globalcom\DoormanBundle\Entity\Key;
use Globalcom\DoormanBundle\Form\Type\KeyType;
use Globalcom\DoormanBundle\Form\Type\KeyFilterType;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Key controller.
 *
 * @Route("/admin/Key")
 */
class KeyController extends Controller
{
    /**
     * Lists all Key entities.
     *
     * @Route("/", name="admin_key")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new KeyFilterType());
        if (!is_null($response = $this->saveFilter($form, 'key', 'admin_key'))) {
            return $response;
        }
        $qb = $em->getRepository('GlobalcomDoormanBundle:Key')->createQueryBuilder('k');
        $paginator = $this->filter($form, $qb, 'key');
        
        return array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Key entity.
     *
     * @Route("/{id}/zobrazit", name="admin_key_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Key $key)
    {
        $deleteForm = $this->createDeleteForm($key->getId(), 'admin_key_delete');

        return array(
            'key' => $key,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Key entity.
     *
     * @Route("/novy", name="admin_key_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $key = new Key();
        $form = $this->createForm(new KeyType(), $key);

        return array(
            'key' => $key,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Key entity.
     *
     * @Route("/vytvorit", name="admin_key_create")
     * @Method("POST")
     * @Template("GlobalcomDoormanBundle:Key:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $key = new Key();
        $form = $this->createForm(new KeyType(), $key);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($key);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_key_show', array('id' => $key->getId())));
        }

        return array(
            'key' => $key,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Key entity.
     *
     * @Route("/{id}/editovat", name="admin_key_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Key $key)
    {
        $editForm = $this->createForm(new KeyType(), $key, array(
            'action' => $this->generateUrl('admin_key_update', array('id' => $key->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($key->getId(), 'admin_key_delete');

        return array(
            'key' => $key,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Key entity.
     *
     * @Route("/{id}/aktualizovat", name="admin_key_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("GlobalcomDoormanBundle:Key:edit.html.twig")
     */
    public function updateAction(Key $key, Request $request)
    {
        $editForm = $this->createForm(new KeyType(), $key, array(
            'action' => $this->generateUrl('admin_key_update', array('id' => $key->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_key_edit', array('id' => $key->getId())));
        }
        $deleteForm = $this->createDeleteForm($key->getId(), 'admin_key_delete');

        return array(
            'key' => $key,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/seradit/{field}/{type}", name="admin_key_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('key', $field, $type);

        return $this->redirect($this->generateUrl('admin_key'));
    }

    /**
     * @param string $name  session name
     * @param string $field field name
     * @param string $type  sort type ("ASC"/"DESC")
     */
    protected function setOrder($name, $field, $type = 'ASC')
    {
        $this->getRequest()->getSession()->set('sort.' . $name, array('field' => $field, 'type' => $type));
    }

    /**
     * @param  string $name
     * @return array
     */
    protected function getOrder($name)
    {
        $session = $this->getRequest()->getSession();

        return $session->has('sort.' . $name) ? $session->get('sort.' . $name) : null;
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $name
     */
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            $qb->orderBy($alias . '.' . $order['field'], $order['type']);
        }
    }

    /**
     * Save filters
     *
     * @param  FormInterface $form
     * @param  string        $name   route/entity name
     * @param  string        $route  route name, if different from entity name
     * @param  array         $params possible route parameters
     * @return Response
     */
    protected function saveFilter(FormInterface $form, $name, $route = null, array $params = null)
    {
        $request = $this->getRequest();
        $url = $this->generateUrl($route ?: $name, is_null($params) ? array() : $params);
        if ($request->query->has('submit-filter') && $form->handleRequest($request)->isValid()) {
            $request->getSession()->set('filter.' . $name, $request->query->get($form->getName()));

            return $this->redirect($url);
        } elseif ($request->query->has('reset-filter')) {
            $request->getSession()->set('filter.' . $name, null);

            return $this->redirect($url);
        }
    }

    /**
     * Filter form
     *
     * @param  FormInterface                                       $form
     * @param  QueryBuilder                                        $qb
     * @param  string                                              $name
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    protected function filter(FormInterface $form, QueryBuilder $qb, $name)
    {
        if (!is_null($values = $this->getFilter($name))) {
            if ($form->submit($values)->isValid()) {
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $qb);
            }
        }

        // possible sorting
        $this->addQueryBuilderSort($qb, $name);
        return $this->get('knp_paginator')->paginate($qb->getQuery(), $this->getRequest()->query->get('page', 1), 20);
    }

    /**
     * Get filters from session
     *
     * @param  string $name
     * @return array
     */
    protected function getFilter($name)
    {
        return $this->getRequest()->getSession()->get('filter.' . $name);
    }

    /**
     * Deletes a Key entity.
     *
     * @Route("/{id}/delete", name="admin_key_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Key $key, Request $request)
    {
        $form = $this->createDeleteForm($key->getId(), 'admin_key_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($key);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_key'));
    }

    /**
     * Create Delete form
     *
     * @param integer                       $id
     * @param string                        $route
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

}
