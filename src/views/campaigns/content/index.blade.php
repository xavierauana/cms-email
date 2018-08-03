@extends("cms::layouts.default")

@section("content")
	
	@component('cms_email::components.nav_container')
		@slot('title')
			{{$campaign->title}}
		@endslot
		
		@include("cms::admin.contents.content_blocks",['contentOwner'=>$campaign])
		
	@endcomponent


@endsection