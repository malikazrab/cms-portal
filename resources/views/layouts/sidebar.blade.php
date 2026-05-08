<!-- After existing menu items, add: -->

<!-- Headers Menu Item -->
<li class="nav-item">
    <a href="{{ route('admin.headers.index') }}" 
       class="nav-link {{ request()->routeIs('admin.headers.*') ? 'active' : '' }}">
        <i class="fas fa-header"></i>
        <span>Headers</span>
    </a>
</li>

<!-- Footers Menu Item -->
<li class="nav-item">
    <a href="{{ route('admin.footers.index') }}" 
       class="nav-link {{ request()->routeIs('admin.footers.*') ? 'active' : '' }}">
        <i class="fas fa-footer"></i>
        <span>Footers</span>
    </a>
</li>