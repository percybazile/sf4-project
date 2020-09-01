<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Classe "modèle" pour les fixtures
 * On ne peut pas instancier une abstraction
 */
abstract class BaseFixture extends Fixture 
{
/** @var ObjectManager */
    private $manager;

/** @var Generator */
    protected $faker;

/** @var array liste des références connues (cf.memoïsation) */
    private $references = [];

    /**
     * Méthode à implémenter par les classes qui héritent
     * pour générer les fausses données
     */
    abstract protected function loadData();

      /**
     * Méthode appelée par le système de fixtures
     */
    public function load(ObjectManager $manager)
    {
        // On enregistre le ObjectManager
        $this->manager = $manager;
        // On instancie Faker
        $this->faker = Factory::create('fr_FR');

        // On appelle loadData() pour avoir les fausses données
        $this->loadData();
        // On exécute l'enregistrement en base
        $this->manager->flush();
    }

    /**
     * Enregistrer plusieurs entités
     * @param int $count nombre d'entité à générer
     * @param string $groupName nom du groupe de références
     * @param callable $factory fonction qui génére 1 entité
     */
    protected function createMany(int $count, string $groupName, callable $factory) 
    {
        for ($i = 0; $i < $count; $i++){
            // on execute $factory qui doit retourner l'entité generée
            $entity = $factory($i);

            // Vérifier que l'entité ai été retournée
            if ($entity === null) {
                throw new \LogicException('l\'entité doit être retournée. Auriez-vous oublié un "return" ?');
            }
          // On prépare à l'enregistrement de l'entité
          $this->manager->persist($entity);
          
          // On enregistre une références a l'entité
          $reference = sprintf('%s_%d', $groupName, $i);
          $this->addReference($reference, $entity);
        }    
    }


    /**
     *  Récuperer 1 entité par son nom de groupe de références
     * @param string $groupName nom du groupe de réfèrences    
     */

     protected function getRandomReference(string $groupName)
     {
        // Vérifier si on a déjà enregistrer  les réfèrences du groupe demandé
        if(!isset($this->references[$groupName]))
        // Si non, On va chercher les réfèrences
        $this->references[$groupName] = [];
        // On parcourt la liste de toutes les réfèrences(toutes classes confondues)
        foreach($this->referenceRepository->getReferences() as $key =>$ref){
            // la clé $key correspond à nos réfèrences
            // Si $key commence par le nom de groupe demandé, on le sauvegarde
            if(strpos($key, $groupName) === 0) {
                $this->references[$groupName][] = $key;
            }
        }
     

     // Vérifier que l'ona récuperer des réfèrences 
     if ($this->references[$groupName] === []) {
         throw new \Exception(sprintf('Aucune réfèrence trouvée pour le groupe "%s"', $groupName));
     }
   // Retourner une entité correspondant à une réfèrence aléatoire
   $randomReference = $this->faker->randomElement($this->references[$groupName]);
   return $this->getReference($randomReference);
    }
}