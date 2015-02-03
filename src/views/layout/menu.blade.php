<div class="sidebar" id="sidebar">
    <script type="text/javascript">
        try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
    </script>

    <div class="sidebar-shortcuts" id="sidebar-shortcuts">
        <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
            @if($dashboardIcons)
                <button class="btn btn-success">
                    <a href="{{\URL::to($dashboardIcons['icon1']['link']) }}" style="color:white;">
                        <i class="{{$dashboardIcons['icon1']['icon'] }}"></i>
                    </a>
                </button>
                <button class="btn btn-info">
                    <a href="{{\URL::to($dashboardIcons['icon2']['link']) }}" style="color:white;">
                        <i class="{{$dashboardIcons['icon2']['icon'] }}"></i>
                    </a>
                </button>
                <button class="btn btn-warning">
                    <a href="{{\URL::to($dashboardIcons['icon3']['link']) }}" style="color:white;">
                        <i class="{{$dashboardIcons['icon3']['icon'] }}"></i>
                    </a>
                </button>
                <button class="btn btn-danger">
                    <a href="{{\URL::to($dashboardIcons['icon4']['link']) }}" style="color:white;">
                        <i class="{{$dashboardIcons['icon4']['icon'] }}"></i>
                    </a>
                </button>
            @endif
        </div>

        <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
            <span class="btn btn-success"></span>

            <span class="btn btn-info"></span>

            <span class="btn btn-warning"></span>

            <span class="btn btn-danger"></span>
        </div>
    </div><!-- #sidebar-shortcuts -->
    <ul class="nav nav-list">
        {{ render_menu($menu) }}
    </ul>

    <div class="sidebar-collapse" id="sidebar-collapse">
        <i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
    </div>

    <script type="text/javascript">
        try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
    </script>
</div>
