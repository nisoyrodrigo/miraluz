    <div class="row border-bottom">
      <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">

        <div class="navbar-header">
          <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
          <ul id="search-results" class="nav navbar-top-links navbar-left">
            <li style="padding: 20px">
              <span class="m-r-sm welcome-message" style="font-weight: bold;">PLATAFORMA DIGITAL</span>
            </li>
          </ul>
        </div>

        <ul class="nav navbar-top-links navbar-right">
          <li class="dropdown" id="notificaciones_lista">
            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
              <i class="fa fa-bell"></i>  <span class="label label-primary"><?=$no_leidas;?></span>
            </a>
            <ul class="dropdown-menu dropdown-alerts" id="notificaciones_lista_interior">
              <li>
                <div class="text-center link-block">
                  <a href="notifications.html">
                    <strong>Ver todas</strong>
                    <i class="fa fa-angle-right"></i>
                  </a>
                </div>
              </li>
            </ul>
          </li>


          <li>
            <a href="<?=$url("ecom/auth/logout")?>">
              <i class="fa fa-sign-out"></i> Cerrar sesión
            </a>
          </li>
        </ul>

      </nav>
    </div>