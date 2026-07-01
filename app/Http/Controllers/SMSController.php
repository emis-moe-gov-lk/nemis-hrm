<?php

namespace App\Http\Controllers;

use App\Http\Controllers\SoapController;

use Illuminate\Http\Request;

class SMSController extends Controller
{
    public function testForm()
    {
        return view('sms-test');
    }

    public function sendSmsTest(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
            'message' => 'required|string'
        ]);

        try {
            $mobileU = $request->mobile;
            if (substr($mobileU, 0, 1) == '0') {
                $mobileNo = '94' . substr($mobileU, 1);
            } else {
                $mobileNo = $mobileU;
            }

            $SoapController = new SoapController;
            $response = $SoapController->multilang_msg_Send($mobileNo, $request->message);

            return back()->with('success', 'SMS test executed. Response: ' . json_encode($response));
        } catch (\Exception $e) {
            return back()->with('error', 'Error sending SMS: ' . $e->getMessage());
        }
    }

    public function mobileSMSTest()
    {
        $message = 'Hi, this is a test sms gateway message. සිංහල ✔ தமிழ் ✔';

        //sent OTP
            $mobileU = '0761094038';
            //$mobileU = $mobile;
            if (substr($mobileU, 0, 1) == '0') {
                $mobileNo = '94' . substr($mobileU, 1); // Convert 076XXXXXXX to 9476XXXXXXX
            } else {
                $mobileNo = $mobileU; // Assume it's already in correct format
            }

            $SoapController = new SoapController;
            $SoapController->multilang_msg_Send($mobileNo,$message);
        //End sent OTP
    }
}
