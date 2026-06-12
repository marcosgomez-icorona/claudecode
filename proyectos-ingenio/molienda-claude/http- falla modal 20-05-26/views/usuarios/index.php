<?php

include 'controller/usuarios.php';

?>
<div class="container-fluid">
    <h4> Lista de Usuarios </h4>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <nav class="navbar navbar-light bg-light">
                    <form route="home.php" method="GET" class="form-inline" >
                        <input name="nombre" size="42"  class="form-control mr-sm-2" type="search" placeholder="Nombre" aria-label="Search">                                   
                        <input name="menu" hidden=""value="usuarios">
                        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>
                    </form>
                </nav>
            </div>
            <div class="card" style="width: 1024px;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    
                    <a href="home.php?accion=agregar_usuario" class="btn btn-success my-1 my-sm-0 btn-sm">Agregar Usuario</a>
                    
                </div>

                <div class="card-body">      
                    <table class="table-striped">
                        <thead class="p-3 mb-2 bg-info text-white">
                            <tr>
                                <th scope="col">Nombre</th>
                                <th scope="col">Tipo</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                           <?php
                                while($row_resultado_usuarios = $resultado_usuarios->fetch_assoc()) {
                           ?>
                            <tr>                                
                                <td><?php if(!empty($row_resultado_usuarios)){ echo $row_resultado_usuarios['nombre']; }?></td>
                                <td><?php if(!empty($row_resultado_usuarios)){ echo $row_resultado_usuarios['tipo_usuario']; }?></td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group" aria-label="...">
                                        <a href="home.php?accion=modificar_usuario&id=<?php if(!empty($row_resultado_usuarios)){ echo $row_resultado_usuarios['id']; }?>" class="btn btn-warning my-1 my-sm-0 btn-sm">Editar</a>
                                        <form method="POST" action="home.php?menu=usuarios&accion=baja&id=<?php if(!empty($row_resultado_usuarios)){ echo $row_resultado_usuarios['id']; }?>">
                                            <button type="submit" Onclick="return confirm('Esta seguro de Inactivar?');" class="btn btn-danger my-1 my-sm-0 btn-sm">Eliminar</button>
                                        </form>
                                    </div>    
                                </td>
                            </tr>
                            <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>