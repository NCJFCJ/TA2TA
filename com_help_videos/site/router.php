<?php

/**
 * Routing class from com_help_videos
 *
 * @package     Joomla.Site
 * @subpackage  com_help_videos
 */
class Help_videosRouter extends JComponentRouterBase{
	/**
	 * Build the route for the com_help_videos component
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   3.3
	 */
	public function build(&$query){
		//must transform an array of URL parameters into an array of segments that will form the SEF URL
		$segments = array();
	  
	  // get rid of the blasted view
	  unset($query['view']);

	  $db = JFactory::getDbo();

		if(isset($query['catid'])){
			// retrieve the category alias
			$dbquery = $db->getQuery(true);
			$dbquery->select($db->quoteName('alias'));
			$dbquery->from($db->quoteName('#__help_videos_categories'));
			$dbquery->where($db->quoteName('id') . '=' . (int) $query['catid']);
			$db->setQuery($dbquery);
			$alias = $db->loadResult();

			$segments[] = $alias;
			unset($query['catid']);
		}
		if(isset($query['id'])){
			// retrieve the video alias
			$dbquery = $db->getQuery(true);
			$dbquery->select($db->quoteName('alias'));
			$dbquery->from($db->quoteName('#__help_videos'));
			$dbquery->where($db->quoteName('id') . '=' . (int) $query['id']);
			$db->setQuery($dbquery);
			$alias = $db->loadResult();

			$segments[] = $alias;
			unset($query['id']);
		}

		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   3.3
	 */
	public function parse(&$segments){
		// must transform an array of segments back into an array of URL parameters
		$vars = array();
		$vars['view'] = 'video';
		if(count($segments)){
			// retrieve the category id
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->quoteName('id'));
			$query->from($db->quoteName('#__help_videos_categories'));
			$query->where($db->quoteName('alias') . '=' . $db->quote($segments[0]));
			$db->setQuery($query);
			$id = $db->loadResult();

		  $vars['catid'] = $id; 
		  
		  if(count($segments) > 1){
				// retrieve the video id
				$query = $db->getQuery(true);
				$query->select($db->quoteName('id'));
				$query->from($db->quoteName('#__help_videos'));
				$query->where($db->quoteName('alias') . '=' . $db->quote($segments[1]));
				$db->setQuery($query);
				$id = $db->loadResult();

	  		$vars['id'] = $id;
	  	}
	  }

		return $vars;
	}
}

/**
 * Content router functions
 *
 * These functions are proxys for the new router interface
 * for old SEF extensions.
 *
 * @deprecated  4.0  Use Class based routers instead
 */
function Help_videosBuildRoute(&$query){
	$router = new Help_videosRouter;

	return $router->build($query);
}

function Help_videosParseRoute($segments){
	$router = new Help_videosRouter;

	return $router->parse($segments);
}
