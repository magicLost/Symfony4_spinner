<?php

namespace App\Entity\Auth;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as MineAssert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;


    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=2,
     *     max=45,
     *     minMessage="fos_user.username.short",
     *     groups={"Profile", "ResetPassword", "Registration", "ChangePassword"}
     * )
     * @MineAssert\MineRegex(
     *     message="fos_user.username.regex",
     *     pattern="/[a-zA-Z0-9-_]{0,}/"
     * )
     *
     */
    protected $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=6,
     *     max=254,
     *     minMessage="fos_user.password.short",
     *     groups={"Profile", "ResetPassword", "Registration", "ChangePassword"}
     * )
     *
     */
    protected $plainPassword;

    /**
     * @return integer
     */
    public function getId()
    {


        return $this->id;
    }


}