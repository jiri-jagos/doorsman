<?php

namespace Globalcom\DoormanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Globalcom\DoormanBundle\Entity\House;
use Globalcom\DoormanBundle\Form\Type\HouseType;
use Globalcom\DoormanBundle\Form\Type\HouseFilterType;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * House controller.
 *
 * @Route("/admin/House")
 */
class HouseController extends Controller
{
    /**
     * Lists all House entities.
     *
     * @Route("/", name="admin_House")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new HouseFilterType());
        if (!is_null($response = $this->saveFilter($form, 'house', 'admin_House'))) {
            return $response;
        }
        $qb = $em->getRepository('GlobalcomDoormanBundle:House')->createQueryBuilder('h');
        $paginator = $this->filter($form, $qb, 'house');
        
        return array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a House entity.
     *
     * @Route("/{id}/zobrazit", name="admin_House_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(House $house)
    {
        $deleteForm = $this->createDeleteForm($house->getId(), 'admin_House_delete');

        return array(
            'house' => $house,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new House entity.
     *
     * @Route("/novy", name="admin_House_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $house = new House();
        $form = $this->createForm(new HouseType(), $house);

        return array(
            'house' => $house,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new House entity.
     *
     * @Route("/vytvorit", name="admin_House_create")
     * @Method("POST")
     * @Template("GlobalcomDoormanBundle:House:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $house = new House();
        $form = $this->createForm(new HouseType(), $house);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($house);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_House_show', array('id' => $house->getId())));
        }

        return array(
            'house' => $house,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing House entity.
     *
     * @Route("/{id}/editovat", name="admin_House_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(House $house)
    {
        $editForm = $this->createForm(new HouseType(), $house, array(
            'action' => $this->generateUrl('admin_House_update', array('id' => $house->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($house->getId(), 'admin_House_delete');

        return array(
            'house' => $house,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing House entity.
     *
     * @Route("/{id}/aktualizovat", name="admin_House_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("GlobalcomDoormanBundle:House:edit.html.twig")
     */
    public function updateAction(House $house, Request $request)
    {
        $editForm = $this->createForm(new HouseType(), $house, array(
            'action' => $this->generateUrl('admin_House_update', array('id' => $house->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_House_edit', array('id' => $house->getId())));
        }
        $deleteForm = $this->createDeleteForm($house->getId(), 'admin_House_delete');

        return array(
            'house' => $house,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/seradit/{field}/{type}", name="admin_House_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('house', $field, $type);

        return $this->redirect($this->generateUrl('admin_House'));
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
     * Deletes a House entity.
     *
     * @Route("/{id}/delete", name="admin_House_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(House $house, Request $request)
    {
        $form = $this->createDeleteForm($house->getId(), 'admin_House_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($house);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_House'));
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
