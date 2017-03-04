<?php

namespace NotificationChannel\SmscAruba\Tests;

use Mockery as M;
use Illuminate\Notifications\Notification;
use NotificationChannels\SmscAruba\SmscArubaApi;
use NotificationChannels\SmscAruba\SmscArubaChannel;
use NotificationChannels\SmscAruba\SmscArubaMessage;
use NotificationChannels\SmscAruba\Exceptions\CouldNotSendNotification;

class SmscArubaChannelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SmscArubaApi
     */
    private $smsc;

    /**
     * @var SmscArubaMessage
     */
    private $message;

    /**
     * @var SmscArubaChannel
     */
    private $channel;

    public function setUp()
    {
        parent::setUp();

        $this->smsc = M::mock(SmscArubaApi::class, ['test', 'test', 'John_Doe']);
        $this->channel = new SmscArubaChannel($this->smsc);
        $this->message = M::mock(SmscArubaMessage::class);
    }

    public function tearDown()
    {
        M::close();

        parent::tearDown();
    }

    /** @test */
    public function it_can_send_a_notification()
    {
        $this->smsc->shouldReceive('send')->once()
            ->with(
                [
                    'phones'  => '+1234567890',
                    'mes'     => 'hello',
                    'sender'  => 'John_Doe',
                ]
            );

        $this->channel->send(new TestNotifiable(), new TestNotification());
    }

    /** @test */
    public function it_does_not_send_a_message_when_to_missed()
    {
        $this->expectException(CouldNotSendNotification::class);

        $this->channel->send(
            new TestNotifiableWithoutRouteNotificationForSmscaruba(), new TestNotification()
        );
    }
}

class TestNotifiable
{
    public function routeNotificationFor()
    {
        return '+1234567890';
    }
}

class TestNotifiableWithoutRouteNotificationForSmscaruba extends TestNotifiable
{
    public function routeNotificationFor()
    {
        return false;
    }
}

class TestNotification extends Notification
{
    public function toSmscAruba()
    {
        return SmscArubaMessage::create('hello')->from('John_Doe');
    }
}
