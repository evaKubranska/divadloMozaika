<?php


namespace App\Presenters;


use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Forms\Controls\Checkbox;

class BasePresenter extends Presenter {


    protected function createForm() : Form{
        return new Form();
    }

    public function makeBootstrap4(Form $form): Form {
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = null;
        $renderer->wrappers['pair']['container'] = 'div class="row row-item justify-content-center row-pair-info"';
        $renderer->wrappers['pair']['.error'] = 'has-danger';
        $renderer->wrappers['control']['container'] = 'div class="form-group col-sx-12 col-md-6"';
        $renderer->wrappers['label']['container'] = 'div class="col-sx-12 col-sm-2 col-form-label label-text"';
        $renderer->wrappers['control']['description'] = 'span class=form-text';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=form-control-feedback';
        $renderer->wrappers['control']['.error'] = 'is-invalid';
        $usedPrimary=false;
        foreach ($form->getControls() as $control) {
            $type = $control->getOption('type');
            if ($type === 'button') {
                $control->getControlPrototype()->addClass($usedPrimary ? 'btn btn-maroon float-right ' : 'btn btn-secondary ');
                $usedPrimary = true;

            } elseif (in_array($type, ['text', 'select'], true)) {
                $control->getControlPrototype()->addClass('form-control');
                $control->getControlPrototype()->setAttribute('rows', '5');
            }elseif ($type === 'textarea') {
                $control->getControlPrototype()->addClass('form-control');
                $control->getControlPrototype()->setAttribute('rows', '5');
            } elseif ($type === 'file') {
                $control->getControlPrototype()->addClass('form-control-file file-label');

            } elseif (in_array($type, ['checkbox', 'radio'], true)) {
                if ($control instanceof Checkbox) {
                    $control->getLabelPrototype()->addClass('form-check-label');
                } else {
                    $control->getItemLabelPrototype()->addClass('form-check-label');
                }
                $control->getControlPrototype()->addClass('form-check-input');
                $control->getSeparatorPrototype()->setName('div')->addClass('form-check');
            }
        }
        return $form;
    }
    public function makeBootstrap4AddShow(Form $form): Form {
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = null;
        $renderer->wrappers['pair']['container'] = 'div class="row justify-content-center row-pair-info"';
        $renderer->wrappers['pair']['.error'] = 'has-danger';
        $renderer->wrappers['control']['container'] = 'div class=col-sm-6';
        $renderer->wrappers['label']['container'] = 'div class="col-sm-2 col-form-label label-text"';
        $renderer->wrappers['control']['description'] = 'span class=form-text';
        $usedPrimary=false;
        foreach ($form->getControls() as $control) {
            $type = $control->getOption('type');
            if ($type === 'button') {
                $control->getControlPrototype()->addClass($usedPrimary ? 'btn btn-maroon float-right ' : 'btn btn-secondary ');
                $usedPrimary = true;

            } elseif (in_array($type, ['text', 'select'], true)) {
                $control->getControlPrototype()->addClass('form-control');
                $control->getControlPrototype()->setAttribute('rows', '5');
            }  elseif (in_array($type, ['checkbox', 'radio'], true)) {
                if ($control instanceof Checkbox) {
                    $control->getLabelPrototype()->addClass('form-check-label select-option');
                } else {
                    $control->getItemLabelPrototype()->addClass('form-check-label  select-option');
                }
                $control->getControlPrototype()->addClass('form-check-input');
                $control->getSeparatorPrototype()->setName('div')->addClass('form-check');
            }
        }
        return $form;
    }
    public function makeBootstrapPersonalInfo(Form $form): Form {
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = null;
        $renderer->wrappers['pair']['container'] = 'div class="row row-personal-info"';
        $renderer->wrappers['control']['container'] = 'div class="col-xs-12 col-sm-6"';
        $renderer->wrappers['label']['container'] = 'div class="col-xs-12 col-sm-2 col-form-label label-text"';
        $renderer->wrappers['control']['description'] = 'span class=form-text';
        $renderer->wrappers['control']['.error'] = 'is-invalid';
        $usedPrimary=false;
        foreach ($form->getControls() as $control) {
            $type = $control->getOption('type');
            if ($type === 'button') {
                $control->getControlPrototype()->addClass($usedPrimary ? 'btn btn-maroon float-right ' : 'btn btn-secondary ');
                $usedPrimary = true;
            } elseif (in_array($type, ['text', 'select'], true)) {
                $control->getControlPrototype()->addClass('form-control');
                $control->getControlPrototype()->setAttribute('rows', '5');
            }elseif ($type === 'textarea') {
                $control->getControlPrototype()->addClass('form-control');
                $control->getControlPrototype()->setAttribute('rows', '5');
            }
        }
        return $form;
    }

