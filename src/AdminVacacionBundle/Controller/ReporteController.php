<?php

namespace AdminVacacionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AdminVacacionBundle\Entity\VacacionCabecera;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AdminVacacionBundle\Entity\VacacionDetalle;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;
use AdminVacacionBundle\Form\VacacionCabeceraType;
use PHPExcel_Style_Alignment;

/**
 * reporte controller.
 *
 * @Route("/reporte")
 */
class ReporteController extends Controller
{
    /**
     * Formulario de reporte para generar reporte.
     *
     * @Route("/", name="reporte_index1")
     * @Method({"GET", "POST"})
     */
    public function index1Action()
    {
        return $this->render('reporte/index.html.twig');
    }

    /**
     * Formulario de reporte para generar reporte.
     *
     * @Route("/index/", name="reporte_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        // ==================================crea el formulario del reporte vacacion======================================================
        // ======================================================================================================================
        $reporte=array('fechaInicio'=>null,'fechaFin'=>null,'empleadoId'=>'','seleccion'=>'','seleccionCampo'=>'');
        $form = $this->createForm('AdminVacacionBundle\Form\ReporteType',$reporte);
        $form->handleRequest($request);
        //=====================================REPORTE DE CORRELATIVO=======================================
        $reporte2=array('idInicio'=>null,'idFin'=>null);
        $form2 = $this->createForm('AdminVacacionBundle\Form\ReporteCorrelativoType',$reporte2);
        $form2->handleRequest($request);

          if ($form->isSubmitted() && $form->get('save')->isClicked()) {
                    $em=$this->getDoctrine()->getManager();
                    $data=$form->getData();

                    $fecIni=$data['fechaInicio'];
                    $fecFin=$data['fechaFin'];

                    
                    $empleadoId=$data['empleadoId'];


                    $fecIni=$fecIni->format('Y-m-d');
                    $fecFin=$fecFin->format('Y-m-d');

                    switch ($data['seleccion']) {
                      case 'V':
                        if ($data['seleccionCampo']==1) {
                          $vacacionCabeceras=$em->getRepository('AdminVacacionBundle:VacacionCabecera')->findByFechas($fecIni,$fecFin,$empleadoId);
                        }else{
                            $vacacionCabeceras=$em->getRepository('AdminVacacionBundle:VacacionCabecera')->findByFechasRegistro($fecIni,$fecFin);
                        }
                       
                        //pdf----------------------------------------------
					  	if ($data['formato']){
							$html = $this->renderView('AdminVacacionBundle:reporte:reporteVacacion.html.twig', array(
								'vacacionCabeceras'=>$vacacionCabeceras,'fIni'=>$fecIni,'fFin'=>$fecFin
							));
							$filename = sprintf('ReporteVacacion-%s.pdf', date('Y-m-d'));
						}else{
                        	$html=$vacacionCabeceras;
						}

                        break;
                      
                      case 'P':
                        $permisos=$em->getRepository('AdminVacacionBundle:Permiso')->findByFechas($fecIni,$fecFin,$empleadoId);
                        $html = $this->renderView('AdminVacacionBundle:reporte:reportePermiso.html.twig', array(
                        'fIni'=>$fecIni,'fFin'=>$fecFin,
                              'permisos'=>$permisos
                          ));
                        $filename = sprintf('ReportePermisos-%s.pdf', date('Y-m-d'));
                        break;

                      case 'B':
                        if ($data['seleccionCampo']) {
                          $bajamedicas=$em->getRepository('AdminVacacionBundle:BajaMedica')->findByFechas($fecIni,$fecFin,$empleadoId);
                        }else{
                            $bajamedicas=$em->getRepository('AdminVacacionBundle:BajaMedica')->findByFechasRegistro($fecIni,$fecFin);
                        }

                          //pdf----------------------------------------------
						  if ($data['formato']){
							  $html = $this->renderView('AdminVacacionBundle:reporte:reporteBajaMedica.html.twig', array(
								  'bajamedicas'=>$bajamedicas,'fIni'=>$fecIni,'fFin'=>$fecFin,'id'=>$empleadoId
							  ));
							  $filename = sprintf('ReporteBajaMedicas-%s.pdf', date('Y-m-d'));
						  }else{
							  $html=$bajamedicas;
						  }



                        break;
                    }

                    if ($data['formato']){
						$response= new Response(
							$this->get('knp_snappy.pdf')->getOutputFromHtml($html,
								array('lowquality' => false,
									'encoding' => 'utf-8',
									'page-size' => 'Letter',
									'outline-depth' => 8,
									'orientation' => 'Portrait',
									'title'=> 'Reporte Vacacion',
									'header-font-size'=>7
								)),
							200,
							array(
								'Content-Type'        => 'application/pdf',
								'Content-Disposition' => sprintf('attachment ; filename="%s"', $filename),
							)
						);
						return $response;
					}else{
						$response=$this->generarExcel($html,$fecIni,$fecFin,$data['seleccion']);
						return $response;
					}

                }

          if ($form2->isSubmitted() && $form2->get('save2')->isClicked()) {
                    $em=$this->getDoctrine()->getManager();
                    $data=$form2->getData();

                    $fecIni=$data['idInicio'];
                    $fecFin=$data['idFin'];

                      $bajamedicas=$em->getRepository('AdminVacacionBundle:BajaMedica')->findByCorrelativo($fecIni,$fecFin);

                    //pdf----------------------------------------------
                    $html = $this->renderView('AdminVacacionBundle:reporte:reporteBajaMedica.html.twig', array(
                        'bajamedicas'=>$bajamedicas,'fIni'=>$fecIni,'fFin'=>$fecFin
                    ));

                    $filename = sprintf('ReporteBajaMedicas-%s.pdf', date('Y-m-d'));
                       
                        
                    $response= new Response(
                            $this->get('knp_snappy.pdf')->getOutputFromHtml($html,
                            array('lowquality' => false,
                                    'encoding' => 'utf-8',
                                    'page-size' => 'Letter',
                                    'outline-depth' => 8,
                                    'orientation' => 'Portrait',
                                    'title'=> 'Reporte Bajas Medicas',
                                    'header-font-size'=>7
                                    )),
                            200,
                            array(
                                'Content-Type'        => 'application/pdf',
                                'Content-Disposition' => sprintf('attachment ; filename="%s"', $filename),
                            )
                        );
                        return $response;

                }


        return $this->render('AdminVacacionBundle:reporte:index.html.twig',array('form'=>$form->createView(),'form2'=>$form2->createView()));
    }


    public function generarExcel($items,$fechaIni,$fechaFin,$tipo){
		// solicitamos el servicio 'phpexcel' y creamos el objeto vacío...
		$phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

		// ...y le asignamos una serie de propiedades
		$phpExcelObject->getProperties()
			->setCreator("Erlan")
			->setLastModifiedBy("Erlan")
			->setTitle("Reporte")
			->setSubject("Sistema de vacacion y bajas medicas")
			->setDescription("Reporte solicitado");

		// establecemos como hoja activa la primera, y le asignamos un título
		$phpExcelObject->setActiveSheetIndex(0);
		if ($tipo=='V')
		{
			$phpExcelObject->getActiveSheet()->setTitle('REPORTE VACACION');
			$phpExcelObject->setActiveSheetIndex(0)
				->setCellValue('A1', 'REPORTE DE VACACION');
			$phpExcelObject->setActiveSheetIndex(0)
				->setCellValue('A3', 'CORRELATIVO')
				->setCellValue('B3', 'COD. EMPLEADO')
				->setCellValue('C3', 'NOMBRE EMPLEADO')
				->setCellValue('D3', 'CARGO')
				->setCellValue('E3', 'FECHA INICIO')
				->setCellValue('F3', 'FECHA FIN')
				->setCellValue('G3', 'DIAS ');

			$fila = 4;
			foreach ( $items as $item) {
				if ($item->getEmpleado()->getAuxEmpleado())
				{
					$cargo=$item->getEmpleado()->getAuxEmpleado()->getCargo();
				}else{
					$cargo='----';
				}
				$phpExcelObject->setActiveSheetIndex(0)
					->setCellValue('A' . $fila, $item->getId())
					->setCellValue('B' . $fila, $item->getEmpleado()->getId())
					->setCellValue('C' . $fila, $item->getEmpleado())
					->setCellValue('D' . $fila, $cargo)
					->setCellValue('E' . $fila, $item->getFechaInicio()->format('d-m-Y'))
					->setCellValue('F' . $fila, $item->getFechaFin()->format('d-m-Y'))
					->setCellValue('G' . $fila, $item->getTotalDias());

				$fila = $fila + 1;
			}
		}
		elseif($tipo=='B'){
			$phpExcelObject->getActiveSheet()->setTitle('REPORTE BAJAS MEDICAS');
			$phpExcelObject->setActiveSheetIndex(0)
				->setCellValue('A1', 'REPORTE DE BAJAS MEDICAS');
			$phpExcelObject->setActiveSheetIndex(0)
				->setCellValue('A3', 'CORRELATIVO')
				->setCellValue('B3', 'COD. EMPLEADO')
				->setCellValue('C3', 'NOMBRE EMPLEADO')
				->setCellValue('D3', 'FECHA INICIO')
				->setCellValue('E3', 'FECHA FIN')
				->setCellValue('F3', 'FECHA REGISTRO')
				->setCellValue('G3', 'USUARIO ');

			$fila = 4;
			foreach ( $items as $item) {
				$phpExcelObject->setActiveSheetIndex(0)
					->setCellValue('A' . $fila, $item->getId())
					->setCellValue('B' . $fila, $item->getEmpleado()->getId())
					->setCellValue('C' . $fila, $item->getEmpleado())
					->setCellValue('D' . $fila, $item->getDesdeElObject()->format('d-m-Y'))
					->setCellValue('E' . $fila, $item->getHastaElObject()->format('d-m-Y'))
					->setCellValue('F' . $fila, $item->getCreadoEl()->format('d-m-Y'))
					->setCellValue('G' . $fila, $item->getUsuario());

				$fila = $fila + 1;
			}
		}


		// establecemos stylos por defecto y a celdas distintas
		$phpExcelObject->getDefaultStyle()->getFont()->setName('Arial')
			->setSize(9);

		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('A1:G1');
		$phpExcelObject->getActiveSheet()->getStyle('A1')->applyFromArray(array('font' => array('name' => 'Arial', 'bold' => true, 'size' => 10, 'underline' => true), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)));

		$phpExcelObject->getActiveSheet()->getStyle('A3:G3')->applyFromArray(array('font' => array('name' => 'Arial', 'bold' => true, 'size' => 10), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)));

		$phpExcelObject->getActiveSheet()->getStyle('A2')->applyFromArray(array('font' => array('name' => 'Arial', 'bold' => true, 'size' => 10)));
		$phpExcelObject->getActiveSheet()->getStyle('C2')->applyFromArray(array('font' => array('name' => 'Arial', 'bold' => true, 'size' => 10)));
		$phpExcelObject->getActiveSheet()->getStyle('E2')->applyFromArray(array('font' => array('name' => 'Arial', 'bold' => true, 'size' => 10)));

		// fijamos un ancho a las distintas columnas

		$phpExcelObject->setActiveSheetIndex(0)
			->getColumnDimension('A')
			->setWidth(13);
		$phpExcelObject->setActiveSheetIndex(0)
			->getColumnDimension('B')
			->setWidth(17);
		$phpExcelObject->setActiveSheetIndex(0)
			->getColumnDimension('C')
			->setWidth(30);
		$phpExcelObject->setActiveSheetIndex(0)
			->getColumnDimension('D')
			->setWidth(25);
		$phpExcelObject->setActiveSheetIndex(0)
			->getColumnDimension('E')
			->setWidth(13);
		$phpExcelObject->setActiveSheetIndex(0)
			->getColumnDimension('F')
			->setWidth(13);
		$phpExcelObject->setActiveSheetIndex(0)
			->getColumnDimension('G')
			->setWidth(7);

		$phpExcelObject->setActiveSheetIndex(0)
			->setCellValue('A1', 'REPORTE DE VACACION')
			->setCellValue('A2', 'CANTIDAD')
			->setCellValue('B2', count($items))
			->setCellValue('C2', 'FECHA INICIO')
			->setCellValue('D2', $fechaIni)
			->setCellValue('E2', 'FECHA FIN')
			->setCellValue('F2', $fechaFin);







		$hoy = date('d-m-Y');
		// se crea el writer
		$writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
		// se crea el response
		$response = $this->get('phpexcel')->createStreamedResponse($writer);
		// y por último se añaden las cabeceras
		$dispositionHeader = $response->headers->makeDisposition(
			ResponseHeaderBag::DISPOSITION_ATTACHMENT,
			'Reporte' . $hoy . '.xlsx'
		);
		$response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'maxage=1');
		$response->headers->set('Content-Disposition', $dispositionHeader);

		return $response;
	}


    public function calcularTotalDiasSuspendida($suspendidas){
        $totalDiasSuspendida=0;
        foreach($suspendidas as $suspendida){
            $totalDiasSuspendida=$totalDiasSuspendida+$suspendida->getDias();
        }

        return $totalDiasSuspendida;
    }

}
