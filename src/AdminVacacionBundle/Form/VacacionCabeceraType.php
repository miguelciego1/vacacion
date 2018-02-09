<?php

namespace AdminVacacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use AdminVacacionBundle\Form\DataTransformer\EmpleadoToNumberTransformer;
use AdminVacacionBundle\Form\DataTransformer\CentCostoToNumberTransformer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VacacionCabeceraType extends AbstractType
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
        ->add('fechaInicio')
        ->add('empleado', 'genemu_jqueryautocomplete_entity', array(
            'class' => 'Cps\Personal\ArchivoBundle\Entity\Empleado',
            'route_name' => 'buscarEmpleadoAjax',
            'invalid_message' => 'No es valido el codigo de Empleado'))
        ->add('totalDias')
        ->add('centroCostos', 'genemu_jqueryautocomplete_entity', array(
        'class' => 'Cps\Administracion\AdministracionBundle\Entity\Centrocosto',
        'route_name' => 'buscarCentroCostoAjax',
        'invalid_message' => 'No es valido el Centro de Costo'))
        ->add('reemplazo')
        ->add('tipo','choice',array('choices'=>array('N'=>'Normal','R'=>'Radiacion'),'label'=>'Tipo de Gestion'));

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
            'data_class' => 'AdminVacacionBundle\Entity\VacacionCabecera'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'uservacacionbundle_vacacioncabecera';
    }


}
