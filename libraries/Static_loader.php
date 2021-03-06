<?php
if ( ! defined("BASEPATH"))
{
    exit("No direct script access allowed");
}
/**
 * Read the static config to generate inline YUI config.
 *
 *    $this->load->library("static_loader");
 *    $this->static_module->set("common/_masthead", "home/_notification");
 *    $data["loader_html"] = $this->static_module->load();
 *
 * @class Static_loader
 */
class Static_loader
{

    public $static_config;
    public $yui_config;
    public $use_css_files;
    public $use_modules;

    public function __construct()
    {
        $this->config =& load_class("Config");
        $this->CI =& get_instance();
    }

    /**
     * Get YUI JavaScript module config.
     *
     * @param $module {Array} The static module configuration.
     * @return {Array} The YUI JavaScript module config.
     */
    private function _get_js_config($module)
    {

        if ( ! isset($module["js"])) {
            return FALSE;
        }

        // Move 'js' attribute to 'path'
        // to keep align with YUI config.
        $data = array();
        if (isset($module["js"]))
        {
            $data["path"] = $module["js"];
            unset($module["js"]);
        }

        // List the attributes which should be attached.
        $allows = array("lang", "async", "requires");
        foreach ($module as $key => $value)
        {
            if (in_array($key, $allows))
            {
                $data[$key] = $value;
            }
        }
        $data["type"] = "js";
        return $data;
    }

    /**
     * Get the loader HTML.
     *
     *     echo $this->static_loader->load();
     *
     * @return {String} The loader HTML code.
     */
    public function load()
    {
        $html          = array();
        $config        = $this->yui_config;
        $seed_config   = $this->static_config["seed"];
        $use_css_files = $this->use_css_files;
        $modules       = implode("\",\"", $this->use_modules);

        // Prepare for link tag.
        if (isset($seed_config["css"]))
        {
            if (count($use_css_files))
            {
                $connector = (strpos($seed_config["css"], "?") === FALSE) ? "?" : "&";
                $tpl_link  = '<link rel="stylesheet" href="' . $seed_config["css"]. $connector . 'f=%s">';
                $html[] = sprintf($tpl_link, implode(",", $use_css_files));
            }
            else
            {
                $html[] = '<link rel="stylesheet" href="' . $seed_config["css"] . '">';
            }
        }

        // Prepare for script tag.
        $tpl_script = array('<script type="text/javascript" src="' . $seed_config["js"] . '"></script>');
        if (isset($config["jsCallback"]))
        {
            $js_callback = $config["jsCallback"];
            // Hack for language setting.
            $lang = "";
            if (isset($config["lang"]))
            {
                $lang = "YUI_config.lang = " . $config["lang"] . ";\n";
                unset($config["lang"]);
            }
            $tpl_script[] = '<script type="text/javascript">' . "\n" .
                            "YUI_config = %s;\n" .
                            $lang .
                            'YUI().use("' . $modules . '", function (Y) {' . $js_callback . '});' . "\n" .
                            "</script>\n";
            unset($config["jsCallback"]);
        }
        else
        {
            $tpl_script[] = '<script type="text/javascript">' .
                            'YUI(%s).use("' . $modules . '");' .
                            '</script>';
        }
        $tpl_script = implode("\n", $tpl_script);

        $html[] = sprintf($tpl_script, json_encode($config));
        return implode("\n", $html);
    }

    /**
     * Set modules you want use.
     *
     *    $this->static_module->set("common/_masthead", "home/_notification");
     *
     * @method set
     * @param $use_modules {Array} The use module list.
     * @public
     */
    public function set($use_modules)
    {
        if (gettype(func_get_arg(0)) === "string")
        {
            $use_modules = func_get_args();
        }

        // Load configuration file - config/static.php.
        $this->config->load("static", TRUE);
        $static_config = $this->config->item("static");
        $this->static_config = $static_config;

        // Make groups config.
        $groups = array();
        foreach ($static_config["groups"] as $k => $v)
        {
            $groups[$k] = array(
                "combine"  => $v["combine"],
                "fetchCSS" => !($v["serverComboCSS"]),
                "modules"  => array(),
            );
            if (isset($v["filter"]))
            {
                $groups[$k]["filter"] = $v["filter"];
            }
            if (isset($v["root"]))
            {
                $groups[$k]["root"] = $v["root"];
            }
            if (isset($v["base"]))
            {
                $groups[$k]["base"] = $v["base"];
            }
        }

        // The CSS files which needs to be combined.
        $use_css_files = array();

        // Loop all config modules.
        $config_modules = $static_config["modules"];
        foreach ($config_modules as $k => $v)
        {
            $group_name = $v["group"];
            $group      = $groups[$group_name];

            // Attach JavaScript modules.
            if (isset($v["js"]))
            {
                $groups[$group_name]["modules"][$k] = $this->_get_js_config($v);
            }

            // Check if any server-combo css modules exist in require list.
            if (in_array($k, $use_modules) && isset($v["requires"]))
            {
                foreach ($v["requires"] as $x)
                {
                    // If it's not defined (might be YUI native module),
                    // just ignore it.
                    if ( ! isset($config_modules[$x]))
                    {
                        continue;
                    }
                    $y = $config_modules[$x]; // Current module.
                    $z = $groups[$y["group"]]["root"]; // Current group path.
                    if (isset($y["css"]) && ! $groups[$y["group"]]["fetchCSS"])
                    {
                        $use_css_files[] = $z . $y["css"];
                    }
                }
            }

            // Break to next iteration if no css attribute exists.
            if ( ! isset($v["css"]))
            {
                continue;
            }

            // Check if this module's belonging group
            // uses CSS server combo.
            $server_combo = !($group["fetchCSS"]);
            if ($server_combo)
            {
                // Add server combo CSS files.
                if (in_array($k, $use_modules))
                {
                    $use_css_files[] = $group["root"] . $v["css"];
                }

                // Remove this module from static setting or
                // it causes dynamically loading CSS file.
                if ( ! isset($v["js"]) && in_array($k, $use_modules))
                {
                    $offset = array_search($k, $use_modules);
                    unset($use_modules[$offset]);
                    /*
                    // It might have requires setting.
                    if (isset($v["requires"]))
                    {
                        foreach ($v["requires"] as $x)
                        {
                            if ( ! in_array($x, $use_modules))
                            {
                                $use_modules[] = $x;
                            }
                        }
                    }
                    */
                }
            }
            else
            {
                // Avoid use same module name
                if (isset($v["js"]))
                {
                    $groups[$group_name]["modules"][$k]["requires"][] = "$k-css";
                    $k = "$k-css";
                }
                $groups[$group_name]["modules"][$k] = array(
                    "path" => $v["css"],
                    "type" => "css",
                );
                if (isset($v["requires"]))
                {
                    $groups[$group_name]["modules"][$k]["requires"] = $v["requires"];
                }
            }
        }

        $this->use_modules = $use_modules;
        $this->use_css_files = array_unique($use_css_files);
        $static_config["base"]["groups"]  = $groups;
        $this->yui_config = $static_config["base"];
    }

}
/* End of file Static_loader.php */
?>
