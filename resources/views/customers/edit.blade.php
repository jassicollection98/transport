@extends('employees-mgmt.base')

@section('action-content')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" />
<style>
    .form-horizontal .form-group {
        margin-right: 0;
        margin-left: 0;
    }
    .error{
        color:red;
    }
    .del-view{
        display:flex;
    }
    .del-packet-row{
        margin-left:5px;
    }
</style>
<section>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default" style = "margin:15px">
            <!-- show flash messages -->
            @if(session()->has('message.level'))
                <div class="alert alert-{{ session('message.level') }}"> 
                {!! session('message.content') !!}
                </div>
            @endif
            <form class="form-horizontal" id = "customer-form" role="form" method="POST" action="{{ route('customer.update',base64_encode($customer->id)) }}" enctype="multipart/form-data">
                <div class="panel-heading">
                    <span style = "font-size:30px; font-weight:bold;">Edit Customer</span>
                </div>

                <div class="panel-body">
                        {{ csrf_field() }}
                        <div class = "row">
                            <div class="form-group col-md-6">
                                <label for="name">Name:</label>
                                <input type="text" name = "name" class="form-control" id="name" value = "{{$customer->name}}">
                                @if ($errors->has('name'))
                                    <span class="error">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group col-md-6">
                                <label for="mobile">Mobile:</label>
                                <input type="text" name = "mobile" class="form-control" id="mobile" value = "{{$customer->mobile}}">
                                @if ($errors->has('mobile'))
                                    <span class="error">
                                        <strong>{{ $errors->first('mobile') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class = "row">
                            <div class="form-group col-md-6">
                                <label for="gst">GST:</label>
                                <input type="text" name = "gst" class="form-control" id="gst" value = "{{$customer->gst}}">
                                @if ($errors->has('gst'))
                                    <span class="error">
                                        <strong>{{ $errors->first('gst') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class = "row">
                            <div class="form-group col-md-12">
                                <button type="submit" id = "save_customer" name = "save_customer" class="btn btn-primary">Update Customer</button>
                            </div>
                        </div>
                    
                </div>
            </form>
            
            </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
<script>

</script>
@endsection
