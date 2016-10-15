<?php
// src/AppBundle/Entity/User.php

namespace CoreBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

//http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/annotations-reference.html

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
    
    /**
     * 
     * Stripe customer id
     * 
     * @var boolean
     * @ORM\Column(type="string", length=127, nullable=true)
     */
    protected $customerId;
    
    /**
     * 
     * Get customer Id
     * 
     * @return string
     */
    
    public function getCustomerId(){
        return $this->customerId;
    }
    
    /**
     * 
     * Set customer Id
     * 
     * @return string
     */
    
    public function setCustomerId($customerId){
        $this->customerId = $customerId;
        return $this->customerId;
    }
}