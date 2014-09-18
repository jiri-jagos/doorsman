<?php

namespace Globalcom\DoormanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Globalcom\DoormanBundle\Entity\KeyGroup;
use Globalcom\DoormanBundle\Form\Type\KeyGroupType;
use Globalcom\DoormanBundle\Form\Type\KeyGroupFilterType;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * KeyGroup controller.
 *
 * @Route("/admin/skupiny-klicu")
 */
class KeyGroupController extends Controller
{
    /**
     * Lists all KeyGroup entities.
     *
     * @Route("/", name="admin_keyGroup")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new KeyGroupFilterType());
        if (!is_null($response = $this->saveFilter($form, 'keygroup', 'admin_keyGroup'))) {
            return $response;
        }
        $qb = $em->getRepository('GlobalcomDoormanBundle:KeyGroup')->createQueryBuilder('k');
        $paginator = $this->filter($form, $qb, 'keygroup');
        
        return array(
            'form'      => $form->createView(),
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a KeyGroup entity.
     *
     * @Route("/{id}/zobrazit", name="admin_keyGroup_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(KeyGroup $keygroup)
    {
        $deleteForm = $this->createDeleteForm($keygroup->getId(), 'admin_keyGroup_delete');

        return array(
            'keygroup' => $keygroup,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new KeyGroup entity.
     *
     * @Route("/novy", name="admin_keyGroup_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $keygroup = new KeyGroup();
        $form = $this->createForm(new KeyGroupType(), $keygroup);

        return array(
            'keygroup' => $keygroup,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new KeyGroup entity.
     *
     * @Route("/vytvorit", name="admin_keyGroup_create")
     * @Method("POST")
     * @Template("GlobalcomDoormanBundle:KeyGroup:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $keygroup = new KeyGroup();
        $form = $this->createForm(new KeyGroupType(), $keygroup);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($keygroup);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_keyGroup_show', array('id' => $keygroup->getId())));
        }

        return array(
            'keygroup' => $keygroup,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing KeyGroup entity.
     *
     * @Route("/{id}/editovat", name="admin_keyGroup_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(KeyGroup $keygroup)
    {
        $editForm = $this->createForm(new KeyGroupType(), $keygroup, array(
            'action' => $this->generateUrl('admin_keyGroup_update', array('id' => $keygroup->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($keygroup->getId(), 'admin_keyGroup_delete');

        return array(
            'keygroup' => $keygroup,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing KeyGroup entity.
     *
     * @Route("/{id}/aktualizovat", name="admin_keyGroup_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("GlobalcomDoormanBundle:KeyGroup:edit.html.twig")
     */
    public function updateAction(KeyGroup $keygroup, Request $request)
    {
        $editForm = $this->createForm(new KeyGroupType(), $keygroup, array(
            'action' => $this->generateUrl('admin_keyGroup_update', array('id' => $keygroup->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_keyGroup_edit', array('id' => $keygroup->getId())));
        }
        $deleteForm = $this->createDeleteForm($keygroup->getId(), 'admin_keyGroup_delete');

        return array(
            'keygroup' => $keygroup,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/seradit/{field}/{type}", name="admin_keyGroup_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('keygroup', $field, $type);

        return $this->redirect($this->generateUrl('admin_keyGroup'));
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
     * Deletes a KeyGroup entity.
     *
     * @Route("/{id}/delete", name="admin_keyGroup_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(KeyGroup $keygroup, Request $request)
    {
        $form = $this->createDeleteForm($keygroup->getId(), 'admin_keyGroup_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($keygroup);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_keyGroup'));
    }

    /**
     * @param KeyGroup $keyGroup
     * @Template()
     */
    public function deleteFormAction(KeyGroup $keyGroup)
    {
        $form = $this->createDeleteForm($keyGroup->getId(), 'admin_keyGroup_delete');

        return array(
            'delete_form' => $form->createView()
        );
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
