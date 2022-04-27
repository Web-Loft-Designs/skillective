<?php

namespace App\Notifications;

use App\Models\CustomNotification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Twilio\TwilioChannel;
use Twilio\Rest\Client;
use App\Channels\WhatsAppChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;
use Tylercd100\Placeholders\Facades\Placeholders;

abstract class AbstractCustomNotification extends Notification
{
    use Queueable;

    /**
     * @var \App\Models\CustomNotification
     */
	private $customNotification;
    protected $content = [];
    protected $vars = [];
    protected $methods;
    protected $notifiable;

    /**
     * Create a new notification instance.
     *
     */
    public function __construct()
    {
        Placeholders::setStyle("[[", "]]");

        $this->customNotification = $this->getCustomNotificationClass();

        $this->methods = $this->customNotification->methods->mapWithKeys(function ($item) {
            return [$item->method => $item];
        });

        $this->initVariables();

        if (isset($this->methods['mail'])) {
            $this->generateEmailContent();
        }
        if (isset($this->methods['sms'])) {
            $this->generateSMSContent();
        }
		if (isset($this->methods['whatsapp'])) {
			$this->generateWhatsAppContent();
		}
    }

    protected function generateEmailContent()
    {
        $mailContent    = Placeholders::parse($this->methods['mail']['content'], $this->getVars());
        $this->content['mail'] = $mailContent;
        $this->content['var'] = $this->getVars();
    }

    protected function generateSMSContent()
    {
        $smsContent    = Placeholders::parse($this->methods['sms']['content'], $this->getVars());
		$smsContent		= preg_replace( "/\r|\n/", "", strip_tags($smsContent));
        $this->content['sms'] = $smsContent;
    }

	protected function generateWhatsAppContent()
	{
		$whatsappContent    = Placeholders::parse($this->methods['whatsapp']['content'], $this->getVars());
		$whatsappContent	= preg_replace( "/\r|\n/", "", strip_tags($whatsappContent));
		$this->content['whatsapp'] = $whatsappContent;
	}

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $this->notifiable = $notifiable;

        if ($this->notifiable instanceof User && $this->notifiable->profile) {
            $deliveryChannels = collect($this->notifiable->profile->notification_methods)
                ->map(function ($item) {
                    if ($item == 'sms') {
                        return TwilioChannel::class;
                    } elseif ($item == 'email') {
                        return 'mail';
                    }  elseif ($item == 'whatsapp') {
						return WhatsAppChannel::class;
					}

                    return $item;
                })->toArray();

            return $deliveryChannels;
        }

        return $via = $this->methods->pluck('method')->map(function ($item) {
            if ($item == 'sms') {
                return TwilioChannel::class;
            }

            return $item;
        })->toArray();
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
		if ($this->customNotification==null && !isset($this->methods['mail'])) // for some reason saved queued job doesnot contain prepared array of methods
			$this->methods = $this->methods->mapWithKeys(function ($item) {
				return [$item->method => $item];
			});

        return (new MailMessage)
            ->subject($this->methods['mail']['data']['subject'])
            ->markdown('emails.abstract', [
                'view' => $this->content['mail'],
                'sender_first_name' => data_get($this->content['var'], 'sender_first_name'),
                'sender_last_name' => data_get($this->content['var'], 'sender_last_name'),
            ]);
    }

    public function toTwilio($notifiable)
    {
		if ($this->customNotification==null && !isset($this->methods['sms'])) // for some reason saved queued job doesnot contain prepared array of methods
			$this->methods = $this->methods->mapWithKeys(function ($item) {
				return [$item->method => $item];
			});
//		Log::info($this->content['sms']);
		if ($this->content['sms'])
			return (new TwilioSmsMessage())
				->content($this->content['sms']);
		else
			return false;
    }

	public function toWhatsApp($notifiable)
	{
		if ($this->customNotification==null && !isset($this->methods['whatsapp'])) // for some reason saved queued job doesnot contain prepared array of methods
			$this->methods = $this->methods->mapWithKeys(function ($item) {
				return [$item->method => $item];
			});

		$sid    = config( 'services.twilio.account_sid' );
		$token  = config( 'services.twilio.auth_token' );
		$twilio = new Client($sid, $token);
		if (isset($this->content['whatsapp'])) {
			$message = $twilio->messages
				->create( "whatsapp:" . $notifiable->profile()->value( 'mobile_phone' ), // to format +15005550006
					array(
						"from" => "whatsapp:" . config( 'services.whatsapp.from' ),
						"body" => $this->content['whatsapp'] // this should be a template
					)
				);
			//		Log::info('message', (array)$message);
			return $message;
		}else
			return false;
	}

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    protected function getVars()
    {
        if (method_exists($this, 'variables')) {
            return array_merge($this->vars, $this->variables());
        }

        return $this->vars;
    }

    private function initVariables()
    {
        $this->vars = [
            'app_name'     => config('app.name'),
            'app_url'      => config('app.url'),
			'login_url'      => route('frontend.login'),
            'current_year' => now()->format('Y'),
        ];
    }

    /**
     * @return \App\Models\CustomNotification
     */
    abstract protected function getCustomNotificationClass(): CustomNotification;
}
