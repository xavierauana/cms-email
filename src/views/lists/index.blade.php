@extends("cms::layouts.default")

@section("content")
	@component('cms::components.container')
		@slot('title')Email Lists <a href="{{route('lists.create')}}"
		                             class="btn btn-sm btn-success pull-right">Create New Email List</a> @endslot
		
		<form id="campaign" method="POST">
			{{csrf_field()}}
			<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Title</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				@foreach($lists as $list)
					<tr>
						<td>{{$list->title}}</td>
						<td>
							<div class="btn-group btn-group-sm">
								
								<a href="{{route('lists.edit', $list)}}"
								   class="btn btn-info">Edit</a>
								<a href="{{route('lists.recipients.index',$list )}}"
								   class="btn btn-secondary">Recipients</a>
								
								<delete-item
										url="{{route('lists.destroy', $list)}}"
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
		</div>
		</form>
	
	@endcomponent
@endsection