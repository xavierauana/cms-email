@extends("cms::layouts.default")

@section("content")
	
	@component('cms_email::components.nav_container')
		@slot('title')
			Recipients for Campaign: {{$campaign->title}}
			<a href="{{route('campaigns.recipients.create',$campaign)}}"
			   class="btn btn-sm btn-success pull-right">Add new recipient</a>
			<a href="{{route('campaigns.recipients.create',$campaign)}}"
			   class="btn btn-sm btn-primary pull-right mr-3">Import recipients</a>
		
		@endslot
		
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Name</th>
						<th>Email</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				@foreach($recipients as $recipient)
					<tr>
						<td>{{$recipient->name}}</td>
						<td>{{$recipient->email}}</td>
						<td>
							<div class="btn-group btn-group-sm">
								<delete-item
										url="{{route('pages.destroy', $campaign->id)}}"
										inline-template>
								<button class="btn btn-danger"
								        @click.prevent="deleteItem">Delete</button>
									</delete-item>
							</div>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
			{{$recipients->links()}}
		</div>
	@endcomponent



@endsection