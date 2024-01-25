<?php

namespace UnitTest;

use Doctrine\Common\Collections\ArrayCollection;
use ExampleClient\Client;
use ExampleClient\Comment;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client();
    }

    public function testGetComments()
    {
        $comments = $this->client->getComments();
        $this->assertIsArray($comments);
        $this->assertInstanceOf(Comment::class, $comments[0]);
    }

    public function testCreateComment()
    {
        $comment = new Comment(1,'Test name', 'Test text');

        $resultComment = $this->client->createComment('Test name', 'Test text');
        $this->assertInstanceOf(Comment::class, $resultComment);
        $this->assertEquals($comment->getName(), $resultComment->getName());
        $this->assertEquals($comment->getText(), $resultComment->getText());
    }

    public function testUpdateComment()
    {
        $comment = new Comment(2,'Test name1', 'Test text1');
        $resultComment = $this->client->updateComment(2, 'Test name1', 'Test text1');
        $this->assertInstanceOf(Comment::class, $resultComment);
        $this->assertEquals($comment->getName(), $resultComment->getName());
        $this->assertEquals($comment->getText(), $resultComment->getText());
    }
}