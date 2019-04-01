@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Donate</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    <form action="{{ route('donate') }}" method="post">
                        @csrf
                    <div class="alert-danger response">
                        <p>Note: Give Monthly sets up for you ‘Regular Giving’: a recurring Monthly debit on your payment-card as sign-on to Action Aids’s Social Justice support to the Rights of : Women & safe city, child & Girl’s to Education. Psychosocial support to displaced victims of insurgences Relief in emergencies and Building resilience of rural communities</p>

                        <p>While: Give Once is a ‘One-off’ Donation</p>

                    </div>

                      <div class="form-group">
                          <label for="">Type <span class="text-danger">*</span></label>
                          <select class="form-control" name="type" required>
                            <option value="" selected>Choose</option>
                            <option value="m">Give Monthly</option>
                            <option value="o">Give Once</option>
                          </select>
                        </div>
                        
                    <div class="form-group">
                          <label for="">Amount <span class="text-danger">*</span></label>
                          <select class="form-control" name="amount" required>
                            <option value="" selected>Choose</option>
                            <option value="2000">NGN 2,000</option>
                            <option value="3000">NGN 3,000</option>
                            <option value="5000">NGN 5,000</option>
                            <option value="10000">NGN 10,000</option>
                          </select>
                        
                        </div>

                    
                       <button class="btn btn-primary" type="submit">Submit</button>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