    public function makeBootstrapSearch(Form $form): Form {
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = 'div class="row justify-content-center"';
        $renderer->wrappers['pair']['container'] = 'div class="form-group col-sm-12 col-md-5 col-lg-3"';
        $renderer->wrappers['control']['description'] = 'span class=form-text';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=form-control-feedback';
        $renderer->wrappers['control']['.error'] = 'is-invalid';
        $renderer->wrappers['label']['container'] = 'div class="col-form-label label-text"';
        foreach ($form->getControls() as $control) {
            $type = $control->getOption('type');
            if ($type === 'button') {
                $control->getControlPrototype()->addClass('btn btn-maroon');
            } elseif (in_array($type, ['text', 'textarea'], true)) {
                $control->getControlPrototype()->addClass('form-control');
            }else if ($type === 'select'){
                $control->getControlPrototype()->addClass('form-control');
            } elseif ($type === 'file') {
                $control->getControlPrototype()->addClass('form-control-file');

            } elseif (in_array($type, ['checkbox', 'radio'], true)) {
                if ($control instanceof Checkbox) {
                    $control->getLabelPrototype()->addClass('form-check-label');
                } else {
                    $control->getItemLabelPrototype()->addClass('form-check-label');
                }
                $control->getControlPrototype()->addClass('form-check-input');
                $control->getSeparatorPrototype()->setName('div')->addClass('form-check');
            }
        }
        return $form;
    }

    public function makeBootstrapInline(Form $form): Form {
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = 'div class="form-inline justify-content-center"';
        $renderer->wrappers['control']['container'] = 'div class="form-group col"';
        $renderer->wrappers['pair']['container'] = 'div class="row row-item"';
        $renderer->wrappers['control']['description'] = 'span class=form-text';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=form-control-feedback';
        $renderer->wrappers['control']['.error'] = 'is-invalid';
        foreach ($form->getControls() as $control) {
            $type = $control->getOption('type');
            if ($type === 'button') {
                $control->getControlPrototype()->addClass('btn btn-maroon');
            } elseif (in_array($type, ['text', 'textarea'], true)) {
                $control->getControlPrototype()->addClass('form-control');
            }
        }
        return $form;
    }

 /*   public function makeBootstrapInline(Form $form): Form {
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = 'div class="row justify-content-center"';
        $renderer->wrappers['pair']['container'] = 'div class="form-group col-sm-12 col-md-5 col-lg-3"';
        $renderer->wrappers['control']['description'] = 'span class=form-text';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=form-control-feedback';
        $renderer->wrappers['control']['.error'] = 'is-invalid';
        $renderer->wrappers['label']['container'] = 'div class="col-form-label label-text"';
        foreach ($form->getControls() as $control) {
            $type = $control->getOption('type');
            if ($type === 'button') {
                $control->getControlPrototype()->addClass('btn btn-maroon');
            } elseif (in_array($type, ['text', 'textarea'], true)) {
                $control->getControlPrototype()->addClass('form-control col-4');
            }else if ($type === 'select'){
                $control->getControlPrototype()->addClass('form-control col-4');
            } elseif ($type === 'file') {
                $control->getControlPrototype()->addClass('form-control-file');

            } elseif (in_array($type, ['checkbox', 'radio'], true)) {
                if ($control instanceof Checkbox) {
                    $control->getLabelPrototype()->addClass('form-check-label');
                } else {
                    $control->getItemLabelPrototype()->addClass('form-check-label');
                }
                $control->getControlPrototype()->addClass('form-check-input');
                $control->getSeparatorPrototype()->setName('div')->addClass('form-check');
            }
        }
        return $form;
    }*/

    public function makeBootstrapSearchNav (Form $form): Form {
        $renderer = $form->getRenderer();
        $renderer->wrappers['control']['description'] = 'form-inline mx-auto';
        foreach ($form->getControls() as $control) {
            $type = $control->getOption('type');
            if ($type === 'button') {
                $control->getControlPrototype()->addClass('btn btn-rounded btn-sm my-0 ml-sm-2');
            } elseif (in_array($type, ['text', 'textarea'], true)) {
                $control->getControlPrototype()->addClass('form-control col-md-4');
            }
        }
        return $form;
    }
}