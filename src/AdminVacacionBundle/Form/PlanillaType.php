<?php

namespace AdminVacacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use AdminVacacionBundle\Form\DataTransformer\EmpleadoToNumberTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanillaType extends AbstractType
{
		public $meses = array(
		'January' => 'Enero',
		'February' => 'Febrero',
		'March' => 'Marzo',
		'April' => 'Abril',
		'May' => 'Mayo',
		'June' => 'Junio',
		'July' => 'Julio',
		'August' => 'Agosto',
		'September' => 'Septiembre',
		'October' => 'Octubre',
		'November' => 'Noviembre',
		'December' => 'Diciembre',
		);

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
        $builder
			->add('mes', 'choice', array('choices' => $this->meses, 'label' => 'Mes'))
			->add('gestion', 'choice', array('choices' => array('2017' => '2017'), 'label' => 'Año'))
			->add('opcion', 'choice', array('choices' => array(true => 'Linea', false => 'Excel'), 'label' => 'Opcion'));

    }

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'adminvacacionbundle_planilla';
	}


}
