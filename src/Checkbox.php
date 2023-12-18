<?php

namespace Aspectus\Components\Input;

use Aspectus\Component;
use Aspectus\Components\Input\View\CheckboxView;
use Aspectus\Message;

class Checkbox implements Component
{
    private bool $checked = false;

    public function __construct(
        private $view = new CheckboxView()
    ) {
    }

    public function isChecked(): bool
    {
        return $this->checked;
    }

    public function toggle(): void
    {
        $this->checked = !$this->checked;
    }

    public function view(): string
    {
        return $this->view->render($this->checked);
    }

    public function update(?Message $message): ?Message
    {
        return null;
    }
}