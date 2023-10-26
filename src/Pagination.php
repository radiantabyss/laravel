<?php
namespace Lumi\Core;

class Pagination
{
	/*properties = Object(
		page - current page
		pages - number of total pages
		// pagesLeft - number pages to be shown on the left of the current page ( pagesLeft == 0 && pagesRight == 0 => all pages will be displayed )
		// pagesRight - number pages to be shown on the right of the current page ( pagesLeft == 0 && pagesRight == 0 => all pages will be displayed )
		url
		hashtag
	);
	*/
	protected $properties;
	public $html;

	public function __construct($properties = []) {
		$this->properties = new \stdClass;

		$defaults = [
			'pagesLeft' => 3,
			'pagesRight' => 3,
			'pageName' => 'page',
		];

        $properties = array_merge($defaults, $properties);

		if ( $properties ) {
			$this->setProperties($properties);
		}
	}

	public function setProperties($properties) {
		foreach ( $properties as $key => $value ) {
			$this->properties->$key = $value;
		}

        $this->properties->url .= '?';
        foreach ( $this->properties->params as $key => $value ) {
            if ( $value === '' ) continue;
            if ( in_array($key, ['page', 'per_page', '_url']) ) continue;

            $this->properties->url .= $key.'='.$value.'&';
        }
	}

	public function write() {
		if ( $this->properties->pages < 2 ) {
            $this->html = '';
            return $this->html;
        }

		$this->html = $this->render([
            'url' => $this->properties->url,
            'pagesLeft' => $this->properties->pagesLeft,
            'pagesRight' => $this->properties->pagesRight,
            'page' => $this->properties->page,
            'pages' => $this->properties->pages,
			'pageName' => $this->properties->pageName,
        ]);

		return $this->html;
	}

	private function render($params) {
		extract($params);

		$start = $page - $pagesLeft;
		$end = $page + $pagesRight;

		if ( $start < 1 ) {
		    $start = 1;
		}
		if ( $end > $pages ) {
		    $end = $pages;
		}

		if ( $pages > ($pagesLeft + $pagesRight + 1) ) {
		    if ( $end < ($pagesLeft + $pagesRight + 1) ) {
		        $end = ($pagesLeft + $pagesRight + 1);
		    }

		    if ( ($pages - $page) < 3 ) {
		        $start = $pages - ($pagesLeft + $pagesRight);
		    }
		}

		if ( $pages < 2 ) {
			return '';
		}

		$html = '<div class="pages clearfix">';

        if ( $start > 1 ) {
            $html .= '<a class="page" href="'.$url.$pageParam.'=1#'.$pageParam.'s"> &#9668; </a>';
        }

        if ( $page > 1 ) {
           $html .= '<a class="page" href="'.$url.$pageParam.'='.($page - 1).'#'.$pageParam.'s"> &#9665; </a>';
       }

        for ( $i = $start; $i <= $end; $i++ ) {
            if ( $i == $page ) {
                $html .= '<div class="page active">
                    <form method="get" action="" data-pagination-form>
                        <input type="hidden" value="'.$url.'" />
                        <input type="text" class="input" value="'.$i.'" />
                    </form>
                </div>';
            }
			else {
                $html .= '<a class="page" href="'.$url.$pageParam.'='.$i.'#'.$pageParam.'s">'.$i.'</a>';
            }
        }

        if ( $page < $pages ) {
            $html .= '<a class="page" href="'.$url.$pageParam.'='.($page + 1).'#'.$pageParam.'s"> &#9655; </a>';
        }

        if ( $end < $pages ) {
            $html .= '<a class="page" href="'.$url.$pageParam.'='.$pages.'#'.$pageParam.'s"> &#9658; </a>';
        }

		$html .= '</div>';

		return $html;
	}

	public function getHtml() {
		return $this->html;
	}

}
