<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\FcmMessage;
use App\Models\Document;

class DocumentReceivedNotification extends Notification
{
    use Queueable;

    protected $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function via($notifiable)
    {
        return ['fcm'];
    }

    public function toFcm($notifiable)
    {
        return (new FcmMessage)
            ->content([
                'title' => 'Dokumen Baru',
                'body' => 'Dokumen "' . $this->document->name . '" siap ditandatangani.',
            ])
            ->data([
                'document_id' => $this->document->id,
            ]);
    }
}
