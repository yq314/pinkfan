<?php

// sitemap generator class
class SiteMap {

// constructor receives the list of URLs to include in the sitemap
    function Sitemap($items = array()) {
        $this->_items = $items;
    }

// add a new sitemap item
    function addItem($url, $lastmod = '', $changefreq = 'always', $priority = '0.8', $additional_fields = array()) {
        if(empty($lastmod)){
            $lastmod = time();
        }
        $this->_items[] = array_merge(array('loc' => $url,
            'lastmod' => $lastmod,
            'changefreq' => $changefreq,
            'priority' => $priority), $additional_fields);
    }

// get Google sitemap
    function getGoogle() {
        ob_start();
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        foreach ($this->_items as $i) {
            echo "\t<url>\n";
			echo "\t\t<loc>".$this->_escapeXML($i['loc'])."</loc>\n";
			echo "\t\t<lastmod>".date('Y-m-d\TH:i:s\Z', $i['lastmod'])."</lastmod>\n";
			echo "\t\t<changefreq>".$i['changefreq']."</changefreq>\n";
			echo "\t\t<priority>".$i['priority']."</priority>\n";
			echo "\t</url>\n";
        }
        echo '</urlset>';

        return ob_get_clean();
    }

// escape string characters for inclusion in XML structure
    function _escapeXML($str) {
        $translation = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
        foreach ($translation as $key => $value) {
            $translation[$key] = '&#' . ord($key) . ';';
        }
        $translation[chr(38)] = '&';
        return preg_replace("/&(?![A-Za-z]{0,4}w{2,3};|#[0-9]{2,3};)/","&#38;",
        strtr($str, $translation));
    }

}

?>