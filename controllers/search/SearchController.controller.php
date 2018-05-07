<?php

	class SearchController extends Controller {
	
		public static function search($request, $vars) {
			$page = $vars['page_number'];
			$searchText = $vars['search_text'];
			$lang = Application::$storage['lang'];
			$limit = 10;
			$start = $page * $limit;
			$data = SearchModel::searchText($searchText, $lang, $start, $limit);
			
			$paginator = Utils::generatePaginator($data['count'], $limit, $page);
			
			self::renderTemplate('search' . ds . 'search.tpl', Array(
				'foundData' => $data['data'],
				'csrf_key' => Application::getCSRFKey(),
				'paginator' => $paginator,
				'searchText' => $searchText,
				'currentPage' => $page,
			));
		}
	
	}

?>