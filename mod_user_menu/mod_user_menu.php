<?php
/**
* @package User Menu
* @copyright (C) 2013 NCFJCJ. All rights reserved.
*/

// no direct access
defined('_JEXEC') or die;

// require the helper
require_once __DIR__ . '/helper.php';

// variables
$list		= ModUserMenuHelper::getList($params);
$base		= ModUserMenuHelper::getBase($params);
$active		= ModUserMenuHelper::getActive($params);
$active_id 	= $active->id;
$path		= $base->tree;
$showAll	= $params->get('showAllChildren');
$class_sfx	= htmlspecialchars($params->get('class_sfx'));
?>
<div class="row-fluid">
	<div class="span12" style="border-right: 1px solid #E5E5E5;">
		<div id="userMenuHead">
			<span class="icomoon-user" style="font-size: 45pt; float: left; margin-right: 5px;"></span>
			<div id="userInfo">
				<p>
					<span style="font-weight: bold;">
					<?php
						$user = JFactory::getUser();
						echo $user->name;	
					?>
					</span>
					<br>
					<a href="/my-account/profile.html" style="font-size: 12px;">My Profile</a>
				</p>
			</div>
		</div>
		<div>
			<?php if( count( $list ) ): ?>
			<ul class="nav nav-list menu<?php echo $class_sfx;?>"<?php 
				$tag = '';
				if($params->get('tag_id') != null ) {
					$tag = $params->get('tag_id') . '';
					echo ' id="' . $tag . '"';
				}
				?>>
				<?php foreach ( $list as $i => &$item ) : 
					if($item->alias == 'profile'){
						continue;
					}
					$class = 'item-'.$item->id;
					if ($item->id == $active_id){
						$class .= ' current';
					}
					if ( in_array( $item->id, $path ) ) {
						$class .= ' active';
					} elseif ( $item->type == 'alias' ){
						$aliasToId = $item->params->get( 'aliasoptions' );
						if ( count( $path ) > 0
						&& $aliasToId == $path[count( $path ) - 1] ){
							$class .= ' active';
						} elseif ( in_array( $aliasToId, $path ) ) {
							$class .= ' alias-parent-active';
						}
					}
					if ( $item->type == 'separator' ) {
						$class .= ' divider';
					}
					if ( $item->type == 'heading' ) {
						$class .= ' nav-header';
					}
					if ( $item->deeper ) {
						$class .= ' deeper';
					}
					if ( $item->parent ){
						$class .= ' parent';
					}
					if ( !empty( $class ) ){
						$class = ' class="' . trim( $class ) . '"';
					}
					
					// render the list item witht he appropriate class
					echo '<li'.$class.'>';
					
					switch ($item->type) :
						case 'separator':
						case 'url':
						case 'component':
						case 'heading':
							require JModuleHelper::getLayoutPath('mod_menu', 'default_'.$item->type);
							break;
						default:
							require JModuleHelper::getLayoutPath('mod_menu', 'default_url');
							break;
					endswitch;	
					
					// The next item is deeper.
					if ( $item->deeper ) {
						echo '<ul class="nav-child unstyled small">';
					}
					// The next item is shallower.
					elseif ( $item->shallower ) {
						echo '</li>';
						echo str_repeat('</ul></li>', $item->level_diff);
					}
					// The next item is on the same level.
					else {
						echo '</li>';
					}
				endforeach; ?>
			</ul>
			<?php endif; ?>
		</div>
	</div>
</div>