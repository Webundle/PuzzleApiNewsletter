<?php

namespace Puzzle\Api\NewsletterBundle\Controller;

use JMS\Serializer\SerializerInterface;
use Puzzle\Api\NewsletterBundle\Entity\Subscriber;
use Puzzle\OAuthServerBundle\Controller\BaseFOSRestController;
use Puzzle\OAuthServerBundle\Service\ErrorFactory;
use Puzzle\OAuthServerBundle\Service\Repository;
use Puzzle\OAuthServerBundle\Service\Utils;
use Puzzle\OAuthServerBundle\Util\FormatUtil;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 * 
 */
class SubscriberController extends BaseFOSRestController
{
    /**
     * @param RegistryInterface         $doctrine
     * @param Repository                $repository
     * @param SerializerInterface       $serializer
     * @param EventDispatcherInterface  $dispatcher
     * @param ErrorFactory              $errorFactory
     */
    public function __construct(
        RegistryInterface $doctrine,
        Repository $repository,
        SerializerInterface $serializer,
        EventDispatcherInterface $dispatcher,
        ErrorFactory $errorFactory
    ){
        parent::__construct($doctrine, $repository, $serializer, $dispatcher, $errorFactory);
        $this->fields = ['name', 'email'];
    }
    
	/**
	 * @FOS\RestBundle\Controller\Annotations\View()
	 * @FOS\RestBundle\Controller\Annotations\Get("/subscribers")
	 */
	public function getNewsletterSubscribersAction(Request $request) {
	    $query = Utils::blameRequestQuery($request->query, $this->getUser());
	    $response = $this->repository->filter($query, Subscriber::class, $this->connection);
	    
	    return $this->handleView(FormatUtil::formatView($request, $response));
	}
	
	/**
	 * @FOS\RestBundle\Controller\Annotations\View()
	 * @FOS\RestBundle\Controller\Annotations\Get("/subscribers/{id}")
	 * @Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter("subscriber", class="PuzzleApiNewsletterBundle:Subscriber")
	 */
	public function getNewsletterSubscriberAction(Request $request, Subscriber $subscriber) {
	    if ($subscriber->getCreatedBy()->getId() !== $this->getUser()->getId()) {
	        return $this->handleView($this->errorFactory->accessDenied($request));
	    }
	    
	    return $this->handleView(FormatUtil::formatView($request, ['resources' => $subscriber]));
	}
	
	/**
	 * @FOS\RestBundle\Controller\Annotations\View()
	 * @FOS\RestBundle\Controller\Annotations\Post("/subscribers")
	 */
	public function postNewsletterSubscriberAction(Request $request) {
	    $data = $request->request->all();
	    /** @var Subscriber $subscriber */
	    $subscriber = Utils::setter(new Subscriber(), $this->fields, $data);
	    /** @var Doctrine\ORM\EntityManager $em */
	    $em = $this->doctrine->getManager($this->connection);
	    $em->persist($subscriber);
	    $em->flush();
	    
	    return $this->handleView(FormatUtil::formatView($request, ['resources' => $subscriber]));
	}
	
	/**
	 * @FOS\RestBundle\Controller\Annotations\View()
	 * @FOS\RestBundle\Controller\Annotations\Put("/subscribers/{id}")
	 * @Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter("subscriber", class="PuzzleApiNewsletterBundle:Subscriber")
	 */
	public function putNewsletterSubscriberAction(Request $request, Subscriber $subscriber) {
	    if ($subscriber->getCreatedBy()->getId() !== $this->getUser()->getId()) {
	        return $this->handleView($this->errorFactory->accessDenied($request));
	    }
	    
	    $data = $request->request->all();
	    /** @var Subscriber $subscriber */
	    $subscriber = Utils::setter($subscriber, $this->fields, $data);
	    /** @var Doctrine\ORM\EntityManager $em */
	    $em = $this->doctrine->getManager($this->connection);
	    $em->flush($subscriber);
	    
	    return $this->handleView(FormatUtil::formatView($request, ['resources' => $subscriber]));
	}
	
	/**
	 * @FOS\RestBundle\Controller\Annotations\View()
	 * @FOS\RestBundle\Controller\Annotations\Delete("/subscribers/{id}")
	 * @Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter("subscriber", class="PuzzleApiNewsletterBundle:Subscriber")
	 */
	public function deleteNewsletterSubscriberAction(Request $request, Subscriber $subscriber) {
	    $user = $this->getUser();
	    
	    if ($subscriber->getCreatedBy()->getId() !== $user->getId()) {
	        return $this->handleView($this->errorFactory->badRequest($request));
	    }
	    
	    /** @var Doctrine\ORM\EntityManager $em */
	    $em = $this->doctrine->getManager($this->connection);
	    $em->remove($subscriber);
	    $em->flush($subscriber);
	    
	    return $this->handleView(FormatUtil::formatView($request, ['code' => 200]));
	}
}