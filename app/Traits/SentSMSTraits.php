<?php
namespace App\Traits;

trait SentSMSTraits
{
    public function otpmaster($msg=null,$mobile=null)
    {
        $apikey = "6IwZdXHBhUiacbceBxHwuw";
        $apisender = "RAAMST";
        $ms = rawurlencode($msg);                   		
 
        $url = 'https://www.smsgatewayhub.com/api/mt/SendSMS?APIKey='.$apikey.'&senderid='.$apisender.'&channel=2&DCS=0&flashsms=0&number='.$mobile.'&text='.$ms.'&route=0';
                     
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,"");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,2);
        
        $response = curl_exec($ch);
        $err = curl_error($ch);

        curl_close($ch);
            
        if ($err) {
            $data = [
                'status'=>'false',
                'error'=>'true',
                'response'=>$err
                ];
            return $data;    
        } else {
            $msg = json_decode($response, true);
            $data = [
                'status'=>'true',
                'error'=>'false',
                'response'=>$msg,
                ];
            return $data;
        } 
    }
    public function msg91otpmaster($status=null,$mobile=null,$otp=null)
    {
        $authkey = "200724AR8yxdF4IH5a9a6fe2";
        $sender = "RAAMST";
        //$authkey = "362939Ab3LkBTg60d03828P1";
        //$sender = "RMSETU";
        $otplength = "4";
        $otpexpiry = "5";
        

        if($status=='verifyotp'){
            $sentotpotp = "https://control.msg91.com/api/verifyRequestOTP.php?authkey=".$authkey."&mobile=".$mobile."&otp=".$otp."";
        }
        elseif($status=='sentotp'){
            $sentotpotp = "http://control.msg91.com/api/sendotp.php?otp_length=".$otplength."&authkey=".$authkey."&sender=".$sender."&mobile=".$mobile."&otp_expiry=".$otpexpiry."";
        }elseif($status=='voiceotp'){
            $sentotpotp = "http://control.msg91.com/api/retryotp.php?authkey=".$authkey."&mobile=".$mobile."&retrytype=voice";
        }else{}
                    
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $sentotpotp,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "",
              CURLOPT_SSL_VERIFYHOST => 0,
              CURLOPT_SSL_VERIFYPEER => 0,
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
            
        if ($err) {
            $data = [
                'status'=>'false',
                'error'=>'true',
                'response'=>$err
                ];
            return $data;    
        } else {
            $msg = json_decode($response, true);
            $data = [
                'status'=>'true',
                'error'=>'false',
                'response'=>$msg['message'],
                'type'=>$msg['type']
                ];
            return $data;
        } 
    }

}