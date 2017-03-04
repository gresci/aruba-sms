<?php

namespace NotificationChannels\SmscAruba\Test;

use NotificationChannels\SmscAruba\SmscArubaMessage;

class SmscArubaMessageTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_can_accept_a_content_when_constructing_a_message()
    {
        $message = new SmscArubaMessage('hello');

        $this->assertEquals('hello', $message->content);
    }

    /** @test */
    public function it_can_accept_a_content_when_creating_a_message()
    {
        $message = SmscArubaMessage::create('hello');

        $this->assertEquals('hello', $message->content);
    }

    /** @test */
    public function it_can_set_the_content()
    {
        $message = (new SmscArubaMessage())->content('hello');

        $this->assertEquals('hello', $message->content);
    }

    /** @test */
    public function it_can_set_the_from()
    {
        $message = (new SmscArubaMessage())->from('John_Doe');

        $this->assertEquals('John_Doe', $message->from);
    }
}
