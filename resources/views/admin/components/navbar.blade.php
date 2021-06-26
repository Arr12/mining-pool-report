<section>
    <!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar desktop-toggle-hide">
        <!-- User Info -->
        <div class="user-info">
            <div class="image">
                <img src="/images/user.png" width="48" height="48" alt="User" />
            </div>
            <div class="info-container">
                <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">guest@yml</div>
                <div class="email">Guest</div>
                <div class="btn-group user-helper-dropdown">
                    <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                    <ul class="dropdown-menu pull-right">
                        {{-- <li><a href="javascript:void(0);"><i class="material-icons">person</i>Profile</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="javascript:void(0);"><i class="material-icons">group</i>Followers</a></li>
                        <li><a href="javascript:void(0);"><i class="material-icons">shopping_cart</i>Sales</a></li>
                        <li><a href="javascript:void(0);"><i class="material-icons">favorite</i>Likes</a></li>
                        <li role="separator" class="divider"></li> --}}
                        <li>
                            <!-- Authentication -->
                            {{-- <form method="POST" action=""> --}}
                                {{-- @csrf --}}
                                <button class="sign-out" type="submit"><i class="material-icons">input</i>Sign Out</button>
                            {{-- </form> --}}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- #User Info -->
        <!-- Menu -->
        <div class="menu">
            <ul class="list">
                <li class="header">MAIN NAVIGATION</li>
                <li>
                    <a href="/">
                        <i class="material-icons">home</i>
                        <span>Home</span>
                    </a>
                </li>
                @foreach ($menu->worksheet_list->sheet_title as $key => $data)
                    @if($key != count($menu->worksheet_list->sheet_title)-1)
                    <li>
                        <a href="/mining?d={{$data}}">
                            <i class="material-icons">notifications</i>
                            <span>{{$data}}</span>
                        </a>
                    </li>
                    @endif
                @endforeach
            </ul>
        </div>
        <!-- #Menu -->
        <!-- Footer -->
        <div class="legal">
            <div class="copyright">
                &copy; 2021 <a href="javascript:void(0);">All rights reserved</a>.
            </div>
            <div class="version">
                <b>Version: </b> 1.0.0
            </div>
        </div>
        <!-- #Footer -->
    </aside>
    <!-- #END# Left Sidebar -->
</section>
