<?php


namespace App\Presenters;


use App\Model\Play;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Database\Connection;
use Nette\Utils\FileSystem;
use Nette\Utils\Image;
use Nette\Utils\ImageException;
use Nette\Utils\Random;
use Nette\Utils\UnknownImageFileException;
use ReflectionException;

class ModifyPlayPresenter extends BasePresenter {
    private int $idPlay;
    /** @var Connection @inject */
    public Connection $connection;

    public function startup() :void {
        parent::startup();
        $this->idPlay = $this->getParameter('idPlay');
    }

    public function renderDefault (int $idPlay) : void {
        $this->flashMessage('Pokiaľ nenahráte nový obrázok, pôvodný obrázok bude zachovaný.', 'secondary');
    }



    /**
     * @return Form
     * @throws ReflectionException
     */
    public function createComponentModifyPlayForm(): Form {
        $play = new Play($this->connection);
        $result = $play->getPlayDetail($this->idPlay);
        $form = $this->createForm();
        $form->addHidden('idPlay')
            ->setDefaultValue($result->idPlay);
        $form->addText('name', 'Názov inscenácie:')
            ->setDefaultValue($result->name)
            ->setRequired('Prosím vyplňte názov inscenácie');
        $form->addText('author', 'Autor:')
            ->setDefaultValue($result->author)
            ->setRequired('Prosím vyplňte autora');
        $form->addText('duration', 'Trvanie')
            ->setDefaultValue($result->duration)
            ->setHtmlType('number')
            ->setRequired('Prosím vyplňte trvanie');
        $form->addUpload('image', 'Obrázok:')
            ->setDefaultValue(null)
            ->addRule(Form::IMAGE, 'Obrázok musí byť vo formáte JPEG, PNG alebo GIF.')
            ->addRule(Form::MAX_FILE_SIZE, 'Maximálna veľkosť súboru je 1 Mb.',  1024 *1024 /* v bytoch */);
        $form->addTextArea('description', 'Popis:')
            ->setDefaultValue($result->description)
            ->setRequired('Prosím vyplňte popis');
         $form->addSubmit('back', 'Zrušiť')
            ->setValidationScope([])
            ->onClick[]= [$this, 'backModifyPlayFrom'];
        $form->addSubmit('submit', 'Pridať');
        $form->onSuccess[]=[$this, 'modifyPlayFormSucceeded'];
        $form = $this->makeBootstrap4($form);
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function backModifyPlayFrom():void{
        $this->redirect(':SearchPlay:default');
    }

    /**
     * @param Form $form
     * @throws AbortException
     * @throws ImageException
     * @throws UnknownImageFileException
     */
    public function modifyPlayFormSucceeded(Form $form): void {
        $values = $form->getValues(TRUE);
        $play = new Play($this->connection);
        $play->modifyPlay((int)$values['idPlay'], $values['name'], $values['author'], (int)$values['duration'], $values['description']);
        $fileUpload = $values['image'];
        if($fileUpload->isImage() === true) {
            $image = Image::fromFile($fileUpload->getTemporaryFile());
            $image->resize(350, 525, Image::EXACT);
            $dir = str_replace('/', DIRECTORY_SEPARATOR, APP_DIR.'/www/customImages/');
            FileSystem::createDir($dir);
            $imageName= Random::generate(10).'.jpg';
            $image->save($dir.$imageName, 80, Image::JPEG);
            $play->modifyImage($imageName, (int)$values['idPlay']);
        }
        $this->flashMessage('Inscenácia bola úspešne zmenená', 'success');
        $this->redirect(':EmployeeHomepage:default');
    }


}