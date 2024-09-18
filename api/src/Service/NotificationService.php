<?php

declare(strict_types=1);

namespace Camagru\Service;

use Camagru\Model\Comment;
use Camagru\Model\Notification;
use Camagru\Model\User;

class NotificationService
{
    public function send(Notification $notification): void
    {
        if (
            !mail(
                $notification->getDestination(),
                $notification->getSubject(),
                $notification->getMessage(),
                $notification->getHeaders(),
            )
        ) {
          throw new \RuntimeException('Unable to send notification');
        }
    }

    public static function makeNotification(
        User $user,
        string $subject,
        string $message,
        array $headers = [],
    ): Notification {
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setDestination($user->getEmail());
        $notification->setSubject($subject);
        $notification->setMessage($message);
        $notification->setHeaders($headers);
        $notification->save();

        return  $notification;
    }

    public function createConfirmNotification(User $user, string $confirmLink): Notification
    {
        $subject = $user->getUsername() .', confirm your email address please.';
        $message = <<<Message
        Hello {$user->getUsername()},
            
        Use the following link to confirm your email address: <a href="{$confirmLink}">{$confirmLink}</a> 
        Have a good day.
            
        Best wishes,
        Camagru Team 
        Message;
        $headers = [
            'From' => 'webmaster@example.com',
            'Reply-To' => 'webmaster@example.com',
            'X-Mailer' => 'PHP/' . phpversion()
        ];

        return self::makeNotification($user, $subject, $message, $headers);
    }

    public function createRecoveryNotification(User $user, string $recoveryLink): Notification
    {
        $subject = 'Password recovery.';
        $message = <<<Message
        Hello {$user->getUsername()},
            
        Use the following link to recovery your password: <a href="{$recoveryLink}">{$recoveryLink}</a>.
        If you didn’t expect this email, ignore it.
            
        Best wishes,
        Camagru Team 
        Message;
        $headers = [
            'From' => 'webmaster@example.com',
            'Reply-To' => 'webmaster@example.com',
            'X-Mailer' => 'PHP/' . phpversion()
        ];

        return self::makeNotification($user, $subject, $message, $headers);
    }

    public function createCommentNotification(User $user, Comment $comment): Notification
    {
        $subject = 'Comment notification.';
        $message = <<<Message
        Hello {$user->getUsername()},
            
        You have received a new comment from user {$comment->getUser()->getUsername()}.
        -----------------------------------------
        {$comment->getMessage()}
        -----------------------------------------
            
        Best wishes,
        Camagru Team 
        Message;
        $headers = [
            'From' => 'webmaster@example.com',
            'Reply-To' => 'webmaster@example.com',
            'X-Mailer' => 'PHP/' . phpversion()
        ];

        return self::makeNotification($user, $subject, $message, $headers);
    }
}