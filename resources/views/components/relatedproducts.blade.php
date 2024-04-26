@props(['relatedIds', 'title' => 'More choices to go with your product'])

@if($relatedIds)
    <x-rapidez::productlist :title="$title" field="entity_id" :value="$relatedIds"/>
@endif
