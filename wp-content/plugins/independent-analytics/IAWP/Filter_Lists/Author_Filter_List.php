<?php

namespace IAWP\Filter_Lists;

/** @internal */
class Author_Filter_List
{
    use \IAWP\Filter_Lists\Filter_List_Trait;
    protected static function fetch_options() : array
    {
        $roles_that_can_edit_posts = [];
        foreach (\wp_roles()->roles as $role_name => $role_obj) {
            if ($role_obj['capabilities']['edit_posts'] ?? \false) {
                $roles_that_can_edit_posts[] = $role_name;
            }
        }
        $authors = \get_users(['role__in' => $roles_that_can_edit_posts]);
        return \array_map(function ($author) {
            return [$author->ID, $author->display_name];
        }, $authors);
    }
}
