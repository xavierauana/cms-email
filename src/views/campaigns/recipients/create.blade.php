@extends("cms::layouts.default")

@section("content")
	
	@component('cms::components.container')
		@slot('title')Add New Recipient Campaign @endslot
		
		{{Form::open(['url'=>route('campaigns.recipients.store', $campaign), 'method'=>'POST'])}}
		<div class="card">
			<div class="card-body">
			<div class="form-group">
				{{Form::label('user_id', 'Select from user')}}
					<div class="input-group mb-3">
				  <input type="text"
				         class="form-control"
				         id="search_user_name"
				         placeholder="Search Keyword"
				         aria-label="Search Keyword"
				         aria-describedby="basic-addon2">
				  <div class="input-group-append">
				    <button class="btn btn-outline-secondary"
				            onclick="searchUser()"
				            type="button">Search</button>
				  </div>
				</div>
				<input type="hidden" name="user_id" id="user_id">
					@if ($errors->has('user_id'))
						<span class="help-block">
						<strong>{{ $errors->first('user_id') }}</strong>
					</span>
					@endif
			</div>
			</div>
		</div>
		
		<h3 class="text-center">-- or --</h3>
		
		<div class="card mb-3">
		    <div class="card-body">
			    <div class="form-group">
			{{Form::label('name', 'Recipient Name')}}
				    {{Form::text('name', '', ['class'=>'form-control', 'placeholder'=>'Recipient Name'])}}
				    @if ($errors->has('name'))
					    <span class="help-block">
					<strong>{{ $errors->first('name') }}</strong>
				</span>
				    @endif
		</div>
		
		
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
			<a href='{{route('menus.index')}}' class="btn btn-info">Back</a>
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