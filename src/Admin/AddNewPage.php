<?php
declare(strict_types=1);

namespace Itineris\Lottery\Admin;

use Itineris\Lottery\PostTypes\Result;

class AddNewPage
{
    public static function removeSubmenuPage(): void
    {
        remove_submenu_page(
            'edit.php?post_type=' . Result::POST_TYPE,
            'post-new.php?post_type=' . Result::POST_TYPE
        );
    }
}
