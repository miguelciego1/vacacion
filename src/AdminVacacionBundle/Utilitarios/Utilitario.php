<?php
namespace AdminVacacionBundle\Utilitarios;

class Utilitario
{
  public function fechaFin($fecha,$dias,$feriados){
        // $em=$this->getDoctrine()->getManager();
        if(!is_string($fecha)){
        $fecha=$fecha->format('Y-m-d');
        }

        for ($i = 0; $i < $dias; $i++) {
            $fecha = strtotime ( '+1 day' , strtotime ( $fecha ) ) ;
            $fecha = date ( 'Y-m-d' , $fecha );// aumenta un dia a la fecha

             while(true){
                 if (date("w",strtotime($fecha))==6) { // si el dia de la fecha es sabado
                      $fecha = strtotime ( '+2 day' , strtotime ( $fecha ) ) ;//aumentamos 2 dias hasta el lunes
                      $fecha = date ( 'Y-m-d' , $fecha );
                 }
                 else{
                     if (date("w",strtotime($fecha))==0) {// si el dia de la fecha es domingo
                      $fecha = strtotime ( '+1 day' , strtotime ( $fecha ) ) ;//aumentamos 1 dia hasta el lunes
                      $fecha = date ( 'Y-m-d' , $fecha );
                     }
                 }

                  $fecha2=$fecha;


                           foreach ($feriados as $feriado) {//recorre todos los feriados
                               $fechaFeriado=$feriado->getFecha()->format('Y-m-d');
                               $dataFecha=explode("-",$fecha2);
                               $dataFechaFeriado=explode("-",$fechaFeriado);
                               //switch ($feriado->getTipo()) {// si el feriado es fijo
                                             //case false:// si no es fijo
                                                 /**if($dataFecha[1]==$dataFechaFeriado[1]){//compara los dias de la fecha con feriado
                                                     if($dataFecha[2]==$dataFechaFeriado[2]){//compara los dias de la fecha con feriado
                                                         $fecha2 = strtotime ( '+1 day' , strtotime ( $fecha2 ) ) ;//
                                                          $fecha2 = date ( 'Y-m-d' , $fecha2 );
                                                     }
                                                 }**/
                                                // break;

                                             //case true://si es fijo
                                                 if($fecha2==$fechaFeriado){
                                                         $fecha2 = strtotime ( '+1 day' , strtotime ( $fecha2 ) ) ;
                                                          $fecha2 = date ( 'Y-m-d' , $fecha2 );
                                                 }
                                                // break;
                                        // }
                                }
                        if ($fecha!=$fecha2) {
                          $fecha=$fecha2;
                        }
                        else {
                          break ;
                        }


                   }
        }
        return $fecha;

  }
  public function fechaHoraFin($fechaFin,$cantidad){
          $fechaFin=$fechaFin->format('Y-m-d H:i:s');
          $fechaFin= strtotime ( '+'.$cantidad.''.'hour' , strtotime ( $fechaFin ) ) ;
          $fechaFin = date ( 'Y-m-d H:i:s' , $fechaFin );
          return $fechaFin;
  }

  public function calcularTotalDias($permisos){
      $totalDiasPermiso=0;
      $totalHorasPermiso=0;
      $saldo=0;
      $permisoH=array();
      foreach ($permisos as $permiso) {//itera por cada registro de gestion y suma los dias de permisos
         if($permiso->getTipo()=="D")
         {
             $totalDiasPermiso=$totalDiasPermiso+$permiso->getTiempoLicencia();
         }
         else
         {
             $permisoH[]=$permiso;
         }
      }
      foreach($permisoH as $per){
          $saldo=$saldo+$per->getTiempoLicencia();
      }
      $dias=$saldo/7;
      $dias=round($dias, 0, PHP_ROUND_HALF_DOWN);
      $totalDiasPermiso=$totalDiasPermiso+$dias;

      return $totalDiasPermiso;
  }

  public function cargaHorasPermiso($permisoHoras){

      $dia=0;
      $dias=0;
      $permisosAux=array();
      $permisos=array();
      $saldo=0;
      foreach($permisoHoras as $permiso){

          $dia=$dia+$saldo;
          $saldo=0;
          $diferenciaHoras=7-$dia;
              if($diferenciaHoras>$permiso->getTiempoLicencia()){
                  $dia=$dia+$permiso->getTiempoLicencia();
              }
              else{
                  $dia=7;
                  $saldo=$permiso->getTiempoLicencia()-$diferenciaHoras;
              }
          $permisosAux[]=$permiso;
          if($dia==7){
              $dia=0;
              $dias=$dias+ 1;
              foreach($permisosAux as $per){
                  $permisos[]=$per;
              }
          }
      }

      return $result=array($permisos,$dias);

  }

  public function obtenerDiferencia($fin,$inicio){
      $start=new \DateTime($inicio);
      $end=new \DateTime($fin);
      $diferencia=$start->diff($end)->format('%a');
      return $diferencia;
    }

}

