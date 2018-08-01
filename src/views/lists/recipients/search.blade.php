@extends("cms::layouts.default")

@section("content")
	
	@component('cms_email::components.nav_container')
		@slot('title')Users @endslot
		<form id="selectForm">
			<table class="table">
				<thead>
					<th>Name</th>
					<th>Email</th>
					<th>Select</th>
				</thead>
				<tbody>
				@foreach($users as $user)
					<tr data-name="{{$user->name}}">
						<td>{{$user->name}}</td>
						<td>{{$user->email}}</td>
						<td>
							<input type="checkbox" name="user_id"
							       value="{{$user->id}}" />
						</td>
					</tr>
				
				@endforeach
				</tbody>
			</table>
			
			<button class="btn btn-primary"
			        onclick="window.close()">Select</button>
		</form>
	@endcomponent

@endsection


