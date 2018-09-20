<?php

namespace Puzzle\Api\NewsletterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Hateoas\Configuration\Annotation as Hateoas;

use Doctrine\Common\Collections\Collection;
use Puzzle\OAuthServerBundle\Traits\PrimaryKeyable;
use Puzzle\OAuthServerBundle\Traits\Describable;
use Puzzle\OAuthServerBundle\Traits\Nameable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * Newsletter Group
 *
 * @ORM\Table(name="newsletter_group")
 * @ORM\Entity()
 * @JMS\ExclusionPolicy("all")
 * @JMS\XmlRoot("newsletter_group")
 * @Hateoas\Relation(
 * 		name = "self",
 * 		href = @Hateoas\Route(
 * 			"get_newsletter_group",
 * 			parameters = {"id" = "expr(object.getId())"},
 * 			absolute = true,
 * ))
 * @Hateoas\Relation(
 * 		name = "subscribers", 
 *      exclusion = @Hateoas\Exclusion(excludeIf = "expr(object.getSubscribers() === null)"),
 * 		href = @Hateoas\Route(
 * 			"get_newsletter_subscribers", 
 * 			parameters = {"filter" = "id=:~expr(object.stringify(',',object.getSubscribers()))"},
 * 			absolute = true,
 * ))
 */
class Group
{
    use PrimaryKeyable,
        Describable,
        Nameable,
        Blameable,
        Timestampable;
    
    /**
     * @var array
     * @ORM\Column(name="subscribers", type="array", nullable=true)
     * @JMS\Expose
     * @JMS\Type("array")
     */
    private $subscribers;
    
    public function setSubscribers($subscribers) :self {
        foreach ($subscribers as $subscriber){
            $this->addSubscriber($subscriber);
        }
        
        return $this;
    }
    
    public function addSubscriber($subscriber) :self {
        $this->subscribers[] = $subscriber;
        $this->subscribers = array_unique($this->subscribers);
        
        return $this;
    }
    
    public function removeSubscriber($subscriber) :self {
        $this->subscribers = array_diff($this->subscribers, [$subscriber]);
        return $this;
    }
    
    public function getSubscribers() :?array {
        return $this->subscribers;
    }
}