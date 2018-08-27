@extends("cms::layouts.default")

@section("content")
	
	@component('cms_email::components.nav_container')
		@slot('title')Add New Recipient Campaign @endslot
		
		{{Form::open(['url'=>route('lists.recipients.store', $list), 'method'=>'POST'])}}
		
		<div class="card-body">
			    <div class="form-group">
			{{Form::label('name', 'Recipient Name')}}
				    {{Form::text('name', '', ['class'=>'form-control', 'placeholder'=>'Recipient Name'])}}
				    @if ($errors->has('name'))
					    <span class="help-block">
					<strong>{{ $errors->first('name') }}</strong>
				</span>
				    @endif
				
				
				    <div class="form-group">
			{{Form::label('email', 'Email')}}
					    {{Form::email('email', '', ['class'=>'form-control', 'placeholder'=>'Recipient Email'])}}
					    @if ($errors->has('email'))
						    <span class="help-block">
					<strong>{{ $errors->first('email') }}</strong>
				</span>
					    @endif
		</div>
		
		    </div>
		    </div>
		
		<div class="form-group">
			{{Form::submit('Add', ['class'=>'btn btn-success'])}}
			<a href='{{route('lists.recipients.index', $list)}}'
			   class="btn btn-info">Back</a>
		</div>
		
		
		{{Form::close()}}
	@endcomponent

@endsection

@section('scripts')
	<script>
        function searchUser() {
          var el = document.querySelector("#search_user_name")
          var inputValue = el.value
          if (inputValue) {
            //to actually open the window..
            var win = window.open("?search=" + inputValue);
            win.onbeforeunload = function () {
              var form = win.document.querySelector("form#selectForm")
              var value = form.user_id.value
              if (value) {
                var input = document.querySelector("#user_id")
                input.value = value
                el.value = form.user_id.parentNode.parentNode.dataset.name
              }
            };
          }

        }
	</script>
@endsection