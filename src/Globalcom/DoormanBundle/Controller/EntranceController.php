<?php

namespace Globalcom\DoormanBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Globalcom\DoormanBundle\DomainObject\EntranceAssignKeys;
use Globalcom\DoormanBundle\Entity\KeyGroupRepository;
use Globalcom\DoormanBundle\Entity\KeyRepository;
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
 * @Route("/admin/vchody")
 */
class EntranceController extends Controller
{
    /**
     * Lists all Entrance entities.
     *
     * @Route("/", name="admin_entrance")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new EntranceFilterType());
        if (!is_null($response = $this->saveFilter($form, 'entrance', 'admin_entrance'))) {
            return $response;
        }
        $qb = $em->getRepository('GlobalcomDoormanBundle:Entrance')->createQueryBuilder('e');
        $paginator = $this->filter($form, $qb, 'entrance');

        return array(
            'form' => $form->createView(),
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Entrance entity.
     *
     * @Route("/{id}/zobrazit", name="admin_entrance_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Entrance $entrance)
    {
        $deleteForm = $this->createDeleteForm($entrance->getId(), 'admin_entrance_delete');

        return array(
            'entrance' => $entrance,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Entrance entity.
     *
     * @Route("/novy", name="admin_entrance_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entrance = new Entrance();
        $form = $this->createForm(new EntranceType(), $entrance);

        return array(
            'entrance' => $entrance,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a page for assigning keys to an existing KeyGroup entity.
     *
     * @Route("/{id}/priradit-klice", name="admin_entrance_assignKeys", requirements={"id"="\d+"})
     * #Method("GET")
     * @Template()
     */
    public function assignKeysAction(Entrance $entrance, Request $request)
    {
        $formData = $this->readEntranceAssignKeyFormData($entrance);

        $form = $this->createForm('entranceAssignKeysType', $formData, array('entrance' => $entrance));

        $form->handleRequest($request);
        if ($form->isValid() && $request->request->has('entranceAssignKeysType')) {
            /** @var EntranceAssignKeys $formData */
            $formData = $form->getData();

            $requestEntranceAssignKeysType = $request->request->get('entranceAssignKeysType');

            if ($form->get('addToEntrance')->isClicked()) {

                if (isset($requestEntranceAssignKeysType['keyGroupsToAdd']) && $formData->getKeyGroupsToAdd()->count()
                ) {
                    $entrance->addKeyGroups($formData->getKeyGroupsToAdd());
                }
                if (isset($requestEntranceAssignKeysType['keysToAdd']) && $formData->getKeysToAdd()->count()) {
                    $entrance->addKeys($formData->getKeysToAdd());
                }

            } elseif ($form->get('removeFromEntrance')->isClicked()) {
                if (isset($requestEntranceAssignKeysType['keyGroupsToRemove']) && $formData->getKeyGroupsToRemove()
                        ->count()
                ) {
                    $entrance->removeKeyGroups($formData->getKeyGroupsToRemove());
                }

                if (isset($requestEntranceAssignKeysType['keysToRemove']) && $formData->getKeysToRemove()->count()) {
                    $entrance->removeKeys($formData->getKeysToRemove());
                }
            }

            $om = $this->getDoctrine()->getManager();
            $om->persist($entrance);
            $om->flush();

            return $this->redirect(
                $this->generateUrl(
                    'admin_entrance_assignKeys',
                    array(
                        'id' => $entrance->getId()
                    )
                )
            );
        }

        return array(
            'entrance' => $entrance,
            'form' => $form->createView(),
            'formData' => $formData,
        );
    }

    private function readEntranceAssignKeyFormData(Entrance $entrance)
    {
        /** @var KeyRepository $keysRepo */
        $keysRepo = $this->getDoctrine()->getManager()->getRepository('GlobalcomDoormanBundle:Key');

        $formData = new EntranceAssignKeys();
        $formData
            ->setKeysToAdd(
                new ArrayCollection($keysRepo->findAllNotInEntrance($entrance))
            )
            ->setKeysToRemove(clone($entrance->getKeys()));

        return $formData;
    }

    /**
     * Renders a form to assign keys to an existing KeyGroup entity.
     *
     * @Method("GET")
     * @Template()
     */
    public function assignKeysFormAction(Entrance $entrance)
    {
        $formData = $this->readKeygroupAssignKeyFormData($entrance);

        $form = $this->createForm('entranceAssignKeysType', $formData, array('entrance' => $entrance));

        return array(
            'form' => $form->createView(),
        );
    }

    private function readKeygroupAssignKeyFormData(Entrance $entrance)
    {
        /** @var KeyRepository $keyRepo */
        $keyRepo = $this->getDoctrine()->getManager()->getRepository('GlobalcomDoormanBundle:Key');

        /** @var KeyGroupRepository $keyGroupRepo */
        $keyGroupRepo = $this->getDoctrine()->getManager()->getRepository('GlobalcomDoormanBundle:KeyGroup');

        $formData = new EntranceAssignKeys();
        $formData
            ->setKeyGroupsToAdd($keyGroupRepo->findAllNotInEntrance($entrance))
            ->setKeyGroupsToRemove(clone($entrance->getKeyGroups()))
            ->setKeysToAdd(
                new ArrayCollection($keyRepo->findAllNotInEntrance($entrance))
            )
            ->setKeysToRemove(clone($entrance->getKeys()));

        return $formData;
    }


    /**
     * Creates a new Entrance entity.
     *
     * @Route("/vytvorit", name="admin_entrance_create")
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

            return $this->redirect($this->generateUrl('admin_entrance_show', array('id' => $entrance->getId())));
        }

        return array(
            'entrance' => $entrance,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Entrance entity.
     *
     * @Route("/{id}/editovat", name="admin_entrance_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Entrance $entrance)
    {
        $editForm = $this->createForm(
            new EntranceType(), $entrance, array(
                'action' => $this->generateUrl('admin_entrance_update', array('id' => $entrance->getid())),
                'method' => 'PUT',
            )
        );
        $deleteForm = $this->createDeleteForm($entrance->getId(), 'admin_entrance_delete');

        return array(
            'entrance' => $entrance,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Entrance entity.
     *
     * @Route("/{id}/aktualizovat", name="admin_entrance_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("GlobalcomDoormanBundle:Entrance:edit.html.twig")
     */
    public function updateAction(Entrance $entrance, Request $request)
    {
        $editForm = $this->createForm(
            new EntranceType(), $entrance, array(
                'action' => $this->generateUrl('admin_entrance_update', array('id' => $entrance->getid())),
                'method' => 'PUT',
            )
        );
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_entrance_edit', array('id' => $entrance->getId())));
        }
        $deleteForm = $this->createDeleteForm($entrance->getId(), 'admin_entrance_delete');

        return array(
            'entrance' => $entrance,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/seradit/{field}/{type}", name="admin_entrance_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('entrance', $field, $type);

        return $this->redirect($this->generateUrl('admin_entrance'));
    }

    /**
     * @param string $name session name
     * @param string $field field name
     * @param string $type sort type ("ASC"/"DESC")
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
     * @param string $name
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
     * @param  string $name route/entity name
     * @param  string $route route name, if different from entity name
     * @param  array $params possible route parameters
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
     * @param  FormInterface $form
     * @param  QueryBuilder $qb
     * @param  string $name
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
     * @Route("/{id}/delete", name="admin_entrance_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Entrance $entrance, Request $request)
    {
        $form = $this->createDeleteForm($entrance->getId(), 'admin_entrance_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($entrance);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_entrance'));
    }

    /**
     * Create Delete form
     *
     * @param integer $id
     * @param string $route
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();
    }

}
