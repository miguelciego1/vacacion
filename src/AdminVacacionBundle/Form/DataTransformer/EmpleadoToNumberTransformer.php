<?php  
namespace AdminVacacionBundle\Form\DataTransformer;

use Cps\Personal\ArchivoBundle\Entity\Empleado;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EmpleadoToNumberTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms an object (empleado) to a string (number).
     *
     * @param  Empleado|null $empleado
     * @return string
     */
    public function transform($empleado)
    {
        if (null === $empleado) {
            return '';
        }

        return $empleado->getId();
    }

    /**
     * Transforms a string (number) to an object (empleado).
     *
     * @param  string $empleadoNumber
     * @return Empleado|null
     * @throws TransformationFailedException if object (empleado) is not found.
     */
    public function reverseTransform($empleadoNumber)
    {
        // no empleado number? It's optional, so that's ok
        if (!$empleadoNumber) {
            return;
        }

        $empleado = $this->manager
            ->getRepository('CpsPerArchivoBundle:Empleado')
            // query for the empleado with this id
            ->findOneById($empleadoNumber)
        ;

        if (null === $empleado) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An empleado with number "%s" does not exist!',
                $empleadoNumber
            ));
        }

        return $empleado;
    }
}

?>