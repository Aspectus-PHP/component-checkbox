<?php

namespace Aspectus\Components\Input\Checkbox;

use Aspectus\Component;
use Aspectus\Message;

class Checkbox implements Component
{
    private bool $checked = false;

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
        return '[' . ($this->checked ? '*' : ' ') .  ']';
    }

    public function update(?Message $message): ?Message
    {
        return null;
    }
}