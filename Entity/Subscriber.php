<?php

namespace Puzzle\Api\NewsletterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Hateoas\Configuration\Annotation as Hateoas;
use Puzzle\OAuthServerBundle\Traits\PrimaryKeyable;
use Puzzle\OAuthServerBundle\Traits\Nameable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;


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
        Nameable,
        Blameable;
    
    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $email;
    
    public function setEmail($email) :self {
        $this->email = $email;
        return $this;
    }

    public function getEmail() :?string {
        return $this->email;
    }
}
