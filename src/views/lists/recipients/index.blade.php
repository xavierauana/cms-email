@extends("cms::layouts.default")

@section("content")
	
	@component('cms_email::components.nav_container')
		@slot('title')
			Recipients for Email List: {{$list->title}}
			<a href="{{route('lists.recipients.export',$list)}}"
			   class="btn btn-sm btn-info pull-right">Export recipient</a>
			<a href="{{route('lists.recipients.create',$list)}}"
			   class="btn btn-sm btn-success pull-right">Add new recipient</a>
			<a href="{{route('lists.recipients.import',$list)}}"
			   class="btn btn-sm btn-primary pull-right mr-3">Import recipients</a>
		
		@endslot
		
		<div class="table-responsive">
			<table class="table table-hover" ref="table">
				<thead>
					<tr>
						<th>Name</th>
						<th>Email</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				@foreach($recipients as $recipient)
					<tr data-id="{{$recipient->id}}">
						<td>{{$recipient->name}}</td>
						<td>{{$recipient->email}}</td>
						<td>
							<div class="btn-group btn-group-sm">
								<delete-item
										url="{{route('lists.recipients.index', $list->id)}}/"
										inline-template>
								<button class="btn btn-danger"
								        @click.prevent="deleteItem({{$recipient->id}})">Delete</button>
									</delete-item>
							</div>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
			{!! $recipients->links() !!}
		</div>
	@endcomponent



@endsection