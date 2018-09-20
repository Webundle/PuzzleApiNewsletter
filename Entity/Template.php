<?php

namespace Puzzle\Api\NewsletterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Hateoas\Configuration\Annotation as Hateoas;
use Puzzle\OAuthServerBundle\Traits\PrimaryKeyable;
use Puzzle\OAuthServerBundle\Traits\Nameable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;


/**
 * Template
 *
 * @ORM\Table(name="newsletter_template")
 * @ORM\Entity()
 * @JMS\ExclusionPolicy("all")
 * @JMS\XmlRoot("newsletter_template")
 * @Hateoas\Relation(
 * 		name = "self",
 * 		href = @Hateoas\Route(
 * 			"get_newsletter_template",
 * 			parameters = {"id" = "expr(object.getId())"},
 * 			absolute = true,
 * ))
 */
class Template
{
    use PrimaryKeyable,
        Nameable,
        Blameable;

    /**
     * @var string
     * @ORM\Column(name="eventName", type="string", length=255)
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $eventName;
    
    /**
     * @var string
     * @ORM\Column(name="content", type="text")
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $content;
   
    public function setEventName($eventName) : self {
        $this->eventName = $eventName;
        return $this;
    }
    
    public function getEventName() :? string {
        return $this->eventName;
    }
    
    public function setContent($content) : self {
        $this->content = $content;
        return $this;
    }
    
    public function getContent() :? string {
        return $this->content;
    }
}
