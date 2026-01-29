<?
function tiene_permiso($seccion, $permiso, $rol){
  if($rol == 1) return true;
  $result = false;
  $qry = "SELECT us.permiso FROM user_section us LEFT JOIN section s ON us.section = s.id WHERE us.rol = ".$rol." AND s.name = '".$seccion."' AND s.action = '".$permiso."'";
  $permiso = UserSection::model()->executeQuery($qry);
  $result = ($permiso[0]->permiso == 1) ? true:false;
  return $result;
}

?>
      <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
          <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
              <div class="dropdown profile-element">
                <span><img id="menuAvatar" style="max-width: 80px; border-radius: 50%;" alt="image" class="img-responsive" src="<?=$url("images/logo-optica.png");?>"/></span>
                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                  <span class="clear">
                    <span class="block m-t-xs">
                      <strong class="font-bold"><?=($reclutador->id != "") ? $reclutador->nombre:$user->username;?></strong>
                    </span> 
                    <!--<span class="text-xs block"><?=$rol->name;?> <b class="caret"></b></span>-->
                  </span>
                </a>
                <ul class="dropdown-menu animated fadeInRight m-t-xs">
                  <li><a href="<?=$url("ecom/auth/logout")?>">Cerrar sesión</a></li>
                </ul>
              </div>
              <div class="logo-element">
              Óptica
              </div>
            </li>

            <li>
              <a href="<?=$url("ecom")?>"><i class="fa fa-th-large"></i><span class="nav-label"> Dashboard</span></a>
            </li>
            
 
            <?if(tiene_permiso("Usuario", "Init", $user->rol) || tiene_permiso("Rol", "Init", $user->rol)):?>
            <li>
              <a href="javascript: void(0);"><i class="far fa-unlock-alt"></i><span class="nav-label"> Seguridad</span><i class="far fa-caret-down float-right"></i></a>
              <ul class="nav nav-second-level">
                <?if(tiene_permiso("Usuario", "Init", $user->rol)):?>
                <li><a href="<?=$url("ecom/rol")?>">Roles</a></li>
                <?endif;?>
                <?if(tiene_permiso("PermisoAyuda", "Init", $user->rol)):?>
                <li><a href="<?=$url("ecom/permisoayuda")?>">Ayuda de permisos</a></li>
                <?endif;?>
              </ul>
            </li>
            <?endif;?>

            <?if(tiene_permiso("Sucursal", "Init", $user->rol) && ($this->user->id == "1" || $this->user->id == "5") ):?>
            <li>
              <a href="<?=$url("ecom/sucursal")?>">
                <i class="fas fa-store"></i>
                <span class="nav-label"> Sucursales</span>
              </a>
            </li>
            <?endif;?>



            <?if(tiene_permiso("Cliente", "Init", $user->rol) || tiene_permiso("Operador", "Init", $user->rol)):?>
            <li>
              <a href="javascript: void(0);"><i class="fal fa-users"></i><span class="nav-label"> Usuarios</span><i class="far fa-caret-down float-right"></i></a>
              <ul class="nav nav-second-level">
                <?if(tiene_permiso("Cliente", "Init", $user->rol)):?>
                <li><a href="<?=$url("ecom/cliente")?>">Lista clientes</a></li>
                <?endif;?>
                <?if(tiene_permiso("Operador", "Init", $user->rol)):?>
                <li><a href="<?=$url("ecom/operador")?>">Lista usuarios</a></li>
                <?endif;?>
              </ul>
            </li>
            <?endif;?>

            <?if(tiene_permiso("Producto", "Init", $user->rol)):?>
            <li>
              <a href="<?=$url("ecom/producto/")?>"><i class="fas fa-glasses"></i><span class="nav-label"> Productos</span></a>
            </li>
            <?endif;?>

            <?if(tiene_permiso("Compra", "Init", $user->rol)):?>
            <li>
              <a href="javascript: void(0);"><i class="fas fa-industry"></i><span class="nav-label"> Inventarios</span><i class="fas fa-caret-down float-right"></i></a>
              <ul class="nav nav-second-level">
                <?if(tiene_permiso("Compra", "Init", $user->rol)):?>
                <li><a href="<?=$url("ecom/compra")?>">Compras</a></li>
                <?endif;?>
                <?if(tiene_permiso("Inventario", "Init", $user->rol)):?>
                <li><a href="<?=$url("ecom/inventario")?>">Inventario</a></li>
                <?endif;?>
                <?if(tiene_permiso("Inventario", "Transferencia", $user->rol)):?>
                <li><a href="<?=$url("ecom/inventario/transferencia")?>">Transferencia de almacén</a></li>
                <?endif;?>
              </ul>
            </li>
            <?endif;?>

            <?if(tiene_permiso("Producto", "Init", $user->rol)):?>
            <li>
              <a href="<?=$url("ecom/producto/micas")?>"><i class="fas fa-glasses"></i><span class="nav-label"> Micas</span></a>
            </li>
            <?endif;?>

            <?if(tiene_permiso("Producto", "Init", $user->rol)):?>
            <li>
              <a href="<?=$url("ecom/producto/extras")?>"><i class="fas fa-puzzle-piece"></i><span class="nav-label"> Extras</span></a>
            </li>
            <?endif;?>



            <?if(tiene_permiso("Corte", "Init", $user->rol)):?>
            <li>
              <a href="<?=$url("ecom/corte")?>"><i class="fas fa-money-check-alt"></i><span class="nav-label"> Cortes de caja</span></a>
            </li>
            <?endif;?>


            <?if(tiene_permiso("Venta", "Init", $user->rol)):?>
            <li>
              <a href="javascript: void(0);"><i class="fas fa-dollar-sign"></i><span class="nav-label"> Ventas</span><i class="far fa-caret-down float-right"></i></a>
              <ul class="nav nav-second-level">

                <?if(tiene_permiso("Venta", "Init", $user->rol)):?>
                <li><a href="<?=$url("ecom/venta/agregar")?>">Punto de Venta</a></li>
                <?endif;?>

                <?if(tiene_permiso("Venta", "Cotizaciones", $user->rol)):?>
                <li><a href="<?=$url("ecom/venta/cotizaciones")?>">Cotizaciones</a></li>
                <?endif;?>

                <?if(tiene_permiso("Venta", "Ventas", $user->rol)):?>
                <li><a href="<?=$url("ecom/venta/ventas")?>">Ventas</a></li>
                <?endif;?>

                <?if(tiene_permiso("Venta", "Apartados", $user->rol)):?>
                <li><a href="<?=$url("ecom/venta/apartados")?>">Apartados</a></li>
                <?endif;?>

                <?if(tiene_permiso("Venta", "EnviarLaboratorio", $user->rol)):?>
                <li><a href="<?=$url("ecom/venta/enviarlaboratorio")?>">Por enviar a laboratorio</a></li>
                <?endif;?>

                <?if(tiene_permiso("Venta", "Laboratorio", $user->rol)):?>
                <li><a href="<?=$url("ecom/venta/laboratorio")?>">En laboratorio</a></li>
                <?endif;?>


                <?if(tiene_permiso("Venta", "Sucursal", $user->rol)):?>
                <li><a href="<?=$url("ecom/venta/sucursal")?>">En sucursal</a></li>
                <?endif;?>


                <?if(tiene_permiso("Venta", "Entregados", $user->rol)):?>
                <li><a href="<?=$url("ecom/venta/entregados")?>">Entregados</a></li>
                <?endif;?>
              
              </ul>
            </li>
            <?endif;?>


            <?if(tiene_permiso("Reporte", "Init", $user->rol)):?>
            <li>
              <a href="javascript: void(0);"><i class="fas fa-chart-line"></i><span class="nav-label"> Reportes</span><i class="far fa-caret-down float-right"></i></a>
              <ul class="nav nav-second-level">

                <?if(tiene_permiso("Reporte", "Init", $user->rol)):?>
                <li><a href="<?=$url("ecom/reporte/ventas")?>">Reporte de ventas</a></li>
                <?endif;?>
              
              </ul>
            </li>
            <?endif;?>


            <?if(tiene_permiso("VentaFacturaSolicitud", "Init", $user->rol)):?>
            <li>
              <a href="<?=$url("ecom/ventafacturasolicitud")?>">
                <i class="fas fa-file-invoice"></i>
                <span class="nav-label"> Solicitud Venta Factura</span>
              </a>
            </li>
            <?endif;?>



          

          </ul>

        </div>
      </nav>