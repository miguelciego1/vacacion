<-----------------------------------activo---------------------------->



 CREATE TRIGGER `cambioEstadoDetalleConfirmado` BEFORE UPDATE ON `vacacion_cabecera`
  FOR EACH ROW UPDATE vacacion_detalle d  set d.estado=NEW.estado
 WHERE NEW.id=d.vacacion_cabecera_id

 CREATE TRIGGER `confirmarDetalleDescontarGestion` AFTER UPDATE ON `vacacion_detalle`
  FOR EACH ROW IF NEW.estado = 2 THEN

    UPDATE vacacion_gestion g set g.tomados=g.tomados+NEW.dias
    WHERE g.id=NEW.vacacion_gestion_id;
 ELSEIF NEW.estado = 0 THEN
 	UPDATE vacacion_gestion g set g.tomados=g.tomados-NEW.dias
     WHERE g.id=NEW.vacacion_gestion_id;
 END IF
 <----------------------------------------end activo------------------------------------>

 CREATE TRIGGER `actualizarGestion` AFTER INSERT ON `vacacion_detalle`
  FOR EACH ROW UPDATE vacacion_gestion g set g.tomados=NEW.dias where g.id=NEW.vacacion_gestion_id

 CREATE TRIGGER `enlazarCabeceraPermiso` AFTER INSERT ON `vacacion_cabecera`
  FOR EACH ROW UPDATE permiso p set p.estado=3 WHERE
 p.empleado_id=NEW.empleado_id and DATE(p.fecha_inicio) > NEW.fecha_solicitud and p.tipo_permiso_id=3
