<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Donation;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $donations = Auth::user()->donations()->orderBy('id','desc')->get();
        //$donations = Donation::where('user_id',Auth::user()->id)->get();
        //return json_encode($donations);
        return view('home',compact('donations'));
    }

    public function donate()
    {

        if (!request()->isMethod('post')){
            return view('donate');
        }

        request()->validate([
            'type' => ['required', 'string'],
            'amount' => ['required', 'integer'],
        ]);

        $type = request()->type;
        $amount = request()->amount;
        request()->session()->put('amount', $amount);

        $donations = Donation::distinct()->pluck('amount');
        //return $donations;

        if ($type == "m" && in_array((int) $amount , json_decode($donations))) {

            request()->session()->flash('error', 'You have already subscribed to pay '.$amount);
            return redirect(route('donate'));
        }

        $postdata =  array('email' => Auth::user()->email,"reference" => random_int(10000, 99999999), "callback_url" => route('verify'));

        if ($type == 'm') {
            request()->session()->put('cat', 'Give Monthly');

            switch ($amount) {
                case 2000:
                    $postdata['plan'] = env('PAYSTACK_2k');
                    break;
                case 3000:
                    $postdata['plan'] = env('PAYSTACK_3k');
                    break;
                case 5000:
                    $postdata['plan'] = env('PAYSTACK_5k');
                    break;
                case 10000:
                    $postdata['plan'] = env('PAYSTACK_10k');
                    break;
                default:
                    // code...
                    break;
            }
        }

        if ($type == 'o') {
            request()->session()->put('cat', 'Give Once');

            $postdata['amount'] = (int) $amount."00";
        }

        $link = "https://api.paystack.co/transaction/initialize";

        $result = json_decode($this->curl($link,$postdata),true);

        if ($result) {
          return redirect($result['data']['authorization_url']);
        }
    } 
    

    public function verify()
    {
        if (!request()->reference) {
            request()->session()->flash('error', 'Reference is not defined');
            return redirect(route('donate'));
        }

        if (!request()->session()->has('cat') || !request()->session()->has('amount')) {
            request()->session()->flash('error', 'Please to donate again, an error occured');
            return redirect(route('donate'));
        }

        $reference = request()->reference;
        $amount = request()->session()->get('amount');
        $cat = request()->session()->get('cat');
        //The parameter after verify/ is the transaction reference to be verified
        $link = 'https://api.paystack.co/transaction/verify/'.$reference;

        $request =  $this->curl($link);

        if ($request) {/*
            echo $request;
            exit();*/

            $result = json_decode($request, true);

            if($result){
              if($result['data']){
                //something came in
                if($result['data']['status'] == 'success'){

                  $check = Donation::where('ref', $reference)->first();
                  
                  
                  if ($check) {

                    request()->session()->flash('error', 'Payment paid already for reference '.$reference);
                    return redirect(route('donate'));
                  }else{

                    $donation = Donation::create([
                        'cat' => $cat,
                        'amount' => $amount,
                        'user_id' => Auth::user()->id,
                        'auth_code' => $result['data']['authorization']['authorization_code'],
                        'ref' => $reference,
                    ]);

                  
                    //request()->session()->flash('success', 'Donation successful');
                    request()->session()->flash('verified', 'Donation successful');
                    return redirect(route('success'));
                }

                }else{
                    request()->session()->flash('error', "Transaction was not successful: Last gateway response was: ".$result['data']['gateway_response']);
                    return redirect(route('donate'));
                  
                }
              }else{
                request()->session()->flash('error', $result['message']);
                return redirect(route('donate'));
              }

            }else{
                request()->session()->flash('error', "Something went wrong while trying to convert the request variable to json.");
                return redirect(route('donate'));
              
            }
          }else{
            request()->session()->flash('error', "Something went wrong while executing curl.");
            return redirect(route('donate'));
          }
        
    }


    public function success(){
        if (!request()->session()->has('verified')) {
            return redirect(route('donate'));
        }

        //request()->session()->forget('verified')
        return view('success');
    }

    public function curl($link, $postdata=null){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$link);

        if ($postdata) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($postdata));  //Post Fields
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
          'Authorization: Bearer '.env('PAYSTACK_API'),
          'Content-Type: application/json',

        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $request = curl_exec ($ch);

        curl_close ($ch);


        return $request;
    }
}
