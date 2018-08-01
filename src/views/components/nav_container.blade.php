<div class="container">
	<div class="row">
		{{--{{dd(route('campaigns.index'))}}--}}
		<div class="col">
		<nav class="nav flex-column mt-3 rounded nav-pills p-3"
		     style="background-color: #e8e8e8">
		  <a class="nav-link @if(Request::url() === route('campaigns.index')) active @endif"
		     href="{{route('campaigns.index')}}">Email Campaigns</a>
		  <a class="nav-link @if(Request::url() === route('lists.index')) active @endif"
		     href="{{route('lists.index')}}">Email List</a>
		</nav>
	</div>
			<div class="col-xl-10">
				<div class="row">
			        @component('cms::components.container')
						@slot('title'){!! $title !!} @endslot
						
						{!! $slot !!}
					
					@endcomponent
	            </div>
			</div>
	</div>
</div>