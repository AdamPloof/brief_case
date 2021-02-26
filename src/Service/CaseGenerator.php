<?php

namespace App\Service;

use App\Document\CaseFile;
use App\Document\Person;

/**
 * Generates dummy case files using a set of random names, ipsum content, choice of images and video.
 * Create 100 dummy cases
 * 50% shoplifting
 * 10% workplace injury
 * 10% vehicle accident
 * 10% aggressive behavior
 * 5% everything else except
 */
class CaseGenerator
{
    protected $category;
    protected $caseDate;

    public function __construct($category, $caseDate) {
        $this->category = $category;
        $this->caseDate = $caseDate;
    }

    // CaseFile Components
    private function makeDescription() {
        $titles = array(
            "Spitting Hideaway",
            "The Assassin's Promise",
            "Hope Sour",
            "The Scholar's Envoy",
            "An Accidental Lady",
            "Spectre of the Dinosaur",
            "The Cotswold Change",
            "The Wings of Jericho",
            "New of Judgment",
            "The Prophecy of the Season",
            "The Mark of the Stag",
            "Prospero's Seduction",
            "Fearful Opposition",
            "Maverick's Journey",
            "Gravity Stones",
            "Mafia Hungry",
            "The Widows of Steel",
            "A Nurse's Promise",
            "The Prophecy of the Machine",
            "A Courtesan's Honor",
            "The Joker's Jazz",
            "The Peacock and the Dagger",
            "The Magnate's Breath",
            "A Harvest of Mercury",
            "Melancholy Winter",
            "Logan's Firm",
            "Chosen of Silk",
            "Flight Rook",
            "Celebration's Verdict",
            "Point of Wizards",
            "The Beauty of the Mistletoe",
            "The Unintended Enterprise",
            "The Doctor's Nanny",
            "Revelations in Jade",
            "Night of Masks",
            "The Frontier Arms",
            "Crowning Lessons",
            "Meg's Homer",
            "Signs in the Blood",
            "Wedge's Circle",
            "Legend of the Fury",
            "Falconer's Kitten",
            "Green for Mercy",
            "The Istanbul Strikes",
            "The Fate of the Mountain",
            "Fifty Masters",
            "A Ruthless Mirror",
        );

        $modifiers = array(
            "car",
            "shopping cart",
            "meat",
            "sales floor",
            "timeclock",
            "front end",
            "register",
            "customer",
            "employee",
            "break room",
            "produce",
            "bathroom",
            "tuna fish",
            "wellness product",
            "CBD"
        );
        return array_rand($titles . ' - ' . array_rand($modifiers));
    }

    private function makeSummary() {
        // Lorem Ipsum API: https://loripsum.net/api/4/short/headers
        $paras = rand(1, 5);
        $contents = file_get_contents("https://loripsum.net/api/$paras/short/headers");
        return $contents;
    }

    private function getVideo() {
        $videos = array(
            "60289a2b7a387-SampleVideo_1280x720_1mb.mp4",
            "60385cf6704b3-file_example_MP4_480_1_5MG.mp4",
            "602058ebb7bea-robo video.mp4",
            null,
        );
        return array_rand($videos);
    }

