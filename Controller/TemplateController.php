<?php

namespace Puzzle\Api\NewsletterBundle\Controller;

use JMS\Serializer\SerializerInterface;
use Puzzle\Api\NewsletterBundle\Entity\Template;
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
class TemplateController extends BaseFOSRestController
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
        $this->fields = ['name', 'content', 'eventName'];
    }
    
	/**
	 * @FOS\RestBundle\Controller\Annotations\View()
	 * @FOS\RestBundle\Controller\Annotations\Get("/templates")
	 */
	public function getNewsletterTemplatesAction(Request $request) {
	    $query = Utils::blameRequestQuery($request->query, $this->getUser());
	    $response = $this->repository->filter($query, Template::class, $this->connection);
	    
	    return $this->handleView(FormatUtil::formatView($request, $response));
	}
	
	/**
	 * @FOS\RestBundle\Controller\Annotations\View()
	 * @FOS\RestBundle\Controller\Annotations\Get("/templates/{id}")
	 * @Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter("template", class="PuzzleApiNewsletterBundle:Template")
	 */
	public function getNewsletterTemplateAction(Request $request, Template $template) {
	    if ($template->getCreatedBy()->getId() !== $this->getUser()->getId()) {
	        return $this->handleView($this->errorFactory->accessDenied($request));
	    }
	    
	    return $this->handleView(FormatUtil::formatView($request, ['resources' => $template]));
	}
	
	/**
	 * @FOS\RestBundle\Controller\Annotations\View()
	 * @FOS\RestBundle\Controller\Annotations\Post("/templates")
	 */
	public function postNewsletterTemplateAction(Request $request) {
	    $data = $request->request->all();
	    /** @var Template $template */
	    $template = Utils::setter(new Template(), $this->fields, $data);
	    /** @var Doctrine\ORM\EntityManager $em */
	    $em = $this->doctrine->getManager($this->connection);
	    $em->persist($template);
	    $em->flush();
	    
	    return $this->handleView(FormatUtil::formatView($request, ['resources' => $template]));
	}
	
	/**
	 * @FOS\RestBundle\Controller\Annotations\View()
	 * @FOS\RestBundle\Controller\Annotations\Put("/{id}")
	 * @Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter("contact", class="PuzzleApiNewsletterBundle:Template")
	 */
	public function putNewsletterTemplateAction(Request $request, Template $template) {
	    $user = $this->getUser();
	    
	    if ($template->getCreatedBy()->getId() !== $user->getId()) {
	        return $this->handleView($this->errorFactory->badRequest($request));
	    }
	    
	    $data = $request->request->all();
	    /** @var Template $template */
	    $template = Utils::setter($template, $this->fields, $data);
	    /** @var Doctrine\ORM\EntityManager $em */
	    $em = $this->doctrine->getManager($this->connection);
	    $em->flush($template);
	    
	    return $this->handleView(FormatUtil::formatView($request, ['code' => 200]));
	}
	
	
	/**
	 * @FOS\RestBundle\Controller\Annotations\View()
	 * @FOS\RestBundle\Controller\Annotations\Delete("/templates/{id}")
	 * @Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter("template", class="PuzzleApiNewsletterBundle:Template")
	 */
	public function deleteNewsletterTemplateAction(Request $request, Template $template) {
	    $user = $this->getUser();
	    
	    if ($template->getCreatedBy()->getId() !== $user->getId()) {
	        return $this->handleView($this->errorFactory->badRequest($request));
	    }
	    
	    /** @var Doctrine\ORM\EntityManager $em */
	    $em = $this->doctrine->getManager($this->connection);
	    $em->remove($template);
	    $em->flush();
	    
	    return $this->handleView(FormatUtil::formatView($request, ['code' => 200]));
	}
}