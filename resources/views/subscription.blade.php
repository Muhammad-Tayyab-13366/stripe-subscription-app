@extends('layout.app')
@section('content')
    <div class="container mt-5">
        <div class="row sub_row">
            @php
                $current_plan = app('subscription_helper')->get_current_subscription();
            @endphp 
            @foreach ($plans as $plan)
                <div class="col-sm-4">
                    <div class="card sub_card">
                        <h2>{{ $plan->name }} @if($current_plan && $current_plan->subscription_plan_price_id == $plan->stripe_price_id) <span class="badge bg-success">Active</span> @endif</h2>
                        <h4>${{ $plan->amount }}</h4>
                        @if($current_plan && $current_plan->subscription_plan_price_id == $plan->stripe_price_id)
                            @if ($current_plan && $current_plan->plan_interval == 'lifetime') 
                            <button class="btn btn-primary">Subscribed</button>
                            @else 
                            <button class="btn btn-danger btn_subscription_cancel" data-id="{{ $plan->id }}" data-bs-toggle="modal" data-bs-target="#confirmationModal"> Cancel
                            @endif 
                        @else 
                            <button class="btn btn-primary btn_confirmation @if ($current_plan && $current_plan->plan_interval ==  'lifetime') btn_disabled @endif " data-id="{{ $plan->id }}" data-bs-toggle="modal" data-bs-target="#confirmationModal"> Subscribe
                        @endif
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

   

    <!-- Confirmation Modal -->
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
                <button type="button" id="btn_confirmation_continue" class="btn btn-primary">Continue</button>
            </div>
            </div>
        </div>
    </div>

       <!-- stripe card  Modal -->
    <div class="modal fade" id="stripCardModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="strpe_card_modal_ttitle">Buy Subscription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="planId" id="planId">
                <!-- stripe card element -->
                <div id="card_element"> </div>
                <!-- show card error -->
                <div id="card-error" style="color:red;"></div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button id="btn_buy_plan" type="button" class="btn btn-primary">Buy Plan</button>
            </div>
            </div>
        </div>
    </div>
@endsection

@section('customjs')
<script src="https://js.stripe.com/v3/"></script>
<script>
    $(document).ready(function(){
       $(".btn_confirmation").click(function(){

        $(".confirmation-data").html('<i class="fa fa-spinner fa-spin" style="font-size:48px"></i>');
        planId = $(this).data('id');
        $("#planId").val(planId);
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
       });

        $("#btn_confirmation_continue").click(function(){
            $("#confirmationModal").modal('hide');
            $("#stripCardModal").modal('show');
        });

        $(".btn_subscription_cancel").click(function(){
            obj = $(this);
            $(obj).html('Please wait <i class="fa fa-spinner fa-spin" style="font-size:16px"></i>');
            $(obj).attr('disabled', 'disabled');
            planId = $("#planId").val();
            $.ajax({
                type: 'post',
                url: "{{ route('cancelSubscription') }}",
                data: { 
                        'planId' : planId
                },
                success: function(response){
                    if(response.success == true){
                        console.log(response);
                        alert(response.msg )
                    window.location.reload();
                    }else {
                        alert('some thing went wrong');
                        $(obj).html('Cancel');
                        $(obj).removeAttr('disabled');
                    }
                    console.log(response);
                }
            });
        });
    });
    
    // stripe code started 
    // check stripe js is loaded
   
    if(window.Stripe){

        var stripePublicKey = "{{ config('services.stripe.public_key') }}";
        var stripe = Stripe(stripePublicKey);

        // create an instance of elements 
        var elements = stripe.elements();
        
        // create an instance of card 
        var card  = elements.create('card', {
            hidePostalCode : true
        });
        
        // add an instance of the card element into card_element div
        card.mount('#card_element');

        card.addEventListener('change', function(event){
           
            var displayError = document.getElementById('card-error');

            if(event.error){
                displayError.innerHTML = event.error.message;
            }else {
                displayError.innerHTML = '';
            }
        });

        // handle form submission and create token

        var submitButton = document.getElementById('btn_buy_plan');
        submitButton.addEventListener('click', function (){
            submitButton.innerHTML = 'Please wait <i class="fa fa-spinner fa-spin" style="font-size:16px"></i>';
            submitButton.setAttribute('disabled', 'disabled');
            stripe.createToken(card).then(function(result){
                if(result.error){
                    var errorElement = document.getElementById('card-error');
                    displayError.innerHTML = result.error.message;
                    submitButton.innerHTML = 'Buy Plan';
                    submitButton.removeAttribute('disabled');
                }else {
                    console.log(result);
                    createSubscription(result.token);
                }
            });
        });

    }

    function createSubscription(token){
        planId = $("#planId").val();
        $.ajax({
            type: 'post',
            url: "{{ route('createSubscription') }}",
            data: { 'data': token,
                    'planId' : planId
            },
            success: function(response){
                if(response.success == true){
                    console.log(response);
                   alert(response.msg )
                   window.location.reload();
                }else {
                    alert('some thing went wrong');
                    submitButton.innerHTML = 'Buy Plan';
                    submitButton.removeAttribute('disabled');
                }
                console.log(response);
            }
        })
    }

</script>
@endsection
   
