<?php
/**
 * Use this configuration file to make relationship
 * between modules and static (CSS/JavaScript) files.
 * You must also specify module dependencies here.
 *
 * The static_loader library will transform this configuration file
 * to an YUI config.
 */
$config = array();
//=================
// Seed
//=================
/**
 * The external CSS/JS seed URLs.
 *
 * For CSS, you must provide your PHP Minify URL.
 * For JavaScript, you need to define your YUI seed URL.
 */
$config["seed"] = array(
    "css"         => STATIC_URL . "combo/?g=css",
    "js"          => STATIC_URL . "combo/?g=js",
);
//=================
// Base
//=================
/**
 * The basis configuration for YUI.
 */
$config["base"] = array(
    "filter"     => "raw",
    "combine"    => TRUE,
    "comboBase"  => STATIC_URL . "combo/?f=",
    "comboSep"   => ",",
    // Uncomment if you want to observe log for module(s).
    // Restore it when you need to commit.
    "logInclude" => array(
                        //"Y.Channel" => TRUE,
                        //"Y.Module" => TRUE,
                        //"Y.ModuleManager" => TRUE,
                        //"Y.VLC" => TRUE,
                        //"Y.VideoParser" => TRUE,
                        //"Y.MPlayer " => TRUE,
                        //"#channel-playlist" => TRUE,
                        //"#channel-player" => TRUE,
                        //"#channel-support-button" => TRUE,
                        //"#channel-welcome" => TRUE,
                        //"#channel-info" => TRUE,
                    ),
    "logExclude" => array(),
    "root"       => "lib/yui/3.5.1/",
    "langs"      => "en-US,zh-TW",
    "jsCallback" => "function (Y) {(new Y.ModuleManager()).startAll();}",
);
//=================
// Groups
//=================
/**
 * Before setting this, you should understand the group attribute in YUI config.
 * Ref: http://yuilibrary.com/yui/docs/api/classes/config.html#property_groups
 *
 * NOTE - We add a magic 'serverComboCSS' attribute.
 *        Set it to true if you want all belonging CSS files
 *        being combined and loaded with traditional link tag approach,
 *        instead of using dynamic scripting.
 */
$config["groups"] = array(

    // For miiiCasa customized libraries.
    "yui-ext" => array(
        "combine"        => TRUE,
        "serverComboCSS" => FALSE, // Load CSS on-the-fly.
        "root"           => "lib/yui/extension/",
        "lang"           => array("en-US", "zh-TW"),
    ),

    // For miiiCasa customized libraries.
    "mui" => array(
        "combine"        => TRUE,
        "serverComboCSS" => FALSE, // Load CSS on-the-fly.
        "root"           => "lib/mui/",
        "lang"           => array("en-US", "zh-TW"),
    ),

    // For miiiCasa index application.
    "tv/channel" => array(
        "combine"        => TRUE,
        "serverComboCSS" => TRUE, // Load CSS using <link>
        "root"           => "apps/tv/channel/",
        "lang"           => array("en-US", "zh-TW"),
    ),

);

//=================
// Modules
//=================
/**
 * Individual module setting.
 * You should specify its belonging group, related CSS & JS files,
 * and dependent modules.
 */
$config["modules"] = array(
    //=================
    // YUI Ext Modules
    //=================
    "yui-object-ext" => array(
        "group"    => "yui-ext",
        "js"       => "yui-object-ext.js",
        "requires" => array(
            "yui-base",
        ),
    ),
    //=================
    // MUI Modules
    //=================
    "module" => array(
        "group"    => "mui",
        "js"       => "module/module.js",
        "requires" => array(
            "base", "node-base", "event-base",
            "module-manager",
        ),
    ),
    "module-manager" => array(
        "group"    => "mui",
        "js"       => "module/module-manager.js",
        "requires" => array(
            "base",
        ),
    ),
    "module-intl" => array(
        "group"    => "mui",
        "js"       => "module/module-intl.js",
        "requires" => array(
            "base-build", "module", "intl", "substitute",
        ),
    ),
    "mplayer" => array(
        "group"    => "mui",
        "js"       => "mplayer/mplayer.js",
        "requires" => array(
            "base", "node-base", "substitute",
        ),
    ),
    "vlc" => array(
        "group"    => "mui",
        "js"       => "vlc/vlc.js",
        "requires" => array(
            "base", "node", "substitute",
        ),
    ),
    //===========================
    // DIV Modules (welcome)
    //===========================
    "tv/channel/channel" => array(
        "group"    => "tv/channel",
        "js"       => "channel.js",
        "css"      => "channel_base.css",
        "lang"     => array("en-US", "zh-TW"),
        "requires" => array(
            "node-base", "yui-object-ext", "module-manager",
        ),
    ),
    "tv/channel/video-parser" => array(
        "group"    => "tv/channel",
        "js"       => "video-parser.js",
        "requires" => array(
            "querystring-parse", "jsonp", "event-custom",
        ),
    ),
    "tv/channel/_channel_info" => array(
        "group"    => "tv/channel",
        "js"       => "_channel_info.js",
        "css"      => "_channel_info.css",
        "requires" => array(
            "escape", "node-base", "node-style",
        ),
    ),
    "tv/channel/_channel_loading" => array(
        "group"    => "tv/channel",
        "js"       => "_channel_loading.js",
        "css"      => "_channel_loading.css",
        "requires" => array(
            "module", "escape", "event-key",
            "tv/channel/channel", "node-base",
        ),
    ),
    "tv/channel/_channel_player" => array(
        "group"    => "tv/channel",
        "js"       => "_channel_player.js",
        "css"      => "_channel_player.css",
        "requires" => array(
            "tv/channel/channel", "tv/channel/video-parser", "vlc",
            "mplayer", "module", "event-key",
        ),
    ),
    "tv/channel/_channel_playlist" => array(
        "group"    => "tv/channel",
        "js"       => "_channel_playlist.js",
        "css"      => "_channel_playlist.css",
        "requires" => array(
            "module", "event-key", "handlebars",
            "node-base", "escape", "tv/channel/channel",
        ),
    ),
    "tv/channel/_channel_support_button" => array(
        "group"    => "tv/channel",
        "js"       => "_channel_support_button.js",
        "css"      => "_channel_support_button.css",
        "requires" => array(
            "module", "event-key", "tv/channel/channel",
        ),
    ),
    "tv/channel/_channel_welcome" => array(
        "group"    => "tv/channel",
        "js"       => "_channel_welcome.js",
        "css"      => "_channel_welcome.css",
        "requires" => array(
            "module", "tv/channel/channel",
        ),
    ),
);

?>
