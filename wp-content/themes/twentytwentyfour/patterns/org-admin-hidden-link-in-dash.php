<?php
/**
 * Title: Organisation hidden link in Dashboard
 * Slug: twentytwentyfour/org-admin-hidded-link-in-dash
 * Categories: query
 * Keywords: hide link if not admin
 * Block Types: core/layout
 */
$id = get_current_user_id();

                                    if(user_can($id, 'editor')){
                                    ?>
                                        <li class="dash-admin-only">
                                            <a class="sidenav-item-link" href="/add-new-organization">
                                                <span class="nav-text">Add New Organization</span>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                    ?>