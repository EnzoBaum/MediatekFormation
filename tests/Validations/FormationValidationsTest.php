<?php

namespace App\Tests\Validations;

use App\Entity\Formation;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Description of FormationValidationsTest
 *
 * @author Enzo Baum
 */
class FormationValidationsTest extends KernelTestCase {
    
    public function getFormation(): Formation {
        return (new Formation())
                ->setTitle("Cours java débutant")
                ->setDescription("")
                ->setPublishedAt(new DateTime("now"));
    }
    
    public function testValidAjoutModifFormation() {
        $formation = $this->getFormation()->setPublishedAt(new DateTime("today"));
        $this->assertErrors($formation, 0);
    }
    
    public function testNonValidAjoutModifFormation() {
        $formation = $this->getFormation()->setPublishedAt(new DateTime("tomorrow"));
        $this->assertErrors($formation, 1, "La date de publication ne peut pas être postérieure a aujourd'hui");
    }
    
    public function assertErrors(Formation $formation, int $nbErreursAttendues, string $message="") {
        self::bootKernel();
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $error = $validator->validate($formation);
        $this->assertCount($nbErreursAttendues, $error, $message);
    }
}