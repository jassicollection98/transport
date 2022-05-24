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

    /*
     * toggle button css
     */
    /* Basic Rules */
    .switch input { 
        display:none;
    }
    .switch {
        display:inline-block;
        width:60px;
        height:30px;
        margin:8px;
        transform:translateY(50%);
        position:relative;
    }
    /* Style Wired */
    .slider {
        position:absolute;
        top:0;
        bottom:0;
        left:0;
        right:0;
        border-radius:30px;
        box-shadow:0 0 0 2px #777, 0 0 4px #777;
        cursor:pointer;
        border:4px solid transparent;
        overflow:hidden;
        transition:.4s;
    }
    .slider:before {
        position:absolute;
        content:"";
        width:100%;
        height:100%;
        background:#777;
        border-radius:30px;
        transform:translateX(-30px);
        transition:.4s;
    }

    input:checked + .slider:before {
        transform:translateX(30px);
        background:limeGreen;
    }
    input:checked + .slider {
        box-shadow:0 0 0 2px limeGreen,0 0 2px limeGreen;
    }

    .switch.flat .slider {
    box-shadow:none;
    }
    .switch.flat .slider:before {
    background:#FFF;
    }
    .switch.flat input:checked + .slider:before {
    background:white;
    }
    .switch.flat input:checked + .slider {
    background:limeGreen;
    }
    .deliver-section{
        float:right;
    }
    .button-18 {
        width:60%;
        align-items: center;
        background-color: #0A66C2;
        border: 0;
        border-radius: 100px;
        box-sizing: border-box;
        color: #ffffff;
        cursor: pointer;
        display: inline-flex;
        font-family: -apple-system, system-ui, system-ui, "Segoe UI", Roboto, "Helvetica Neue", "Fira Sans", Ubuntu, Oxygen, "Oxygen Sans", Cantarell, "Droid Sans", "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Lucida Grande", Helvetica, Arial, sans-serif;
        font-size: 16px;
        font-weight: 600;
        justify-content: center;
        line-height: 20px;
        max-width: 480px;
        min-height: 40px;
        min-width: 0px;
        overflow: hidden;
        padding: 0px;
        padding-left: 20px;
        padding-right: 20px;
        text-align: center;
        touch-action: manipulation;
        transition: background-color 0.167s cubic-bezier(0.4, 0, 0.2, 1) 0s, box-shadow 0.167s cubic-bezier(0.4, 0, 0.2, 1) 0s, color 0.167s cubic-bezier(0.4, 0, 0.2, 1) 0s;
        user-select: none;
        -webkit-user-select: none;
        vertical-align: middle;
        }

        .button-18:hover,
        .button-18:focus { 
        background-color: #16437E;
        color: #ffffff;
        }

        .button-18:active {
        background: #09223b;
        color: rgb(255, 255, 255, .7);
        }

        .button-18:disabled { 
        cursor: not-allowed;
        background: rgba(0, 0, 0, .08);
        color: rgba(0, 0, 0, .3);
        }

        .hide-div{
            display:none;
        }
        .select2-container .select2-selection--single{
            height:34px;
        }
</style>
<section>
<?php 
    //Get first packet
    $single_packet = '';
    if(!empty($package->packets)){
        $single_packet = $package->packets[0];
    }