    // Person Components
    private function makeName() {
        $names = array(
            "Ty Ayelloribbin",
            "Hugo First",
            "Percy Vere",
            "Jack Aranda",
            "Olive Tree",
            "Fran G. Pani",
            "John Quil",
            "Anne T. Dote",
            "Ev R. Lasting",
            "Anne Thurium",
            "Cherry Blossom",
            "Glad I. Oli",
            "Ginger Plant",
            "Del Phineum",
            "Rose Bush",
            "Perry Scope",
            "Frank N. Stein",
            "Roy L. Commishun",
            "Pat Thettick",
            "Percy Kewshun",
            "Rod Knee",
            "Hank R. Cheef",
            "Perry Scope",
            "Bridget Theriveaquai",
            "Pat N. Toffis",
            "Karen Onnabit",
            "Col Fays",
            "Fay Daway",
            "Joe V. Awl",
            "Frank N. Stein",
            "Wes Yabinlatelee",
            "Colin Sik",
            "Greg Arias",
            "Perry Scope",
            "Perry Scope",
            "Perry Scope",
            "Toi Story",
            "Gene Eva Convenshun",
            "Jen Tile",
            "Simon Sais",
            "Peter Owt",
            "Hugh N. Cry",
            "Lee Nonmi",
            "Lynne Gwafranca",
            "Art Decco",
            "Lynne Gwistic",
            "Frank N. Stein",
            "Polly Ester Undawair",
            "Oscar Nommanee",
            "Laura Biding",
            "Laura Norda",
            "Des Ignayshun",
            "Mike Rowe-Soft",
            "Anne T. Kwayted",
            "Frank N. Stein",
            "Wayde N. Thabalanz",
            "Dee Mandingboss",
            "Sly Meedentalfloss",
            "Stanley Knife",
            "Wynn Dozeaplikayshun",
            "Mal Ajusted",
            "Penny Black",
            "Mal Nurrisht",
            "Polly Pipe",
            "Polly Wannakrakouer",
            "Con Staninterupshuns",
            "Fran Tick",
            "Santi Argo",
            "Carmen Goh",
            "Carmen Sayid",
            "Frank N. Stein",
            "Norma Stitts",
            "Ester La Vista",
            "Manuel Labor",
            "Perry Scope",
            "Ivan Itchinos",
            "Ivan Notheridiya",
            "Mustafa Leek",
            "Emma Grate",
            "Annie Versaree",
            "Tim Midsaylesman",
            "Mary Krismass",
            "Tim “Buck” Too",
            "Lana Lynne Creem",
            "Wiley Waites",
            "Perry Scope",
            "Perry Scope",
            "Ty R. Leeva",
            "Ed U. Cayshun",
            "Anne T. Dote",
            "Claude Strophobia",
            "Anne Gloindian",
            "Anne T. Dote",
            "Dulcie Veeta",
            "Abby Normal",
        );

        return array_rand($names);
    }

    private function makeRole() {
        $roles = array(
            'Ring Leader',
            'Bystander',
            'Perp 1',
            'Suspect',
            'Good Samaritan',
            'Observer',
            'Mark',
            'Person of Interest',
            'Person of Disinterest',
            'Farmer',
            'Attorney',
            'Elephant Trainer',
        );
        return array_rand($roles);
    }

    private function makeTraits() {
        $traits = array(
            'disposition'=> 'sunny',
            'height'=> 'short',
            'hair'=> 'dark',
            'eyes'=> 'brown',
            'laugh'=> 'mirthful',
            'musicality'=> 'harmonious',
            'disposition'=> 'distractable',
            'height'=> 'tall',
            'hair'=> 'long',
            'eyes'=> 'green',
            'laugh'=> 'chuckle',
            'musicality'=> 'swingin',
            'disposition'=> 'languid',
            'height'=> 'medium',
            'hair'=> 'curly',
            'eyes'=> 'pirate patch',
            'laugh'=> 'guffaw',
            'musicality'=> 'atonal',
        );
        $traitsNum = rand(3);

        $traitsStr = '';
        for ($i = 0; $i < $traitsNum; $i++) {
            $traitsStr .= array_rand($traits);
            
            if ($i != $traitsNum - 1) {
                $traitsStr .= ',';
            }
        }

        return $traitsStr;
    }

    private function getImage() {
        $images = array(
            '602b3a27607f5-maggie-mugshot.jpg',
            '60288fb294a5b-krusty-mugshot.gif',
            '602893a050168-homer-mugshot.png',
            null,
        );
        return array_rand($images);
    }

    public function generatePerson() {
        $person = new Person();
        $image = $this->getImage();

        $person->setName($this->makeName());
        $person->setRole($this->makeRole());
        $person->setTraits($this->makeTraits());

        if ($image) {
            $person->setImage($image);
        }

        return $person;
    }

    // Master Assembler
    public function generateCaseFile() {
        $caseFile = new CaseFile();
        $video = $this->getVideo();

        $caseFile->setDescription($this->makeDescription());
        $caseFile->setDate($this->caseDate);
        $caseFile->setCategory($this->category);
        $caseFile->setSummary($this->makeSummary());

        if ($video) {
            $caseFile->setVideo($video);
        }

        $primaryPerson = $this->generatePerson();
        $caseFile->setPrimaryPerson($primaryPerson);
        
        $assocPersonNumber = rand(1, 2);

        for ($i = 0; $i < $assocPersonNumber; $i++) {
            $assocPerson = $this->generatePerson;
            $caseFile->addAssociatedPerson($assocPerson);
        }

        return $caseFile;
    }
}
