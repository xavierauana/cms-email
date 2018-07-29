@extends("cms::layouts.default")

@section("content")
	
	@component('cms::components.container')
		@slot('title')Create New Email List @endslot
		
		{{Form::open(['url'=>route('lists.store'), 'method'=>'POST'])}}
		
		<div class="form-group">
			{{Form::label('title', 'List Title')}}
			{{Form::text('title', '', ['class'=>'form-control', 'placeholder'=>'List Title'])}}
			@if ($errors->has('title'))
				<span class="help-block">
					<strong>{{ $errors->first('title') }}</strong>
				</span>
			@endif
		</div>
		<div class="form-group">
			{{Form::label('confirm_opt_in', 'Opt-in confirmation needed')}}
			<br>
			<div class="btn-group btn-group-toggle" data-toggle="buttons">
			  <label class="btn btn-outline-primary active">
			    <input type="radio" name="confirm_opt_in" autocomplete="off"
			           value="1" checked> Yes
			  </label>
			  <label class="btn btn-outline-primary">
			    <input type="radio" name="confirm_opt_in" autocomplete="off"
			           value="0"> No
			  </label>
			</div>
		</div>
		
		<div class="form-group">
			{{Form::label('has_welcome_message', 'Auto Send Welcome Message')}}
			<br>
			<div class="btn-group btn-group-toggle" data-toggle="buttons">
			  <label class="btn btn-outline-primary active">
			    <input type="radio" name="has_welcome_message"
			           autocomplete="off"
			           value="1" checked> Yes
			  </label>
			  <label class="btn btn-outline-primary">
			    <input type="radio" name="has_welcome_message"
			           autocomplete="off"
			           value="0"> No
			  </label>
			</div>
		</div>
		
		<div class="form-group">
			{{Form::label('has_goodbye_message', 'Auto Send Goodbye Message')}}
			<br>
			<div class="btn-group btn-group-toggle" data-toggle="buttons">
			  <label class="btn btn-outline-primary active">
			    <input type="radio" name="has_goodbye_message"
			           autocomplete="off"
			           value="1" checked> Yes
			  </label>
			  <label class="btn btn-outline-primary">
			    <input type="radio" name="has_goodbye_message"
			           autocomplete="off"
			           value="0"> No
			  </label>
			</div>
		</div>
		
		
		
		<div class="form-group">
			{{Form::submit('Create', ['class'=>'btn btn-success'])}}
			<a href='{{route('campaigns.index')}}' class="btn btn-info">Back</a>
		</div>
		
		
		{{Form::close()}}
	@endcomponent

@endsection