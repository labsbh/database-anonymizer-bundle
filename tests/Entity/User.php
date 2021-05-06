<?php

declare(strict_types=1);

namespace WebnetFr\DatabaseAnonymizerBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebnetFr\DatabaseAnonymizerBundle\Annotation as Anonymize;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity
 *
 * This annotation marks the entities to anonymize.
 * @Anonymize\Table()
 */
class User
{
    /**
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\Column(name="email", type="string", length=256, nullable=true)
     * @Anonymize\Field(generator="faker", formatter="safeEmail")
     */
    public $email;

    /**
     * @ORM\Column(name="firstname", type="string", length=256, nullable=true)
     * @Anonymize\Field(generator="faker", formatter="firstName", arguments={"male"})
     */
    public $firstname;

    /**
     * @ORM\Column(name="lastname", type="string", length=256, nullable=true)
     * @Anonymize\Field(generator="faker", formatter="lastName")
     */
    public $lastname;

    /**
     * @ORM\Column(name="birthdate", type="date", nullable=true)
     * @Anonymize\Field(generator="faker", formatter="dateTimeBetween", date_format="Y-m-d", arguments={"-90 years", "-1 year"})
     */
    public $birthdate;

    /**
     * @ORM\Column(name="phone", type="string", length=256, nullable=true)
     * @Anonymize\Field(generator="faker", formatter="phoneNumber")
     */
    public $phone;

    /**
     * @ORM\Column(name="password", type="string", length=256, nullable=true)
     * @Anonymize\Field(generator="constant", value="pass123")
     */
    public $password;
}
