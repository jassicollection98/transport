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

    @if(!empty($customers))
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-sm-12">
          <table id="example2" class="table table-bordered table-success table-striped table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th>Action</th>
                <th tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Picture: activate to sort column descending" aria-sort="ascending">Name</th>
                <th  class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Mobile</th>
                <th  class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">GST</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($customers as $customer)
                <tr role="row" class="odd">
                  <td><a href = "{{ route('customer.edit',base64_encode($customer->id)) }}"><button type = "button" class = "btn btn-success" style = "margin-right:5px;"><i class = "fa fa-edit"></i></button></a><button type = "button" class = "btn btn-danger del-user" data-val = "{{base64_encode($customer->id)}}"><i class = "fa fa-trash"></i></button></td>
                  <td class="sorting_1">{{ $customer->name }}</td>
                  <td class="hidden-xs">{{ $customer->mobile }}</td>
                  <td class="hidden-xs">{{ $customer->gst }}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to {{count($customers)}} of {{count($customers)}} entries</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $customers->links() }}
          </div>
        </div>
      </div>
    </div>
    @else
    <div class="alert alert-danger">
        <strong>Warning! </strong> No user added at the moment.
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
    $(document).on("click",".del-user",function() {
      let id = $(this).data('val');
      swal({
          title: 'Are you sure?',
          text: 'This user will be permanantly deleted!',
          icon: 'warning',
          buttons: ["Cancel", "Yes!"],
      }).then(function(value) {
          if (value) {
            let token = "{{csrf_token()}}";
            $.ajax({
                url:"{{route('customer.delete')}}",
                method:"POST",
                data:{"user_id":id,"_token":token},
                success:function(response)
                {
                  if(response.status == true)
                  {
                    swal('','User deleted successfully.','success'); 
                    location.reload();
                  }else{
                    swal('','Something went wrong while deleting the user.','error');  
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