<?php

namespace Puzzle\Api\NewsletterBundle\Controller;

use Puzzle\Api\NewsletterBundle\Entity\Group;
use Puzzle\OAuthServerBundle\Controller\BaseFOSRestController;
use Puzzle\OAuthServerBundle\Service\Utils;
use Puzzle\OAuthServerBundle\Util\FormatUtil;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\Api\NewsletterBundle\Entity\Subscriber;

/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class GroupController extends BaseFOSRestController
{
    public function __construct(){
        parent::__construct();
        $this->fields = ['name', 'description'];
    }
    
    
    /**
     * @FOS\RestBundle\Controller\Annotations\View()
     * @FOS\RestBundle\Controller\Annotations\Get("/groups")
     */
    public function getNewsletterGroupsAction(Request $request) {
        $query = Utils::blameRequestQuery($request->query, $this->getUser());
        
        /** @var Puzzle\OAuthServerBundle\Service\Repository $repository */
        $repository = $this->get('papis.repository');
        $response = $repository->filter($query, Group::class, $this->connection);
        
        return $this->handleView(FormatUtil::formatView($request, $response));
    }
    
    /**
     * @FOS\RestBundle\Controller\Annotations\View()
     * @FOS\RestBundle\Controller\Annotations\Get("/groups/{id}")
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter("group", class="PuzzleApiNewsletterBundle:Group")
     */
    public function getNewsletterGroupAction(Request $request, Group $group) {
        if ($group->getCreatedBy()->getId() !== $this->getUser()->getId()) {
            /** @var Puzzle\OAuthServerBundle\Service\ErrorFactory $errorFactory */
            $errorFactory = $this->get('papis.error_factory');
            return $this->handleView($errorFactory->accessDenied($request));
        }
        
        return $this->handleView(FormatUtil::formatView($request, $group));
    }
    
    /**
     * @FOS\RestBundle\Controller\Annotations\View()
     * @FOS\RestBundle\Controller\Annotations\Post("/groups")
     */
    public function postNewsletterGroupAction(Request $request) {
        /** @var Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine')->getManager($this->connection);
        
        $data = $request->request->all();
        $data['parent'] = isset($data['parent']) && $data['parent'] ? $em->getRepository(Group::class)->find($data['parent']) : null;
        
        /** @var Puzzle\Api\NewsletterBundle\Entity\Group $group */
        $group = Utils::setter(new Group(), $this->fields, $data);
        
        $em->persist($group);
        $em->flush();
        
        return $this->handleView(FormatUtil::formatView($request, $group));
    }
    
    /**
     * @FOS\RestBundle\Controller\Annotations\View()
     * @FOS\RestBundle\Controller\Annotations\Put("/groups/{id}")
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter("group", class="PuzzleApiNewsletterBundle:Group")
     */
    public function putNewsletterGroupAction(Request $request, Group $group) {
        $user = $this->getUser();
        
        if ($group->getCreatedBy()->getId() !== $user->getId()) {
            /** @var Puzzle\OAuthServerBundle\Service\ErrorFactory $errorFactory */
            $errorFactory = $this->get('papis.error_factory');
            return $this->handleView($errorFactory->badRequest($request));
        }
        
        /** @var Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine')->getManager($this->connection);
        
        $data = $request->request->all();
        $data['parent'] = isset($data['parent']) && $data['parent'] ? $em->getRepository(Group::class)->find($data['parent']) : null;
        
        /** @var Puzzle\Api\NewsletterBundle\Entity\Group $group */
        $group = Utils::setter($group, $this->fields, $data);
        
        $em->flush();
        
        return $this->handleView(FormatUtil::formatView($request, $group));
    }
    
    /**
     * @FOS\RestBundle\Controller\Annotations\View()
     * @FOS\RestBundle\Controller\Annotations\Put("/groups/{id}/add-subscribers")
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter("group", class="PuzzleApiNewsletterBundle:Group")
     */
    public function putNewsletterGroupAddSubscribersAction(Request $request, Group $group) {
        $user = $this->getUser();
        
        if ($group->getCreatedBy()->getId() !== $user->getId()) {
            /** @var Puzzle\OAuthServerBundle\Service\ErrorFactory $errorFactory */
            $errorFactory = $this->get('papis.error_factory');
            return $this->handleView($errorFactory->badRequest($request));
        }
        
        $data = $request->request->all();
        $subscribersToAdd = $data['subscribers_to_add'] ? explode(',', $data['subscribers_to_add']) : null;
        if ($subscribersToAdd !== null) {
            /** @var Doctrine\ORM\EntityManager $em */
            $em = $this->get('doctrine')->getManager($this->connection);
            
            foreach ($subscribersToAdd as $subscriberId) {
                $subscriber = $em->getRepository(Subscriber::class)->find($subscriberId);
                $group->addSubscriber($subscriber);
            }
            
            
            $em->flush();
            
            return $this->handleView(FormatUtil::formatView($request, $group));
        }
        
        return $this->handleView(FormatUtil::formatView($request, null, 204));
    }
    
    
    /**
     * @FOS\RestBundle\Controller\Annotations\View()
     * @FOS\RestBundle\Controller\Annotations\Put("/groups/{id}/remove-subscribers")
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter("group", class="PuzzleApiNewsletterBundle:Group")
     */
    public function putNewsletterGroupRemoveSubscribersAction(Request $request, Group $group) {
        $user = $this->getUser();
        
        if ($group->getCreatedBy()->getId() !== $user->getId()) {
            /** @var Puzzle\OAuthServerBundle\Service\ErrorFactory $errorFactory */
            $errorFactory = $this->get('papis.error_factory');
            return $this->handleView($errorFactory->badRequest($request));
        }
        
        $data = $request->request->all();
        $subscribersToRemove = $data['subscribers_to_remove'] ? explode(',', $data['subscribers_to_remove']) : null;
        if ($subscribersToRemove !== null) {
            /** @var Doctrine\ORM\EntityManager $em */
            $em = $this->get('doctrine')->getManager($this->connection);
            
            foreach ($subscribersToRemove as $subscriberId) {
                $subscriber = $em->getRepository(Subscriber::class)->find($subscriberId);
                $group->removeSubscriber($subscriber);
            }
            
            $em->flush();
            
            return $this->handleView(FormatUtil::formatView($request, $group));
        }
        
        return $this->handleView(FormatUtil::formatView($request, null, 204));
    }
    
    /**
     * @FOS\RestBundle\Controller\Annotations\View()
     * @FOS\RestBundle\Controller\Annotations\Delete("/groups/{id}")
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter("group", class="PuzzleApiNewsletterBundle:Group")
     */
    public function deleteNewsletterGroupAction(Request $request, Group $group) {
        $user = $this->getUser();
        
        if ($group->getCreatedBy()->getId() !== $user->getId()) {
            /** @var Puzzle\OAuthServerBundle\Service\ErrorFactory $errorFactory */
            $errorFactory = $this->get('papis.error_factory');
            return $this->handleView($errorFactory->badRequest($request));
        }
        
        /** @var Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine')->getManager($this->connection);
        $em->remove($group);
        $em->flush();
        
        return $this->handleView(FormatUtil::formatView($request, null, 204));
    }
}