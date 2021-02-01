<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Security\Passwords;
use Nette\Application\UI\Form as Form;


final class AuthPresenter extends Nette\Application\UI\Presenter
{
    
    private $database;
    private $passwords;
    
    public function __construct(Nette\Database\Explorer $database, Passwords $passwords) {
        $this->database = $database;
        $this->passwords = $passwords;
    }
    
    public function actionDefault(): void {
        if ($this->getUser()->getId() != null) {
            $this->redirect("Homepage:");
            return;
        }
        
        $this->redirect("Auth:login");
     
    }
    
    
    public function actionLogout() {
        if ($this->getUser()->getId()) {
            $this->getUser()->logout(true);
            $this->flashMessage("Úspěšně odhlášen.", "primary");
        }
        
        $this->redirect("Auth:login");
    }
    
    /**
     *  COMPONENTS
     */
    
    protected function createComponentLoginForm(): Form {
        $form = new Form();
        
        $form->addText("username", "Uživatelské jméno:")
                ->setRequired();
        $form->addPassword("password", "Heslo:")
                ->setRequired();
        
        $form->addSubmit("login", "Přihlásit se")->setOption("class", "btn-block");
        
        $form->onSuccess[] = [ $this, "loginFormSend" ];
        
        return $form;
    }
    
    
    public function loginFormSend(Form $form, $data): void {
        try {
            $this->getUser()->login($data->username, $data->password);
            
            $this->flashMessage("Úspěšně přihlášen.", "primary");
            $this->redirect("Homepage:");
        } catch (Nette\Security\AuthenticationException $ex) {
            $this->flashMessage($ex->getMessage(), "danger");
            $this->redirect("Auth:Login");
        }
    }
    
            
            
            
           

}
