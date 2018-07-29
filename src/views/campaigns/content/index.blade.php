@extends("cms::layouts.default")

@section("content")
	
	@component('cms::components.container')
		@slot('title'){{$campaign->title}}
		
		@endslot
		
		<content-blocks :contents="{{ count($contents) > 0 ? json_encode($contents) : json_encode(new stdClass()) }}"
		                :editable="{{auth()->user()->hasPermission('edit_content')}}=='1'"
		                :deleteable="{{auth()->user()->hasPermission('delete_content')}}=='1'"
		                :languages="{{$languages}}"
		                :can-add="{{$campaign->editable?:0}}=='1'"
		                :types="{{json_encode((new \Anacreation\Cms\Services\ContentService())->getTypesForJs())}}"
		></content-blocks>
		
	
	@endcomponent


@endsection