<?php

namespace AdminVacacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use AdminVacacionBundle\Form\DataTransformer\EmpleadoToNumberTransformer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VacacionGestionDisType extends AbstractType
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
        ->add('gestion')
        ->add('dias','text',array('label'=>'Corresponde'))
        ->add('empleado', 'genemu_jqueryautocomplete_entity', array(
            'class' => 'Cps\Personal\ArchivoBundle\Entity\Empleado',
            'route_name' => 'buscarEmpleadoAjax',
            'invalid_message' => 'No es valido el codigo de Empleado'));        
        $builder->get('empleado')
            ->addModelTransformer(new EmpleadoToNumberTransformer($this->manager));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdminVacacionBundle\Entity\VacacionGestion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'adminvacacionbundle_vacaciongestion';
    }


}
