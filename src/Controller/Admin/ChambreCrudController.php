<?php

namespace App\Controller\Admin;

use App\Entity\Chambre;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ChambreCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Chambre::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('titre', 'Titre'),
            TextareaField::new('descriptionCourte', 'Description courte')->renderAsHtml()->hideOnForm(),
            TextEditorField::new('descriptionCourte', 'Description courte')->onlyOnForms(),
            TextareaField::new('descriptionLongue', 'Description longue')->renderAsHtml()->hideOnForm(),
            TextEditorField::new('descriptionLongue', 'Description longue')->onlyOnForms(),
            ImageField::new('photo')
                ->setBasePath('images/chambres/')
                ->setUploadDir('public/images/chambres')
                ->setRequired(false)
                ->setUploadedFileNamePattern('[ulid].[extension]'),
            IntegerField::new('prixJournalier'),
            //MoneyField::new('prix')->setCurrency('EUR')->setStoredAsCents(false),
            
            //DateTimeField::new('dateEnregistrement', "Date<br>Enregistrement")->setFormat("dd/MM/Y HH:mm:ss"),
            //DateTimeField::new('updatedAt', 'Date de MAJ')->onlyOnForms(),
        ];
    }

    public function createEntity(string $entityFqcn)
    {
        $chambre = new Chambre();
        $chambre->setDateEnregistrement(new \DateTime);
        $chambre->setUpdatedAt(new \DateTime);
        $ifile = $chambre->getImageFile();
        if(!$ifile)
        {
            $chambre->setPhoto('default.png');
        }
        return $chambre;
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // La fonction sera exécuter lors de la creation d'un article avant ADD Article
        $ifile = $entityInstance->getPhoto();

        if(!$ifile)
        {
            $entityInstance->setPhoto('default.jpg');
        }
        $entityInstance->setUpdatedAt(new \DateTime);
        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }    
}
