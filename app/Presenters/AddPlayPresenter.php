<?php
declare(strict_types=1);

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

class AddPlayPresenter extends BasePresenter {
    /** @var Connection @inject */
    public Connection $connection;

    public function createComponentAddPlayForm(): Form {
        $form = $this->createForm();
        $form->addText('name', 'Názov inscenácie:')
            ->setRequired('Prosím vyplňte názov inscenácie');
        $form->addText('author', 'Autor:')
            ->setRequired('Prosím vyplňte autora');
        $form->addText('duration', 'Trvanie')
            ->setHtmlType('number')
            ->setRequired('Prosím vyplňte trvanie');
        $form->addUpload('image', 'Obrázok:')
            ->setRequired('Prosím vložte obrázok')
            ->addRule(Form::IMAGE, 'Obrázok musí byť vo formáte JPEG, PNG alebo GIF.')
            ->addRule(Form::MAX_FILE_SIZE, 'Maximálna veľkosť souboru je 1 Mb.',  1024 *1024 /* v bytoch */);
        $form->addTextArea('description', 'Popis:')
            ->setRequired('Prosím vyplňte popis');
        $form->addSubmit('back', 'Zrušiť')
            ->setValidationScope([])
            ->onClick[]= [$this, 'backAddPlayFrom'];
        $form->addSubmit('submit', 'Pridať');
        $form->onSuccess[]=[$this, 'addPlayFormSucceeded'];
        $form = $this->makeBootstrap4($form);
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function backAddPlayFrom():void{
        $this->redirect(':EmployeeHomepage:default');
    }

    /**
     * @param Form $form
     * @throws AbortException
     * @throws UnknownImageFileException
     * @throws ImageException
     */
    public function addPlayFormSucceeded(Form $form): void {
        $values = $form->getValues(TRUE);
        $fileUpload = $values['image'];
        /** @noinspection PhpUndefinedMethodInspection */
        $image = Image::fromFile($fileUpload->getTemporaryFile());
        $image->resize(350, 525, Image::EXACT);
        $directory = str_replace('/', DIRECTORY_SEPARATOR, APP_DIR.'/www/customImages/');
        FileSystem::createDir($directory);
        $imageName= Random::generate(10).'.jpg';
        $image->save($directory.$imageName, 80, Image::JPEG);
        $play = new Play($this->connection);
        $play->createPlay((string)$values['name'], (string)$values['author'], (int)$values['duration'], (String)$values['description'], $imageName);
        $this->flashMessage('Inscenácia bola úspešne pridaná', 'success');
        $this->redirect(':EmployeeHomepage:default');
    }
}