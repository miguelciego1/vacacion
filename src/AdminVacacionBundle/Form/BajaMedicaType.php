<?php

namespace AdminVacacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use AdminVacacionBundle\Form\DataTransformer\EmpleadoToNumberTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BajaMedicaType extends AbstractType
{
    private $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('empleado', 'genemu_jqueryautocomplete_entity', array(
            'class' => 'Cps\Personal\ArchivoBundle\Entity\Empleado',
            'route_name' => 'buscarEmpleadoAjax',
            'invalid_message' => 'No es valido el codigo de Empleado'))
        ->add('desdeEl','text')
        ->add('tipo','choice',array('choices'=>array('E'=>'Enfermedad','R'=>'Riesgo Profesional','A'=>'Accidente de Trabajo','X'=>'Maternidad Total','M'=>'Maternidad Parcial'),'label'=>'Tipo de Baja Medica'))
        ->add('hastaEl','text',array('required'=>true))
        
        
        ;

         $builder
                ->get('empleado')->addModelTransformer(new EmpleadoToNumberTransformer($this->manager));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdminVacacionBundle\Entity\BajaMedica'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'uservacacionbundle_bajamedica';
    }


}
