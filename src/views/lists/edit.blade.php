@extends("cms::layouts.default")

@section("content")
	@component('cms_email::components.nav_container')
		@slot('title')Edit Campaign: {{$list->title}} @endslot
		
		{{Form::model($list, ['url'=>route('lists.update', $list), 'method'=>'PUT'])}}
		
		<div class="form-group">
			{{Form::label('title', 'List Title')}}
			{{Form::text('title', old('title')??$list->title, ['class'=>'form-control', 'placeholder'=>'List Title'])}}
			@if ($errors->has('title'))
				<span class="help-block">
					<strong>{{ $errors->first('title') }}</strong>
				</span>
			@endif
		</div>
		
		<?php $check = old('confirm_opt_in')??$list->confirm_opt_in; ?>
		<div class="form-group">
			{{Form::label('confirm_opt_in', 'Opt-in confirmation needed')}}
			<br>
			<div class="btn-group btn-group-toggle" data-toggle="buttons">
			  <label class="btn btn-outline-primary  @if($check) active @endif">
			    <input type="radio" name="confirm_opt_in" autocomplete="off"
			           value="1" @if($check) checked @endif> Yes
			  </label>
			  <label class="btn btn-outline-primary  @if(!$check) active @endif">
			    <input type="radio" name="confirm_opt_in" autocomplete="off"
			           value="0" @if(!$check) checked @endif> No
			  </label>
			</div>
			@if ($errors->has('confirm_opt_in'))
				<span class="help-block">
					<strong>{{ $errors->first('confirm_opt_in') }}</strong>
				</span>
			@endif
		</div>

        <?php $check = old('has_welcome_message')??$list->has_welcome_message; ?>
		<div class="form-group">
			{{Form::label('has_welcome_message', 'Auto Send Welcome Message')}}
			<br>
			<div class="btn-group btn-group-toggle" data-toggle="buttons">
			  <label class="btn btn-outline-primary  @if($check) active @endif">
			    <input type="radio" name="has_welcome_message"
			           autocomplete="off"
			           value="1" @if($check) checked @endif> Yes
			  </label>
			  <label class="btn btn-outline-primary @if(!$check) active @endif">
			    <input type="radio" name="has_welcome_message"
			           autocomplete="off"
			           value="0" @if(!$check) checked @endif> No
			  </label>
			</div>
			@if ($errors->has('has_welcome_message'))
				<span class="help-block">
					<strong>{{ $errors->first('has_welcome_message') }}</strong>
				</span>
			@endif
		</div>

        <?php $check = old('has_goodbye_message')??$list->has_goodbye_message; ?>
		<div class="form-group">
			{{Form::label('has_goodbye_message', 'Auto Send Goodbye Message')}}
			<br>
			<div class="btn-group btn-group-toggle" data-toggle="buttons">
			  <label class="btn btn-outline-primary  @if($check) active @endif">
			    <input type="radio" name="has_goodbye_message"
			           autocomplete="off"
			           value="1" @if($check) checked @endif> Yes
			  </label>
			  <label class="btn btn-outline-primary  @if(!$check) active @endif">
			    <input type="radio" name="has_goodbye_message"
			           autocomplete="off"
			           value="0" @if(!$check) checked @endif> No
			  </label>
			</div>
			@if ($errors->has('has_goodbye_message'))
				<span class="help-block">
					<strong>{{ $errors->first('has_goodbye_message') }}</strong>
				</span>
			@endif
		</div>
		
		<div class="form-group">
			{{Form::submit('Update', ['class'=>'btn btn-success'])}}
			<a href='{{route('lists.index')}}' class="btn btn-info">Back</a>
		</div>
		
		
		{{Form::close()}}
	@endcomponent

@endsection

@section('scripts')
	<script>
		function toggleDateTimeInput(e) {
          var el = document.querySelector("#schedule-container")
          if (e.target.value === '1') {
            $(el).show()
          } else {
            $(el).hide()
          }
        }
		
		@if(old('is_scheduled') === '1')
        toggleDateTimeInput({target: {value: '1'}})
		@endif
	</script>
@endsection