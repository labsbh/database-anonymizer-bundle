<?php

declare(strict_types=1);

namespace WebnetFr\DatabaseAnonymizerBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebnetFr\DatabaseAnonymizerBundle\Annotation as Anonymize;

/**
 * @ORM\Table(name="orders")
 * @ORM\Entity
 *
 * This annotation marks the entities to anonymize.
 * @Anonymize\Table()
 */
class Order
{
    /**
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\Column(name="address", type="string", length=256, nullable=true)
     * @Anonymize\Field(generator="faker", formatter="address")
     */
    public $address;

    /**
     * @ORM\Column(name="street_address", type="string", length=256, nullable=true)
     * @Anonymize\Field(generator="faker", formatter="streetAddress")
     */
    public $streetAddress;

    /**
     * @ORM\Column(name="zip_code", type="string", length=10, nullable=true)
     * @Anonymize\Field(generator="faker", formatter="postcode")
     */
    public $zipCode;

    /**
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     * @Anonymize\Field(generator="faker", formatter="city")
     */
    public $city;

    /**
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     * @Anonymize\Field(generator="faker", formatter="country")
     */
    public $country;

    /**
     * @ORM\Column(name="comment", type="text", length=0, nullable=true)
     * @Anonymize\Field(generator="faker", formatter="text", arguments={300})
     */
    public $comment;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Anonymize\Field(generator="faker", formatter="dateTime", date_format="Y-m-d H:i:s")
     */
    public $createdAt;
}
