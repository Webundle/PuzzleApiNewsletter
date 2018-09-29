<?php

namespace Puzzle\Api\NewsletterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Hateoas\Configuration\Annotation as Hateoas;
use Puzzle\OAuthServerBundle\Traits\PrimaryKeyable;
use Puzzle\OAuthServerBundle\Traits\Nameable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;


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
        Timestampable,
        Sluggable,
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
    
    /**
     * @ORM\Column(name="slug", type="string", length=255)
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $slug;
    
    public function getSluggableFields() {
        return [ 'name' ];
    }
   
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
