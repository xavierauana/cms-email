@extends("cms::layouts.default")

@section("content")
	
	@component('cms_email::components.nav_container')
		@slot('title')Recipients with status: {{$status}}
		<form style="display: inline"
		      method="POST"
		      action="{{route('campaigns.resend.all', $campaign)}}">
			{{csrf_field()}}
			<button class="btn btn-success btn-sm pull-right text-light">Resend All</button>
		</form>
		
		@endslot
		
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Name</th>
						<th>Email</th>
						<th>Status</th>
						@if($status === \Anacreation\CmsEmail\Models\CampaignStatus::Status['bounce'] or
						$status === \Anacreation\CmsEmail\Models\CampaignStatus::Status['dropped'])
							<th>Reason</th>
						@endif
						@if($status === \Anacreation\CmsEmail\Models\CampaignStatus::Status['none'])
							<th>Actions</th>
						@endif
					</tr>
				</thead>
				<tbody>
				@foreach($recipientStatuses as $recipientStatus)
					<tr>
						<td>{{$recipientStatus->recipient->name}}</td>
						<td>{{$recipientStatus->recipient->email}}</td>
						<td>{{$recipientStatus->status}}</td>
						@if($status === \Anacreation\CmsEmail\Models\CampaignStatus::Status['bounce'] or
						$status === \Anacreation\CmsEmail\Models\CampaignStatus::Status['dropped'])
							<td>{{$recipientStatus->reason}}</td>
						@endif
						@if($status === \Anacreation\CmsEmail\Models\CampaignStatus::Status['none'])
							<td><button class="btn btn-success">Resend</button></td>
						@endif
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
		
		{{$recipientStatuses->links()}}
	
	@endcomponent

@endsection