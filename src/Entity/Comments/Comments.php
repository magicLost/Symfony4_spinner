<?php

namespace App\Entity\Comments;



use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentsRepository")
 * @ORM\Table(name="comments")
 *
 */
class Comments
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $parent_id;
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;
    /**
     * @ORM\Column(type="text")
     */
    private $content;
    /**
     * @ORM\Column(type="datetime")
     */
    private $created;
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $reply_to;
    /**
     * @ORM\Column(type="boolean")
     */
    private $has_child;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * @param mixed $parent_id
     */
    public function setParentId($parent_id): void
    {
        $this->parent_id = $parent_id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created): void
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getReplyTo()
    {
        return $this->reply_to;
    }

    /**
     * @param mixed $reply_to
     */
    public function setReplyTo($reply_to): void
    {
        $this->reply_to = $reply_to;
    }

    /**
     * @return mixed
     */
    public function getHasChild()
    {
        return $this->has_child;
    }

    /**
     * @param mixed $has_child
     */
    public function setHasChild($has_child): void
    {
        $this->has_child = $has_child;
    }



}