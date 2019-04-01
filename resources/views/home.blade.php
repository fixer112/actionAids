@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                    <table class="nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <td>ID</td>
                            <td>DONATE CATEGORY</td>
                            <td>DATE</td>
                            <td>AMOUNT</td>
                        </tr>
                    </thead>
                        <tbody>
                            @foreach($donations as $key => $donation)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td>{{$donation->cat}}</td>
                                <td>{{$donation->created_at}}</td>
                                <td>{{$donation->amount}}</td>
                            </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                    </div>
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
