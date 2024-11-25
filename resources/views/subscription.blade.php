@extends('layout.app')
@section('content')
    <div class="container mt-5">
        <div class="row sub_row">
            @foreach ($plans as $plan)
                <div class="col-sm-4">
                    <div class="card sub_card">
                        <h2>{{ $plan->name }}</h2>
                        <h4>${{ $plan->amount }}</h4>
                        <button class="btn btn-primary btn_confirmation" data-id="{{ $plan->id }}" data-bs-toggle="modal" data-bs-target="#confirmationModal">Subscribed</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

   

    <!-- Modal -->
    <div class="modal fade" id="confirmationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="sub_modal_title">Modal title</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <dv class="confirmation-data">
                <i class="fa fa-spinner fa-spin" style="font-size:48px"></i>
            </dv>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Continue</button>
        </div>
        </div>
    </div>
    </div>
@endsection

@section('customjs')
<script>
    $(document).ready(function(){
       $(".btn_confirmation").click(function(){

        $(".confirmation-data").html('<i class="fa fa-spinner fa-spin" style="font-size:48px"></i>');
        planId = $(this).data('id');
        console.log(planId);
        $("#sub_modal_title").text('...');
        $.ajax({
            type: 'post',
            url: "{{ route('getPlanDetail') }}",
            data: { id: planId},
            success: function(response){
                if(response.success == true){
                    planData = response.data
                    var html = '';
                    $("#sub_modal_title").text(planData.name+ " Subscription Plan ($"+planData.amount+")");
                    html = `<p>${response.msg}</p>`;
                    $(".confirmation-data").html(html);

                }
               

            }
        })
       })
    })
</script>
@endsection
   
