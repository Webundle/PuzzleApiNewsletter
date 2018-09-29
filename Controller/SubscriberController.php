<?php

namespace Puzzle\Api\NewsletterBundle\Controller;

use Puzzle\Api\NewsletterBundle\Entity\Subscriber;
use Puzzle\OAuthServerBundle\Controller\BaseFOSRestController;
use Puzzle\OAuthServerBundle\Service\Utils;
use Puzzle\OAuthServerBundle\Util\FormatUtil;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class SubscriberController extends BaseFOSRestController
{
    public function __construct(){
        parent::__construct();
        $this->fields = ['name', 'email'];
    }
    
    /**
     * @FOS\RestBundle\Controller\Annotations\View()
     * @FOS\RestBundle\Controller\Annotations\Get("/subscribers")
     */
    public function getSubscribersAction(Request $request) {
        $query = Utils::blameRequestQuery($request->query, $this->getUser());
        
        /** @var Puzzle\OAuthServerBundle\Service\Repository $repository */
        $repository = $this->get('papis.repository');
        $response = $repository->filter($query, Subscriber::class, $this->connection);
        
        return $this->handleView(FormatUtil::formatView($request, $response));
    }
    
    /**
     * @FOS\RestBundle\Controller\Annotations\View()
     * @FOS\RestBundle\Controller\Annotations\Get("/subscribers/{id}")
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter("subscriber", class="PuzzleApiNewsletterBundle:Subscriber")
     */
    public function getSubscriberAction(Request $request, Subscriber $subscriber) {
        if ($subscriber->getCreatedBy()->getId() !== $this->getUser()->getId()) {
            /** @var Puzzle\OAuthServerBundle\Service\ErrorFactory $errorFactory */
            $errorFactory = $this->get('papis.error_factory');
            return $this->handleView($errorFactory->accessDenied($request));
        }
        
        return $this->handleView(FormatUtil::formatView($request, ['resources' => $subscriber]));
    }
    
    /**
     * @FOS\RestBundle\Controller\Annotations\View()
     * @FOS\RestBundle\Controller\Annotations\Post("/subscribers")
     */
    public function postSubscriberAction(Request $request) {
        $data = $request->request->all();
        
        /** @var Puzzle\Api\NewsletterBundle\Entity\Subscriber $subscriber */
        $subscriber = Utils::setter(new Subscriber(), $this->fields, $data);
        
        /** @var Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine')->getManager($this->connection);
        $em->persist($subscriber);
        $em->flush();
        
        return $this->handleView(FormatUtil::formatView($request, $subscriber));
    }
    
    
    /**
     * @FOS\RestBundle\Controller\Annotations\View()
     * @FOS\RestBundle\Controller\Annotations\Put("/subscribers/{id}")
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter("subscriber", class="PuzzleApiBundle:Subscriber")
     */
    public function putSubscriberAction(Request $request, Subscriber $subscriber) {
        $user = $this->getUser();
        
        if ($subscriber->getCreatedBy()->getId() !== $user->getId()){
            /** @var Puzzle\OAuthServerBundle\Service\ErrorFactory $errorFactory */
            $errorFactory = $this->get('papis.error_factory');
            return $this->handleView($errorFactory->badRequest($request));
        }
        
        $data = $request->request->all();
        
        /** @var Puzzle\Api\NewsletterBundle\Entity\Subscriber $subscriber */
        $subscriber = Utils::setter($subscriber, $this->fields, $data);
        
        /** @var Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine')->getManager($this->connection);
        $em->flush();
        
        return $this->handleView(FormatUtil::formatView($request, $subscriber));
    }
    
    /**
     * @FOS\RestBundle\Controller\Annotations\View()
     * @FOS\RestBundle\Controller\Annotations\Delete("/subscribers/{id}")
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter("subscriber", class="PuzzleApiNewsletterBundle:Subscriber")
     */
    public function deleteSubscriberAction(Request $request, Subscriber $subscriber) {
        $user = $this->getUser();
        
        if ($subscriber->getCreatedBy()->getId() !== $user->getId()) {
            /** @var Puzzle\OAuthServerBundle\Service\ErrorFactory $errorFactory */
            $errorFactory = $this->get('papis.error_factory');
            return $this->handleView($errorFactory->badRequest($request));
        }
        
        /** @var Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine')->getManager($this->connection);
        $em->remove($subscriber);
        $em->flush();
        
        return $this->handleView(FormatUtil::formatView($request, null, 204));
    }
}