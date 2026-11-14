<span class="position-relative">
    <i class="fa fa-shopping-cart fa-lg"></i>
    @if($count > 0)
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
            {{ $count }}
        </span>
    @endif
</span>
