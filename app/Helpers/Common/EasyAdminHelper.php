<?php

namespace App\Helpers\Common;

class EasyAdminHelper {
    public static function replaceActionButton(array $buttons, string $find, string $replacement)
    {
        if (($key = array_search($find, $buttons)) !== false) {
            unset($buttons[$key]);
            $buttons[] = $replacement;
        }

        return $buttons;
    }
}