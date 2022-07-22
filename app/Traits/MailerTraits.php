<?php
namespace App\Traits;
use Mail;

trait MailerTraits
{
    public $fromemail = "inddianst599@gmail.com";
    public $fromname = "Inddianstore Support";
    public $ccmail = "info@inddianstore.com";
    
    public function forgetPassword($user){
        try{
            $sentto = [
                'fromemail' => $this->fromemail,
                'fromname' => $this->fromname,
                'email' => $user['email'],
                'name' => $user['name'],
            ]; 
            Mail::send('mail.forgotpassword',['data' => $user], function($message) use($sentto)
            {
                $message->from($sentto['fromemail'], $sentto['fromname']);
                $message->to($sentto['email'], $sentto['name']);
                $message->subject('Forget Password Request');
            });
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    public function recentOrdermail($order){
        try{
            $sentto = [
                'fromemail' => $this->fromemail,
                'fromname' => $this->fromname,
                'email' => $order->delivery->email,
                'name' => $order->delivery->name,
            ]; 
            Mail::send('mail.order-placed',['order' => $order], function($message) use($sentto)
            {
                $message->from($sentto['fromemail'], $sentto['fromname']);
                $message->to($sentto['email'], $sentto['name']);
                $message->subject('Inddianstore Place Order');
            });
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}