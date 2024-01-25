<?php
class Bookmark {
    public $data;

    public function get_data($source, $substitute = array()) {
        $hostname = parse_url($source, PHP_URL_HOST);
        $hostname = preg_replace("~^www\.~", "", $hostname);

        $substitute = array_filter($substitute);
        $site = strtok($hostname, '.');
        $className = "Bookmark_" . ucfirst($site);
        
        // Check if the class exists, otherwise use the default class
        if (class_exists($className)) {
            $class = new $className();
            $methodName = "get_data_" . $site;
            $data = array_replace($class->$methodName($source), $substitute);
        } else {
            // Fallback to default class and method
            $defaultClass = new Bookmark_Default();
            $data = array_replace($defaultClass->get_data_default($source), $substitute);
        }

        return json_decode(json_encode(array_filter($data)));
    }

    private function get_file_site($file) {
        $docComments = array_filter(
            token_get_all(file_get_contents($file)),
            function ($entry) {
                return $entry[0] == T_DOC_COMMENT;
            }
        );
        $fileDocComment = array_shift($docComments);
        preg_match_all('#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si', $fileDocComment[1], $matches);
        return trim($matches[0][0]);
    }

    private function get_all_sites() {
        $sites = array();

        foreach (glob(plugin_dir_path(__FILE__) . 'inc/sites/*.php') as $site) {
            $sites[] = $this->get_file_site($site);
        }

        return json_decode(json_encode($sites));
    }
}
