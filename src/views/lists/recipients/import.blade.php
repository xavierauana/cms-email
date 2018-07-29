@extends("cms::layouts.default")

@section("content")
	
	@component('cms::components.container')
		@slot('title')Add New Recipient Campaign @endslot
		
		{{Form::open(['url'=>route('lists.recipients.import', $list), 'method'=>'POST', 'files'=>true])}}
		
		<div class="card-body">
		    <div class="form-group">
				{{Form::label('file', 'CSV File')}}
			    {{Form::file('file', ['class'=>'form-control', 'placeholder'=>'CSV File'])}}
			    @if ($errors->has('file'))
				    <span class="help-block">
						<strong>{{ $errors->first('file') }}</strong>
					</span>
			    @endif
			</div>
		</div>
		
		<div class="form-group">
			{{Form::submit('Import', ['class'=>'btn btn-success'])}}
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