@extends("cms::layouts.default")

@section("content")
	
	@component('cms_email::components.nav_container')
		@slot('title')Email Campaigns <a
				href="{{route('campaigns.create')}}"
				class="btn btn-sm btn-success pull-right">Create New Campaign</a> @endslot
		
		<form id="campaign" method="POST">{{csrf_field()}}</form>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Title</th>
						<th>Is Scheduled</th>
						<th>Template</th>
						<th>Sent</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				@foreach($campaigns as $campaign)
					<tr>
						<td>{{$campaign->title}}</td>
						<td>{{$campaign->is_scheduled?$campaign->schedule:"Manual"}}</td>
						<td>{{$campaign->template}}</td>
						<td>{{$campaign->has_sent?"Sent":"Not Sent"}}</td>
						<td>
							<div class="btn-group btn-group-sm">
								<a href='{{route('campaigns.contents.index', $campaign->id)}}'
								   class="btn btn-primary">Content</a>
								<a href="{{route('campaigns.edit', $campaign)}}"
								   class="btn btn-info">Edit</a>
								<a class="btn btn-secondary"
								   href="{{route('campaigns.activities', $campaign)}}">Activities</a>
								
								<div class="btn-group btn-group-sm"
								     role="group">
									<button id="btnGroupDrop1"
									        type="button"
									        class="btn btn-warning dropdown-toggle"
									        data-toggle="dropdown"
									        aria-haspopup="true"
									        aria-expanded="false">More</button>
									<div class="dropdown-menu"
									     aria-labelledby="btnGroupDrop1">
										<button class="dropdown-item"
										        onclick="sendCampaign(event, '{{$campaign->id}}')">Send</button>
										<delete-item
												url="{{route('campaigns.destroy', $campaign)}}"
												inline-template>
											<a href="#"
											   class="dropdown-item text-danger"
											   @click.prevent="deleteItem">Delete</a>
										</delete-item>
									</div>
								</div>
							</div>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	
	@endcomponent

@endsection

@section("scripts")
	<script>
		function sendCampaign(e, campaign_id) {
          e.preventDefault()
          var form = document.getElementById("campaign")
          form.action = window.location.pathname + `/${campaign_id}/send`
          form.submit()
        }
	</script>
@endsection