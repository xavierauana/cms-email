@extends("cms::layouts.default")

@section("content")
	
	@component('cms_email::components.nav_container')
		@slot('title')Create New Email Campaign @endslot
		
		{{Form::open(['url'=>route('campaigns.store'), 'method'=>'POST'])}}
		
		<div class="form-group">
			{{Form::label('title', 'Campaign Title')}}
			{{Form::text('title', '', ['class'=>'form-control', 'placeholder'=>'Campaign Title'])}}
			@if ($errors->has('title'))
				<span class="help-block">
					<strong>{{ $errors->first('title') }}</strong>
				</span>
			@endif
		</div>
		
		
		<div class="form-group">
			{{Form::label('subject', 'Email Subject')}}
			{{Form::text('subject', '', ['class'=>'form-control', 'placeholder'=>'Email Subject','required'])}}
			@if ($errors->has('subject'))
				<span class="help-block">
					<strong>{{ $errors->first('subject') }}</strong>
				</span>
			@endif
		</div>
		
		
		<div class="form-group">
			{{Form::label('email_list_id', 'Email List')}}
			{{Form::select('email_list_id', \Anacreation\CmsEmail\Models\EmailList::pluck("title","id")->toArray(),null, ['class'=>'form-control', 'placeholder'=>'Email List'])}}
			@if ($errors->has('email_list_id'))
				<span class="help-block">
					<strong>{{ $errors->first('email_list_id') }}</strong>
				</span>
			@endif
		</div>
		
		<div class="form-group">
			{{Form::label('role_id', 'User Role')}}
			{{Form::select('role_id', \Anacreation\Cms\Models\Role::pluck("label","id")->toArray(),null, ['class'=>'form-control', 'placeholder'=>'User Role'])}}
			@if ($errors->has('role_id'))
				<span class="help-block">
					<strong>{{ $errors->first('role_id') }}</strong>
				</span>
			@endif
		</div>
		
		<div class="form-group">
			{{Form::label('from_name', 'From Name')}}
			{{Form::text('from_name', config('mail.from.name'), ['class'=>'form-control', 'placeholder'=>'From Name'])}}
			@if ($errors->has('from_name'))
				<span class="help-block">
					<strong>{{ $errors->first('from_name') }}</strong>
				</span>
			@endif
		</div>
		
		
		<div class="form-group">
			{{Form::label('from_address', 'From Address')}}
			{{Form::text('from_address', config('mail.from.address'), ['class'=>'form-control', 'placeholder'=>'From Address'])}}
			@if ($errors->has('from_address'))
				<span class="help-block">
					<strong>{{ $errors->first('from_address') }}</strong>
				</span>
			@endif
		</div>
		
		<div class="form-group">
			{{Form::label('reply_address', 'Reply Address')}}
			{{Form::text('reply_address', config('mail.from.address'), ['class'=>'form-control', 'placeholder'=>'Reply Address'])}}
			@if ($errors->has('reply_address'))
				<span class="help-block">
					<strong>{{ $errors->first('reply_address') }}</strong>
				</span>
			@endif
		</div>
		
		
		<div class="form-group">
			{{Form::label('template', 'Email Template')}}
			{{Form::select('template', array_combine($templates,$templates) ,null,['class'=>'form-control'])}}
			@if ($errors->has('template'))
				<span class="help-block">
					<strong>{{ $errors->first('template') }}</strong>
				</span>
			@endif
		</div>
		
		<div class="form-group">
			{{Form::label('is_scheduled', 'Is Scheduled')}}
			{{Form::select('is_scheduled', [0=>'No', 1=>'Yes'],0,[
			'class'=>'form-control',
			'onchange'=>'toggleDateTimeInput(event)',
			'required',
			])}}
			@if ($errors->has('is_scheduled'))
				<span class="help-block">
					<strong>{{ $errors->first('is_scheduled') }}</strong>
				</span>
			@endif
		</div>
		
		<div class="form-group" id="schedule-container" style="display: none">
			{{Form::label('schedule', 'Schedule at')}}
			<base-datetime name="schedule"
			               value="{{old('schedule')?:''}}"></base-datetime>
			@if ($errors->has('schedule'))
				<span class="help-block">
					<strong>{{ $errors->first('schedule') }}</strong>
				</span>
			@endif
		</div>
		
		<div class="form-group">
			{{Form::submit('Create', ['class'=>'btn btn-success'])}}
			<a href='{{route('campaigns.index')}}' class="btn btn-info">Back</a>
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