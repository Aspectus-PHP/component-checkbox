<?php

namespace Aspectus\Components\Input\View;

final class CheckboxView
{
    public function __construct(
        readonly public string $left = '[',
        readonly public string $checked = '*',
        readonly public string $unchecked = ' ',
        readonly public string $right = ']',
        readonly public string $caption = ''
    ) {
    }

    public function render(bool $toggled): string
    {
        return $this->left . ($toggled ? $this->checked : $this->unchecked) . $this->right . $this->caption;
    }
}