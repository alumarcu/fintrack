<?php
namespace Pms\BaseBundle\Exception;

use Symfony\Component\Translation\Translator;

class TranslatedException extends \Exception
{
    public function __construct($errorCode, Translator $translator = null)
    {
        $errorMessage = !is_null($translator) ? $translator->trans($this->messages($errorCode)) : $this->messages($errorCode, 1);

        parent::__construct($errorMessage, $errorCode, null);
    }

    final protected function messages($errorCode, $index = 0)
    {
        $messages = $this->getErrorMessages($this->getTranslationPrefix());

        return $messages[$errorCode][$index];
    }

    protected function getTranslationPrefix()
    {
        return '';
    }

    protected function getErrorMessages($pre = '')
    {
        return array();
    }
}
