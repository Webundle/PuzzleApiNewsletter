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
 *      embedded = "expr(object.getSubscribers())"
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
     * @ORM\ManyToMany(targetEntity="Subscriber", mappedBy="groups")
     */
    private $subscribers;
    
    public function __construct() {
        $this->subscribers = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function setSubscribers (Collection $subscribers) : self {
        foreach ($subscribers as $subscriber) {
            $this->addSubscriber($subscriber);
        }
        
        return $this;
    }
    
    public function addSubscriber(Subscriber $subscriber) :self {
        if ($this->subscribers->count() === 0 || $this->subscribers->contains($subscriber) === false) {
            $this->subscribers->add($subscriber);
            $subscriber->addGroup($this);
        }
        
        return $this;
    }
    
    public function removeSubscriber(Subscriber $subscriber) :self {
        if ($this->subscribers->contains($subscriber) === true) {
            $this->subscribers->removeElement($subscriber);
        }
        
        return $this;
    }
    
    public function getSubscribers() :?Collection {
        return $this->subscribers;
    }
}