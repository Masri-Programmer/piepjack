<?php

namespace App\Jobs;

use App\Mail\OrderConfirmation;
use App\Mail\ReturningItems;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;


class SendEmailJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $orderData;
    public $returnData;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\User  $user
     * @param  mixed  $orderData
     * @param  mixed  $returnData
     * @return void
     */
    public function __construct(User $user, $orderData = null, $returnData = null)
    {
        $this->user = $user;
        $this->orderData = $orderData;
        $this->returnData = $returnData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer)
    {
        if ($this->orderData) {
            $mailer->to($this->user->email)->send(new OrderConfirmation($this->orderData));
        }

        if ($this->returnData) {
            $mailer->to($this->user->email)->send(new ReturningItems($this->returnData));
        }
    }
}
