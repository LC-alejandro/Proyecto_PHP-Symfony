<?php

namespace App\Repository;

use App\Entity\Imagenes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Imagenes>
 *
 * @method Imagenes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Imagenes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Imagenes[]    findAll()
 * @method Imagenes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImagenesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Imagenes::class);
    }

    public function add(Imagenes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function mostrarTodasImagenes($categoria){

        $consulta = $this->getEntityManager()->createQuery(
            'SELECT i.id,i.nombre,i.descripcion,i.numVisualizaciones,i.numLikes,i.numDownloads FROM
                App:Imagenes i WHERE i.categoria = '.$categoria);
        $resultado = $consulta->getResult();
        return $resultado;
    }
    public function remove(Imagenes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    /*
    public function aumentarNumImagenes(Imagenes $imagenGaleria){
        $categoria = $this->getCategoria($imagenGaleria);
        $categoriaRepository = new CategoriasRepository();
        $categoriaRepository->nuevaImagen($categoria);
    }*/


//    /**
//     * @return Imagenes[] Returns an array of Imagenes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Imagenes
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
