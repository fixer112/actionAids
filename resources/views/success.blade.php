@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Donation Successfull</div>

                <div class="alert alert-warning">
                    <p>Dear {{Auth::user()->firstname}}, your donation is SUCCESSFUL! </p>

                    <p>Please follow the instruction below to generate your receipt. </p>

                </div>

                <div class="mx-5">
                <p>
                    <strong>Step 1</strong> <br>
                    Log into your email and click on inbox. Click on the mail from act!onaid

                </p>

                <p>
                    <strong>Step 2</strong> <br>
                    Click on print receipt


                </p>

                @if(session('cat') == 'Give Monthly')
                <p>
                    <strong>Step 3</strong> <br>
                    To give next month, click on the link below print receipt.

                </p>
                @endif
                </div>
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
