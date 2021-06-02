@props(['relatedIds', 'title' => 'More choices to go with your product'])

<x-rapidez::productlist :title="$title" field="id" :value="explode(',', $relatedIds)"/>