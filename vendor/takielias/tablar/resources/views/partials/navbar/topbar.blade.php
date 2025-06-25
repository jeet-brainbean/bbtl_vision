@include('tablar::partials.header.top')
<header class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar navbar-light">
            <div class="container-xl">
                <ul class="navbar-nav">
                        @foreach($tablar->menu('sidebar') as $item)
                            @continue(in_array($item['text'], ['Transaction History', 'Purchase Credit']))
                            @include('tablar::partials.navbar.dropdown-item', ['item' => $item])
                        @endforeach
                </ul>
                {{-- @include('tablar::partials.navbar.search') --}}
            </div>
        </div>
    </div>
</header>
