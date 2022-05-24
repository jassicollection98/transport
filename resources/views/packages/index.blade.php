@extends('employees-mgmt.base')
@section('action-content')
<style>
.edit-package{
    width:80%
}
</style>
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header" style = "display:none;">
    <div class="row">
        <div class="col-sm-12">
          
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
    <!-- show flash messages -->
    @if(session()->has('message.level'))
        <div class="alert alert-{{ session('message.level') }}"> 
        {!! session('message.content') !!}
        </div>
    @endif

    @if(!empty($packages))
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-sm-12">
          <h2>Filters</h2>
          <form method="POST" action="{{ route('packages.index') }}">
          {{ csrf_field() }}
            <div class = "row">
              <div class="col-sm-3">
                <div class="form-group">
                  <label for="from_date">From Date:</label>
                  <input type ="text" class="form-control datepicker-old" id = "from_date" name = "from_date" />
                </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group">
                  <label for="to_date">To Date:</label><br>
                  <input type="text" class="form-control datepicker-old" id="to_date" name = "to_date">
                </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group">
                  <label for="unique_code">Search Keyword:</label>
                  <input type="text" class="form-control" id="unique_code" name = "unique_code">
                </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group">
                  <label for="payment_status">Payment Status:</label><br>
                  <select name = "payment_status" class="form-control">
                    <option value = "">Select</option>
                    <option value = "1">Paid</option>
                    <option value = "0">Not Paid</option>
                  </select>
                </div>
              </div>
            </div>

            <div class = "row" style = "margin-top:10px;">
              <div class="col-sm-3">
                <div class="form-group">
                  <label for="from_city">From City:</label>
                  <input type ="text" class="form-control" id = "from_city" name = "from_city" />
                </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group">
                  <label for="to_city">To City:</label><br>
                  <input type="text" class="form-control" id="to_city" name = "to_city">
                </div>
              </div>
            </div>

            <div class = "row">
              <div class = "col-md-12">
                <button type = "submit" name = "filter_packages" class = "btn btn-primary" style = "margin:20px 0;">Apply Filter</button>
                <a href = "{{route('packages.index')}}"><button type = "button" class = "btn btn-primary"><i class="fa fa-refresh" aria-hidden="true"></i></button></a>
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <table id="example2" class="table table-bordered table-success table-striped table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th>Action</th>
                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Picture: activate to sort column descending" aria-sort="ascending">Unique Code</th>
                <th  class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Consigner Name</th>
                <th  class="sorting hidden-xs" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Address: activate to sort column ascending">Consigner GST</th>
                <th  class="sorting hidden-xs" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending">Payment Status</th>
                <th  class="sorting hidden-xs" tabindex="0" aria-controls="example2" rowspan="1" colspan="1">Bill Date</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($packages as $package)
                <tr role="row" class="odd">
                  <td><a href = "{{ route('packages.edit',base64_encode($package->id)) }}"><button type = "button" class = "btn btn-success" style = "margin-right:5px;"><i class = "fa fa-edit"></i></button></a><button type = "button" class = "btn btn-danger del-package" data-val = "{{base64_encode($package->id)}}"><i class = "fa fa-trash"></i></button></td>
                  <td class="sorting_1">{{ $package->unique_code }}</td>
                  <td class="hidden-xs">{{ $package->consigner_name }}</td>
                  <td class="hidden-xs">{{ $package->consigner_gst_no }}</td>
                  <td class="hidden-xs">@if($package->payment_status == "1") Paid @else Not Paid @endif</td>
                  <td class="hidden-xs">{{ date('Y-m-d',strtotime($package->bill_date)) }}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to {{count($packages)}} of {{count($packages)}} entries</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $packages->links() }}
          </div>
        </div>
      </div>
    </div>
    @endif
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>
@endsection

@section('scripts')
  <script>
    $(document).on("click",".del-package",function() {
      let id = $(this).data('val');
      swal({
          title: 'Are you sure?',
          text: 'This package will be permanantly deleted!',
          icon: 'warning',
          buttons: ["Cancel", "Yes!"],
      }).then(function(value) {
          if (value) {
            let token = "{{csrf_token()}}";
            $.ajax({
                url:"{{route('packages.delete')}}",
                method:"POST",
                data:{"package_id":id,"_token":token},
                success:function(response)
                {
                  if(response.status == true)
                  {
                    swal('','Package deleted successfully.','success'); 
                    location.reload();
                  }else{
                    swal('','Something went wrong while deleting the package.','error');  
                  }
                },
                error:function(response)
                {
                  swal('','Something went wrong.','error');  
                }
            });
          }
      });
    });
  </script>
@endsection