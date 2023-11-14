@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }} <button onclick="sendMessageToAll()" class="btn btn-danger float-end">Send Notification All</button> </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>

    <script>

        function sendMessageToAll() {
            $.ajax({
                url: '{{ route("sendMessageToAll") }}',
                type: 'POST',
                dataType: 'JSON',
                success: function (response) {
                    console.log(response)
                },
                error: function (err) {
                    console.log('User Chat Token Error'+ err);
                },
            });
        }

    </script>
@endsection
