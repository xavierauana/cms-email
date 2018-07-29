@extends("cms::layouts.default")

@section("content")
	
	@component('cms::components.container')
		@slot('title')
			Recipients for Email List: {{$list->title}}
			<a href="{{route('lists.recipients.create',$list)}}"
			   class="btn btn-sm btn-success pull-right">Add new recipient</a>
			<a href="{{route('lists.recipients.import',$list)}}"
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
										url="{{route('lists.recipients.destroy', [$list->id, $recipient->id])}}"
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