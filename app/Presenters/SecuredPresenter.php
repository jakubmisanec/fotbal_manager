<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


class SecuredPresenter extends Nette\Application\UI\Presenter
{

    public function startup() {
        parent::startup();
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect("Auth:Login");
        }
    }

}
