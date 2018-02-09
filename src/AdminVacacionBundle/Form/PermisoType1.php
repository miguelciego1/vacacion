<?php

namespace AdminVacacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityManager;
use AdminVacacionBundle\Form\DataTransformer\EmpleadoToNumberTransformer;
use AdminVacacionBundle\Form\DataTransformer\CentCostoToNumberTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class PermisoType1 extends AbstractType
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
        ->add('fechaInicio', 'datetime' ,array('required'=>true))
        ->add('tiempoLicencia')
        ->add('tipo','choice',array('choices'=>array('HORAS'=>'Hora','DIAS'=>'Dia'),'label'=>'Unidad'))
        ->add('tipoPermiso')
        ->add('centroCostos', 'genemu_jqueryautocomplete_entity', array(
        'class' => 'Cps\Administracion\AdministracionBundle\Entity\Centrocosto',
        'route_name' => 'buscarCentroCostoAjax',
        'invalid_message' => 'No es valido el codigo de Empleado'))
        ->add('empleado', 'genemu_jqueryautocomplete_entity', array(
        'class' => 'Cps\Personal\ArchivoBundle\Entity\Empleado',
        'route_name' => 'buscarEmpleadoAjax',
        'invalid_message' => 'No es valido el Centro de Costo'))
        ->add('motivo','textarea',array('required'=>true));

        $builder
                ->get('empleado')->addModelTransformer(new EmpleadoToNumberTransformer($this->manager));
        $builder
                ->get('centroCostos')->addModelTransformer(new CentCostoToNumberTransformer($this->manager));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserVacacionBundle\Entity\Permiso'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'uservacacionbundle_permiso';
    }


}
