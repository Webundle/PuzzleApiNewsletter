<?php

namespace Puzzle\Api\NewsletterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Hateoas\Configuration\Annotation as Hateoas;
use Puzzle\OAuthServerBundle\Traits\PrimaryKeyable;
use Puzzle\OAuthServerBundle\Traits\Nameable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Doctrine\Common\Collections\Collection;


/**
 * Newsletter Subscriber
 *
 * @ORM\Table(name="newsletter_subscriber")
 * @ORM\Entity()
 * @JMS\ExclusionPolicy("all")
 * @JMS\XmlRoot("newsletter_subscriber")
 * @Hateoas\Relation(
 * 		name = "self", 
 * 		href = @Hateoas\Route(
 * 			"get_newsletter_subscriber", 
 * 			parameters = {"id" = "expr(object.getId())"},
 * 			absolute = true,
 * ))
 */
class Subscriber
{
    use PrimaryKeyable,
        Timestampable,
        Nameable,
        Blameable;
    
    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $email;
    
    /**
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="subscribers")
     * @ORM\JoinTable(name="subscriber_groups",
     *      joinColumns={@ORM\JoinColumn(name="subscriber_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    private $groups;
    
    public function __construct() {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function setEmail($email) :self {
        $this->email = $email;
        return $this;
    }

    public function getEmail() :?string {
        return $this->email;
    }
    
    public function setGroups (Collection $groups) : self {
        foreach ($groups as $group) {
            $this->addGroup($group);
        }
        
        return $this;
    }
    
    public function addGroup(Group $group) :self {
        if ($this->groups->count() === 0 || $this->groups->contains($group) === false) {
            $this->groups->add($group);
        }
        
        return $this;
    }
    
    public function removeGroup(Group $group) :self {
        if ($this->groups->contains($group) === true) {
            $this->groups->removeElement($group);
        }
        
        return $this;
    }
    
    public function getGroups() :?Collection {
        return $this->groups;
    }
}
