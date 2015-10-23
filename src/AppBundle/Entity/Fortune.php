<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Fortune
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\FortuneRepository")
 */
class Fortune
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="upVote", type="integer")
     */
    private $upVote;

    /**
     * @var integer
     *
     * @ORM\Column(name="downVote", type="integer")
     */
    private $downVote;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="fortune")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $comments;

    /**
     * Setting default values.
     *
     * @return integer
     */
    public function __construct()
    {
        $this->upVote = 0;
        $this->downVote = 0;
        $this->createdAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Fortune
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Fortune
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Fortune
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Fortune
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get upvote
     *
     * @return integer
     */
    public function getUpVote()
    {
        return $this->upVote;
    }

    /**
     * Upvote
     *
     * @return Fortune
     */
    public function voteUp()
    {
        $this->upVote++;

        return $this;
    }

    /**
     * Get downvote
     *
     * @return integer
     */
    public function getDownVote()
    {
        return $this->downVote;
    }

    /**
     * Downvote
     *
     * @return Fortune
     */
    public function voteDown()
    {
        $this->downVote++;

        return $this;
    }

    /**
     * Get comments
     *
     * @return Comment
     */
    public function getComments()
    {
        return $this->comments;
    }

}

