<?php
/**
 * CC_Utils.php
 *
 * Pomocna trida obsahujici ruzne pomocne metody.
 *
 * @copyright 2014
 * @version 1.0.0
 */


class CC_Utils
{
    
    /**
     * Vytvori URL na dany kontroler, akci nebo signal.
     * 
     * @param string $destination Cil odkazu ve formatu 'Module:Controller:action', nebo 'signal!'
     * @param array $params
     * @return string Vraci 
     */
    public static function link($destination, $params = array())
    {
        global $CC_SYSTEM;
        
        
        // Pokud neni params pole, vytvori se ze vsech parametru predanych do metody, mimo prvniho
        if (!is_array($params)) {
            $params = array_slice(func_get_args(), 1);
        }
        
        // 1) Fragment za '#'
        $p = strpos($destination, '#');
        if ($p === false) {
            $fragment = '';
        } else {
            $fragment = substr($destination, $p);
            $destination = substr($destination, 0, $p);
        }
        
        // 2) Parametry v query za '?'
        $p = strpos($destination, '?');
        if ($p !== false) {
            parse_str(substr($destination, $p + 1), $params);
            $destination = substr($destination, 0, $p);
        }
        
        // 3) Signal konci '!'
        if (substr($destination, -1) === '!') {
            $signal = rtrim($destination, '!');
            $destination = 'this';
        } else {
            $signal = '';
        }
        
        
        // 4) Controller, akce
        $p = strrpos($destination, ':');
        if ($p === false) {
            $action = $destination === 'this' ? $CC_SYSTEM->request->action : $destination;
            $controller = ($CC_SYSTEM->request->module) ? $CC_SYSTEM->request->module . ':' . $CC_SYSTEM->request->controller : $CC_SYSTEM->request->controller;
        } else {
            $action = (string) substr($destination, $p + 1);
            
            // Absolutni destinace
            if ($destination[0] === ':') {
                $controller = substr($destination, 1, $p - 1);
            } else {
                $controller = ($CC_SYSTEM->request->module) ? $CC_SYSTEM->request->module . ':' . $CC_SYSTEM->request->controller : $CC_SYSTEM->request->controller;
                $pp = strpos($controller, ':');
                if ($pp === false) {
                    // Modul neni zadan
                    $controller = substr($destination, 0, $p);
                } else {
                    // Vcetne modulu
                    $controller = substr($controller, 0, $pp + 1) . substr($destination, 0, $p);
                }
            }
        }
        
        
        // Sestavi URL
        $args = $params;
        $url = CC_PATH_ROOT;
                
        // Controller ?
        if (isset($controller) && !empty($controller)) {
            // Mame modul?
            $p = strpos($controller, ':');
            if ($p !== false) {
                $module = join('/', array_slice(explode(':', $controller), 0, -1));
                $url .=  '/' . strtolower($module);

                $controller = strtolower(array_pop(explode(':', $controller)));
            }
            
            $args['c'] = ($controller === 'this') ? strtolower($CC_SYSTEM->request->controller) : strtolower($controller);
        }
        
        // Akce ?
        if (isset($action) && !empty($action)) {
            $args['a'] = lcfirst($action);
        }
        
        // Signal ?
        if (isset($signal) && !empty($signal)) {
            $args['s'] = lcfirst($signal);
        }
        
        // Vytvori dvojice z parametru
        $tupples = array();
        foreach($args as $key => $value) {
            $tupples[] = $key .'='. $value;
        }
        
        // Slepi URL parametry a vlozi je za '?'
        $url .= '/?' . join('&amp;', $tupples);
        
        // Prida nakonec fragment pokud nejaky je
        if (isset($fragment) && !empty($fragment)) {
            $url .= $fragment; 
        }
        
        
        return $url;
    }
    
    
    /**
     * Funkce pro presmerovani na dany kontroler.
     * 
     * @param string $path Retezec ve formatu Module:Controller:Action nebo !signal.
     * @param array $params Seznam parametru.
     * @param int $state Stavovy kod http odpovedi, vychozi 302.
     */	 	   
    public static function redirect($path, $params = array(), $state = 302)
    {
        // Pokud jsou nastaveny nejake flash zpravicky, chceme je zobrazit
        // i na dalsi strance, k tomu je nutny je ulozit do session
        global $CC_TEMPLATE;
        CC_Utils::sessionSet('flash-messages', $CC_TEMPLATE->flashMessages);
        
        // Vytvori link a presmeruje na nej
        $url = CC_Utils::link($path, $params);
        CC_Utils::redirectUrl($url, $state);
    }
    
    
    /**
     * Metoda pro presmerovani na danou URL.
     * 
     * @param string $url URL adresa na kterou se presmeruje
     * @param @param int $state Stavovy kod http odpovedi, vychozi 302.
     */
    public static function redirectUrl($url, $state = 302)
    {
        $stateType = "";
        switch($state) {
            case 301:
                $stateType = "Moved Permanently";
                break;
            case 302:
                $stateType = "Moved Temporarily";
                break;
        }

        header("HTTP/1.1 " . $state . " " . $stateType);
        header("Status: " . $state . " " . $stateType);
        header("Location: " . preg_replace("~&amp;~", "&", $url));
        header("Connection: Close");
        die;
    }
    
    
    /**
     * Testuje, zda aktualni URL odpovida destinaci.
     * 
     * @param string $destination
     * @param array $params
     */
    public static function linkCurrent($destination)
    {
        global $CC_SYSTEM;
        
        $current = CC_Utils::link($CC_SYSTEM->request->module . ':' . $CC_SYSTEM->request->controller . ':' . $CC_SYSTEM->request->action);
        $wanted = CC_Utils::link($destination);
        //dump($wanted);
        return $current == $wanted;        
    }
    
    
    /**
     * Nastavi session na pozadovanou hodnotu.
     * 
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public static function sessionSet($key, $value) 
    {
        if (!isset($_SESSION['ccccar'])) {
            $_SESSION['ccccar'] = array();
        }
        
        // Pokud je hotnota NULL, session se zrusi jinak se nastavi
        if ($value === null) {
            unset($_SESSION['ccccar'][$key]);
        } else {
            $_SESSION['ccccar'][$key] = $value;
        }
        
        return true;
    }
    
    
    /**
     * Ziska session podle klice. Pokud neexistuje vraci NULL.
     * 
     * @param string $key
     * @return mixed|NULL
     */
    public static function sessionGet($key) 
    {
        if (CC_Utils::sessionExists($key)) {
            return $_SESSION['ccccar'][$key];
        }
        
        return null;
    }
    
    
    /**
     * Otestuje, zda dana session skutecne existuje.
     * 
     * @param string $key
     * @return boolean
     */
    public static function sessionExists($key) 
    {
        return isset($_SESSION['ccccar'][$key]);
    }
    
    
    /**
     * Vytiskne vsechny sessions.
     */
    public static function sessionDump() 
    {
        if (isset($_SESSION['ccccar'])) {
            dump($_SESSION['ccccar']);
        }
        
        return true;
    }
}