?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default" style = "margin:15px">
            <!-- show flash messages -->
            @if(session()->has('message.level'))
                <div class="alert alert-{{ session('message.level') }}"> 
                {!! session('message.content') !!}
                </div>
            @endif
            <form class="form-horizontal" id = "package-form" role="form" method="POST" action="{{ route('packages.update',base64_encode($package->id)) }}" enctype="multipart/form-data">
                <div class="panel-heading">
                    <span style = "font-size:30px; font-weight:bold;">Edit Package</span>
                    <div class = "deliver-section" style = "font-size:24px;">
                        Delivered 
                        <label class="switch">
                            <input type="checkbox" name = "delivered_status" @if(strtolower($package->delivery_status) == "on") checked @endif>
                            <span class="slider"></span>
                        </label> 
                    </div>
                </div>

                <div class="panel-body">
                        {{ csrf_field() }}
                        <div class = "row">
                            <div class="form-group col-md-6">
                                <label for="unique_code">Unique Code:</label>
                                <input type="text" name = "unique_code" class="form-control" value = "{{$package->unique_code}}" id="unique_code">
                                @if ($errors->has('unique_code'))
                                    <span class="error">
                                        <strong>{{ $errors->first('unique_code') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-md-6">
                                <label for="bill_date">Date:</label>
                                <input type="text" name = "bill_date" value = "{{$package->bill_date}}" class="form-control" id="bill_date">
                                @if ($errors->has('bill_date'))
                                    <span class="error">
                                        <strong>{{ $errors->first('bill_date') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class = "row">
                            <div class="form-group col-md-6">
                                <label for="from_city">From City:</label><span data-toggle="modal" data-target="#cityModal" style = "float:right"><i class = "fa fa-plus"></i></span>
                                <select class="form-control cities-dropdown" name = "from_city" aria-label="Default select example">
                                    <option value = ""></option>
                                    @foreach($cities as $key => $city)
                                        <option value = "{{$city->name}}" @if($package->from_city == $city->name) selected @endif>{{$city->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('from_city'))
                                    <span class="error">
                                        <strong>{{ $errors->first('from_city') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-md-6">
                                <label for="to_city">To City:</label>
                                <select class="form-control cities-dropdown" name = "to_city" aria-label="Default select example">
                                    <option value = ""></option>
                                    @foreach($cities as $key => $city)
                                        <option value = "{{$city->name}}" @if($package->to_city == $city->name) selected @endif >{{$city->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('to_city'))
                                    <span class="error">
                                        <strong>{{ $errors->first('to_city') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class = "row">
                            <div class="form-group col-md-12">
                                <label for="transport_company">Transport Company (GST):</label>
                                <input type="text" name = "transport_company" class="form-control" id="transport_company" value = "{{$package->transport_company_name}}">
                                @if ($errors->has('transport_company'))
                                    <span class="error">
                                        <strong>{{ $errors->first('transport_company') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class = "row" style = "margin-top:20px">
                            <div class="form-group col-md-12">
                                <p class="bg-info" style = "padding:10px;color:darkblue;font-weight:bold;font-size:16px">Consignee Info</p>
                            </div>
                        </div>

                        <div class = "row">
                            <div class="form-group col-md-4">
                                <label for="consignee_name">Name:</label>
                                <input type="text" name = "consignee_name" class="form-control" value = "{{$package->consignee_name}}" id="consignee_name">
                                @if ($errors->has('consignee_name'))
                                    <span class="error">
                                        <strong>{{ $errors->first('consignee_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-md-4">
                                <label for="consignee_mobile">Mobile:</label>
                                <input type="text" name = "consignee_mobile" value = "{{$package->consignee_mobile}}" class="form-control" id="consignee_mobile">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="consignee_gst_no">GST NO:</label>
                                <input type="text" name = "consignee_gst_no" class="form-control" value = "{{$package->consignee_gst}}" id="consignee_gst_no">
                                @if ($errors->has('consignee_gst_no'))
                                    <span class="error">
                                        <strong>{{ $errors->first('consignee_gst_no') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class = "row" style = "margin-top:20px">
                            <div class="form-group col-md-12">
                                <p class="bg-info" style = "padding:10px;color:darkblue;font-weight:bold;font-size:16px">Consigner Info</p>
                            </div>
                        </div>

                        <div class = "row">
                            <div class="form-group col-md-4">
                                <label for="consigner_name">Name:</label>
                                <input type="text" name = "consigner_name" value = "{{$package->consigner_name}}" class="form-control" id="consigner_name">
                                @if ($errors->has('consigner_name'))
                                    <span class="error">
                                        <strong>{{ $errors->first('consigner_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-md-4">
                                <label for="consigner_mobile">Mobile:</label>
                                <input type="text" name = "consigner_mobile" value = "{{$package->consigner_mobile}}" class="form-control" id="consigner_mobile">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="consigner_gst_no">GST NO:</label>
                                <input type="text" name = "consigner_gst_no" value = "{{$package->consigner_gst_no}}" class="form-control" id="consigner_gst_no">
                                @if ($errors->has('consigner_gst_no'))
                                    <span class="error">
                                        <strong>{{ $errors->first('consigner_gst_no') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class = "row" style = "margin-top:20px">
                            <div class="form-group col-md-12">
                                <p class="bg-info" style = "padding:10px;color:darkblue;font-weight:bold;font-size:16px">Add packets info <span style = "display:none;" id = "add_new_packet_row" style = "float:right;"><i class = "fa fa-plus"></i></span></p>
                            </div>
                        </div>

                        <div class = "row" id = "packets-group">
                            <div class = "col-md-12" id = "packets-group-col">
                                <div class = "row packets-row">
                                    <div class="form-group col-md-2">
                                        <input type="text" name = "package_no_of_packets[]" class="form-control no_of_packets" value = "@if(!empty($single_packet)) {{$single_packet->no_of_packets}} @endif" placeholder = "No of packets">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input type="text" name = "package_description[]" class="form-control packets_description" placeholder = "Description" value = "@if(!empty($single_packet)) {{$single_packet->description}} @endif">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="text" name = "package_weight[]" class="form-control packets_weight" placeholder = "Weight" value = "@if(!empty($single_packet)) {{$single_packet->weight}} @endif">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="text" name = "package_freight[]" class="form-control packets_freight" placeholder = "Freight" value = "@if(!empty($single_packet)) {{$single_packet->freight}} @endif">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <div class="form-check" style = "margin: 5px 0;">
                                            <!--label for="payment_status_overall">Payment status:</label><br-->
                                            <input type="radio" @if(!empty($single_packet)) @if($single_packet->payment_status == 1) checked @endif @else checked @endif class="form-check-input" id="payment_status0" name="payment_status" value="1">
                                            <label class="form-check-label" for="payment_status0">Paid</label>
                                            <input type="radio" @if(!empty($single_packet) && ($single_packet->payment_status == 0)) checked @endif style = "margin-left:20px" class="form-check-input" id="payment_status1" name="payment_status" value="0">
                                            <label class="form-check-label" for="payment_status1">Not paid</label>
                                        </div>
                                    </div>
                                </div>  
                            </div>  
                        </div> 

                        <div class = "row" style = "margin-top:20px">
                            <div class="form-group col-md-12">
                                <p class="bg-info" style = "padding:10px;color:darkblue;font-weight:bold;font-size:16px">Additional Info</p>
                            </div>
                        </div>

                        <div class = "row">
                            <div class="form-group col-md-4">
                                <label for="received_by">Received by:</label>
                                <input type="text" name = "received_by" value = "@if(!empty($package)) {{$package->received_by}} @endif" class="form-control" id="received_by">
                                @if ($errors->has('received_by'))
                                    <span class="error">
                                        <strong>{{ $errors->first('received_by') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-md-4">
                                <label for="delivered_by">Delivered by:</label>
                                <input type="text" name = "delivered_by" value = "@if(!empty($package)) {{$package->delivered_by}} @endif" class="form-control" id="delivered_by">
                                @if ($errors->has('delivered_by'))
                                    <span class="error">
                                        <strong>{{ $errors->first('delivered_by') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-md-4">
                                <label for="delivered_date">Delivered date:</label>
                                <input type="text" name = "delivered_date" class="form-control" value = "@if(!empty($package)) {{$package->delivered_date}} @endif" id="delivered_date">
                                @if ($errors->has('delivered_date'))
                                    <span class="error">
                                        <strong>{{ $errors->first('delivered_date') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class = "" style = "display:none">
                            <input type = "hidden" id = "paid_by_me" name = "paid_by_me" value = "0" />
                        </div>

                        <div class = "row">
                            <div class="form-group col-md-12">
                                <button type="button" id = "save_package" name = "save_package" class="btn btn-primary">Save package</button>
                            </div>
                        </div>
                    
                </div>
            </form>
            
            </div>
        </div>
    </div>
</section>

<!-- paid by me or not modal -->
<div class="modal fade" id="paidByMeModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Paid by me</h4>
        </div>
        <div class="modal-body">
            <div class = "row" style = "text-align:center;">
                <div class = "col-md-6">
                    <button class="button-18 submit-packet" data-val = "1" role="button">Yes</button>
                </div>
                <div class = "col-md-6">
                    <button class="button-18 submit-packet" data-val = "0" role="button">No</button>
                </div>
            </div>
        </div>
        <div class="modal-footer hide-div">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        </div>
        
    </div>
</div>

<div class="modal fade" id="cityModal" tabindex="-1" role="dialog" aria-labelledby="cityModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Add new city</h4>
      </div>
      <div class="modal-body">
            <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
            
                <div class = "row" style = "display:none;">
                    <div class="form-group col-md-12">
                        <label for="cityModal_country">Country:</label>
                        <select class="form-control" id = "cityModal_country" aria-label="Default select example">
                            <option value = "" selected>Select Country</option>
                            @foreach($countries as $key => $country)
                                <option value = "{{$country->id}}">{{$country->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class = "row">
                    <div class="form-group col-md-12">
                        <label for="cityModal_state" style = "width:100%">State:</label>
                        <select class="form-control" id = "cityModal_state">
                            <option value = "" selected>Select state</option>
                        </select>
                    </div>
                </div>

                <div class = "row">
                    <div class="form-group col-md-12">
                        <label for="cityModal_city">City:</label>
                        <input type="text" class="form-control" id="cityModal_city">
                    </div>
                </div>

            </form>
      </div>
      <div class="modal-footer">
        <button type="button" id = "add_new_city" class="btn btn-primary">Add City</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
<script>
    
    $(document).on("click","#save_package",function() {
        let val = $('input[name="payment_status"]:checked').val();
        if(val != "1"){
            $('#paidByMeModal').modal('show');
        }else{
            $('#package-form').submit();
        }
    });

    $(document).on("click",".submit-packet",function() {
        let paid_by_me = $(this).data('val');
        $('#paid_by_me').val(paid_by_me);
        $('#paidByMeModal').modal('hide');
        $('#package-form').submit();  
    });

     /*
     * Validation form
     */
    $('#package-form').on('submit', function() {
        let flag = 1;
        let no_of_packets = '';
        let weight = '';
        let description = '';
        let freight = '';
        $("#packets-group-col .packets-row").each(function() {
            no_of_packets = $(this).find("input[name='package_no_of_packets[]']").val();
            weight = $(this).find("input[name='package_weight[]']").val(); 
            description = $(this).find("input[name='package_description[]']").val();
            freight = $(this).find("input[name='package_freight[]']").val();
            if(no_of_packets == "" || weight == "" || description == "" || freight == ""){
                flag = 0;   
            }
        });

        if(flag == 0){
            return false;
        }else{

            return true;
        }
    });
    
    $(document).on("change","#cityModal_country",function() {
        let token = "{{csrf_token()}}";
        let country_id = $(this).val();
        if(country_id == ""){
            swal('','Country field is required','error');    
            return false;
        }

        $.ajax({
            url:"{{route('get.states')}}",
            method:"POST",
            data:{"country_id":country_id,"_token":token},
            success:function(response)
            {
               if(response.status == true)
               {
                    jQuery('#cityModal_state').empty();
                    let html = '<option value = "" selected>Select State</option>';
                    $.each(response.states, function (key, val) {
                        html += `<option value = "${val.id}">${val.name}</option>`;
                    });
                    jQuery('#cityModal_state').html(html);
               }
               
            },
            error:function(response)
            {

            }
         });
    });

    /*
     * add new city
     */
    $(document).on("click","#add_new_city",function() {
        let token = "{{csrf_token()}}";
        let country_id = $('#cityModal_country').val();
        let state_id = $('#cityModal_state').val();
        let city_name = $('#cityModal_city').val();
        if(country_id == ""){
            swal('','Please select the country','error');    
            return false;
        }

        if(state_id == ""){
            swal('','Please select the state','error');    
            return false;
        }

        if(city_name == ""){
            swal('','City name is required','error');    
            return false;
        }

        $.ajax({
            url:"{{route('add.city')}}",
            method:"POST",
            data:{"country_id":country_id,"state_id":state_id,"city_name":city_name,"_token":token},
            success:function(response)
            {
               if(response.status == true)
               {
                    swal('',response.message,'success');  
                    $('#cityModal_city').val('');

                    //append new cities to dropdown
                    jQuery('.cities-dropdown').empty();
                    let html = '<option value = "" selected>Select City</option>';
                    $.each(response.cities, function (key, val) {
                        html += `<option value = "${val.id}">${val.name}</option>`;
                    });
                    jQuery('.cities-dropdown').html(html);
               }else{
                    swal('',response.message,'error');  
               }
            },
            error:function(response)
            {
                swal('','Something went wrong while','error');  
            }
         });
    });

    /*
     * Transport companies auto suggestion
     */
    let tags = [<?php echo '"' . implode ('","', $companies) . '"'; ?>];      
    $( "#transport_company" ).autocomplete({
        source:tags 
    });

    /*
     * Add packets row
     */
    $(document).on("click","#add_new_packet_row",function() {
        let packets_row =  `<div class = "row packets-row">
                                <div class="form-group col-md-2">
                                    <input type="text" name = "package_no_of_packets[]" class="form-control no_of_packets" placeholder = "No of packets">
                                </div>
                                <div class="form-group col-md-4">
                                    <input type="text" name = "package_description[]" class="form-control packets_description" placeholder = "Description">
                                </div>
                                <div class="form-group col-md-2">
                                    <input type="text" name = "package_weight[]" class="form-control packets_weight" placeholder = "Weight">
                                </div>
                                <div class="form-group col-md-2">
                                    <input type="text" name = "package_freight[]" class="form-control packets_freight" placeholder = "Freight">
                                </div>
                                <div class="form-group col-md-2 del-view">
                                    <input type="text" name = "package_topay[]" class="form-control packets_topay" placeholder = "To Pay">
                                    <button type="button" class="btn btn-sm btn-danger del-packet-row"><i class = "fa fa-trash"></i></button>
                                </div>
                            </div>`; 
        jQuery('#packets-group-col').append(packets_row);
    });

    /*
     * Delete packet row
     */
    $(document).on("click",".del-packet-row",function() {
        $(this).parent().parent().remove();
    });

    $(document).ready(function(){
        $(".cities-dropdown").select2();
        // set india to default
        $("#cityModal_country").val('101').change();
    });
</script>
@endsection
