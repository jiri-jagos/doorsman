<?php

namespace Globalcom\DoormanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Globalcom\DoormanBundle\Entity\Entrance;
use Globalcom\DoormanBundle\Form\Type\EntranceType;
use Globalcom\DoormanBundle\Form\Type\EntranceFilterType;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Entrance controller.
 *
 * @Route("/admin/Entrance")
 */
class EntranceController extends Controller
{
    /**
     * Lists all Entrance entities.
     *
     * @Route("/", name="admin_Entrance")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new EntranceFilterType());
        if (!is_null($response = $this->saveFilter($form, 'entrance', 'admin_Entrance'))) {
            return $response;
        }
        $qb = $em->getRepository('GlobalcomDoormanBundle:Entrance')->createQueryBuilder('e');
        $paginator = $this->filter($form, $qb, 'entrance');
        
        return array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Entrance entity.
     *
     * @Route("/{id}/show", name="admin_Entrance_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Entrance $entrance)
    {
        $deleteForm = $this->createDeleteForm($entrance->getId(), 'admin_Entrance_delete');

        return array(
            'entrance' => $entrance,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Entrance entity.
     *
     * @Route("/new", name="admin_Entrance_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entrance = new Entrance();
        $form = $this->createForm(new EntranceType(), $entrance);

        return array(
            'entrance' => $entrance,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Entrance entity.
     *
     * @Route("/create", name="admin_Entrance_create")
     * @Method("POST")
     * @Template("GlobalcomDoormanBundle:Entrance:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entrance = new Entrance();
        $form = $this->createForm(new EntranceType(), $entrance);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entrance);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_Entrance_show', array('id' => $entrance->getId())));
        }

        return array(
            'entrance' => $entrance,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Entrance entity.
     *
     * @Route("/{id}/edit", name="admin_Entrance_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Entrance $entrance)
    {
        $editForm = $this->createForm(new EntranceType(), $entrance, array(
            'action' => $this->generateUrl('admin_Entrance_update', array('id' => $entrance->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($entrance->getId(), 'admin_Entrance_delete');

        return array(
            'entrance' => $entrance,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Entrance entity.
     *
     * @Route("/{id}/update", name="admin_Entrance_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("GlobalcomDoormanBundle:Entrance:edit.html.twig")
     */
    public function updateAction(Entrance $entrance, Request $request)
    {
        $editForm = $this->createForm(new EntranceType(), $entrance, array(
            'action' => $this->generateUrl('admin_Entrance_update', array('id' => $entrance->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_Entrance_edit', array('id' => $entrance->getId())));
        }
        $deleteForm = $this->createDeleteForm($entrance->getId(), 'admin_Entrance_delete');

        return array(
            'entrance' => $entrance,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="admin_Entrance_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('entrance', $field, $type);

        return $this->redirect($this->generateUrl('admin_Entrance'));
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
     * Deletes a Entrance entity.
     *
     * @Route("/{id}/delete", name="admin_Entrance_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Entrance $entrance, Request $request)
    {
        $form = $this->createDeleteForm($entrance->getId(), 'admin_Entrance_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($entrance);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_Entrance'));
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
