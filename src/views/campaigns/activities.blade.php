@extends("cms::layouts.default")

@section("content")
	
	@component('cms_email::components.nav_container')
		@slot('title')Status for Campaign: {{$campaign->title}}  @endslot
		
		<div class="row">
			<div class="col-sm-4 col-xs-6 mb-3">
				<div class="card">
					<h4 class="card-header">Total Recipients</h4>
					<div class="card-body">
						<h3 id="totalRecipients"><i
									class="fa fa-circle-o-notch fa-spin"
									style="font-size:24px"></i></h3>
					</div>
				</div>
			</div>
			<div class="col-sm-4 col-xs-6 mb-3">
				<div class="card">
					<a href="{{route('campaigns.activities.details',[$campaign, \Anacreation\CmsEmail\Models\CampaignStatus::Status['to_provider']])}}"><h4
								class="card-header">Send to Provider</h4></a>
					<div class="card-body">
						<h3 id="totalToProvider"><i
									class="fa fa-circle-o-notch fa-spin"
									style="font-size:24px"></i></h3>
					</div>
				</div>
			</div>
			<div class="col-sm-4 col-xs-6 mb-3">
				<div class="card">
					<a href="{{route('campaigns.activities.details',[$campaign, \Anacreation\CmsEmail\Models\CampaignStatus::Status['none']])}}"><h4
								class="card-header">Not Send</h4></a>
					<div class="card-body">
						<h3 id="notSent"><i class="fa fa-circle-o-notch fa-spin"
						                    style="font-size:24px"></i></h3>
					</div>
				</div>
			</div>
			<div class="col-sm-4 col-xs-6 mb-3">
				<div class="card">
					<a href="{{route('campaigns.activities.details',[$campaign, \Anacreation\CmsEmail\Models\CampaignStatus::Status['delivered']])}}"><h4
								class="card-header">Delivered</h4></a>
					<div class="card-body">
						<h3 id="totalDelivered"><i
									class="fa fa-circle-o-notch fa-spin"
									style="font-size:24px"></i></h3>
					</div>
				</div>
			</div>
			<div class="col-sm-4 col-xs-6 mb-3">
				<div class="card">
					<a href="{{route('campaigns.activities.details',[$campaign, \Anacreation\CmsEmail\Models\CampaignStatus::Status['bounce']])}}"><h4
								class="card-header">Bounced</h4></a>
					<div class="card-body">
						<h3 id="totalBounce"><i
									class="fa fa-circle-o-notch fa-spin"
									style="font-size:24px"></i></h3>
					</div>
				</div>
			</div>
			<div class="col-sm-4 col-xs-6 mb-3">
				<div class="card">
					<a href="{{route('campaigns.activities.details',[$campaign, \Anacreation\CmsEmail\Models\CampaignStatus::Status['dropped']])}}"><h4
								class="card-header">Dropped</h4></a>
					<div class="card-body">
						<h3 id="totalDropped"><i
									class="fa fa-circle-o-notch fa-spin"
									style="font-size:24px"></i></h3>
					</div>
				</div>
			</div>
		</div>
	
	
	@endcomponent

@endsection

@section('scripts')
	
	<script>
		
		function getData() {
          axios.get(window.location.href + "?ajax")
               .then(function (response) {
                 _.forEach(Object.keys(response.data), function (key) {
                   var el = document.getElementById(key)
                   if (el) {


                     el.innerText = parseInt(response.data[key]).toLocaleString(
                       undefined, // leave undefined to use the browser's locale,
                       // or use a string like 'en-US' to override it.
                       {minimumFractionDigits: 0}
                     );
                   }
                 })
               })
               .catch(function () {
                 alert("Something wrong, pls reload page later.")
               })
        }

        getData()

        setInterval(function () {
          getData()
        }, 10000)
	</script>

@endsection