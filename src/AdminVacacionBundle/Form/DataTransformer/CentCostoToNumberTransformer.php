<?php  
namespace AdminVacacionBundle\Form\DataTransformer;

use Cps\AdministracionBundle\Entity\CentroCosto;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CentCostoToNumberTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms an object (centcosto) to a string (number).
     *
     * @param  Seccion|null $centcosto
     * @return string
     */
    public function transform($centcosto)
    {
        if (null === $centcosto) {
            return '';
        }

        return $centcosto->getId();
    }

    /**
     * Transforms a string (number) to an object (centcosto).
     *
     * @param  string $centcostoNumber
     * @return Seccion|null
     * @throws TransformationFailedException if object (centcosto) is not found.
     */
    public function reverseTransform($centcostoNumber)
    {
        // no centcosto number? It's optional, so that's ok
        if (!$centcostoNumber) {
            return;
        }

        $centcosto = $this->manager
            ->getRepository('CpsAdministracionBundle:CentroCosto')
            // query for the centcosto with this id
            ->findOneById($centcostoNumber)
        ;

        if (null === $centcosto) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An centcosto with number "%s" does not exist!',
                $centcostoNumber
            ));
        }

        return $centcosto;
    }
}

?>