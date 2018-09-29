<?php

namespace Puzzle\Api\NewsletterBundle\Controller;

use Puzzle\Api\NewsletterBundle\Entity\Template;
use Puzzle\OAuthServerBundle\Controller\BaseFOSRestController;
use Puzzle\OAuthServerBundle\Service\Utils;
use Puzzle\OAuthServerBundle\Util\FormatUtil;
use Symfony\Component\HttpFoundation\Request;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class TemplateController extends BaseFOSRestController
{
    public function __construct(){
        parent::__construct();
        $this->fields = ['name', 'content', 'eventName'];
    }
    
	/**
	 * @FOS\RestBundle\Controller\Annotations\View()
	 * @FOS\RestBundle\Controller\Annotations\Get("/templates")
	 */
	public function getNewsletterTemplatesAction(Request $request) {
	    $query = Utils::blameRequestQuery($request->query, $this->getUser());
	    
	    /** @var Puzzle\OAuthServerBundle\Service\Repository $repository */
	    $repository = $this->get('papis.repository');
	    $response = $repository->filter($query, Template::class, $this->connection);
	    
	    return $this->handleView(FormatUtil::formatView($request, $response));
	}
	
	/**
	 * @FOS\RestBundle\Controller\Annotations\View()
	 * @FOS\RestBundle\Controller\Annotations\Get("/templates/{id}")
	 * @Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter("template", class="PuzzleApiNewsletterBundle:Template")
	 */
	public function getNewsletterTemplateAction(Request $request, Template $template) {
	    if ($template->getCreatedBy()->getId() !== $this->getUser()->getId()) {
	        /** @var Puzzle\OAuthServerBundle\Service\ErrorFactory $errorFactory */
	        $errorFactory = $this->get('papis.error_factory');
	        return $this->handleView($errorFactory->accessDenied($request));
	    }
	    
	    return $this->handleView(FormatUtil::formatView($request, $template));
	}
	
	/**
	 * @FOS\RestBundle\Controller\Annotations\View()
	 * @FOS\RestBundle\Controller\Annotations\Post("/templates")
	 */
	public function postNewsletterTemplateAction(Request $request) {
	    $data = $request->request->all();
	    
	    /** @var Puzzle\Api\NewsletterBundle\Entity\Template $template */
	    $template = Utils::setter(new Template(), $this->fields, $data);
	    
	    /** @var Doctrine\ORM\EntityManager $em */
	    $em = $this->get('doctrine')->getManager($this->connection);
	    $em->persist($template);
	    $em->flush();
	    
	    return $this->handleView(FormatUtil::formatView($request, $template));
	}
	
	/**
	 * @FOS\RestBundle\Controller\Annotations\View()
	 * @FOS\RestBundle\Controller\Annotations\Put("/{id}")
	 * @Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter("template", class="PuzzleApiNewsletterBundle:Template")
	 */
	public function putNewsletterTemplateAction(Request $request, Template $template) {
	    $user = $this->getUser();
	    
	    if ($template->getCreatedBy()->getId() !== $user->getId()) {
	        /** @var Puzzle\OAuthServerBundle\Service\ErrorFactory $errorFactory */
	        $errorFactory = $this->get('papis.error_factory');
	        return $this->handleView($errorFactory->badRequest($request));
	    }
	    
	    $data = $request->request->all();
	    
	    /** @var Template $template */
	    $template = Utils::setter($template, $this->fields, $data);
	    
	    /** @var Doctrine\ORM\EntityManager $em */
	    $em = $this->get('doctrine')->getManager($this->connection);
	    $em->flush($template);
	    
	    return $this->handleView(FormatUtil::formatView($request, $template));
	}
	
	
	/**
	 * @FOS\RestBundle\Controller\Annotations\View()
	 * @FOS\RestBundle\Controller\Annotations\Delete("/templates/{id}")
	 * @Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter("template", class="PuzzleApiNewsletterBundle:Template")
	 */
	public function deleteNewsletterTemplateAction(Request $request, Template $template) {
	    $user = $this->getUser();
	    
	    if ($template->getCreatedBy()->getId() !== $user->getId()) {
	        /** @var Puzzle\OAuthServerBundle\Service\ErrorFactory $errorFactory */
	        $errorFactory = $this->get('papis.error_factory');
	        return $this->handleView($errorFactory->badRequest($request));
	    }
	    
	    /** @var Doctrine\ORM\EntityManager $em */
	    $em = $this->get('doctrine')->getManager($this->connection);
	    $em->remove($template);
	    $em->flush();
	    
	    return $this->handleView(FormatUtil::formatView($request, null, 204));
	}
}