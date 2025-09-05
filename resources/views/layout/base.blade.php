@include('layout.head')
@include('layout.header')
<div class="d-flex" style="min-height: calc(100vh - 10vh);">
    @include('layout.sidebar')
    <div class="flex-grow-1">
        <div class="content-scroll">
            @include('layout.content')
        </div>
    </div>
</div>
@include('layout.footer')