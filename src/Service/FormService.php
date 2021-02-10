<?php

namespace App\Service;

use Symfony\Component\Form\FormInterface;

class FormService
{

    /**
     * Get errors message linear array with form ID key
     *
     * @param FormInterface $form
     * @return void
     */
    // See: https://symfonycasts.com/screencast/symfony-rest2/validation-errors-response
    public function getErrorsByIds(FormInterface $form, $prefix)
    {
        $errorsByIds = [];
        $errors = $form->getErrors(true, true);
        foreach ($errors as $error) {
            if (is_object($error)) {
                $message = $error->getMessage();
                $originForm = $error->getOrigin();
                $parent = $originForm->getParent();
                $grandParent = $parent->getParent();
                if ($grandParent) {
                    $errorsByIds[$prefix . '_' . $grandParent->getName() . '_' . $parent->getName()] = $message;
                } else {
                    $errorsByIds[$prefix . '_' . $originForm->getName()] = $message;
                }
            }
        }

        return $errorsByIds;
    }
}
